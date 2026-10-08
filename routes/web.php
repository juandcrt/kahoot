<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\SalaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SalaJuego;
use App\Models\RespuestaEstudiante;
use App\Models\Opcion;
use App\Models\Pregunta;
use App\Events\AlumnoUnido;
use App\Events\AlumnoSalio;
use App\Events\PartidaIniciada;
use App\Events\RespuestaEnviada;
use App\Models\StudentFeedback;
use App\Jobs\GenerateAiFeedback;
use App\Events\PartidaFinalizada;

// Función global para calcular el Bimestre dinámicamente según el calendario de Tungasuca
function obtenerBimestreActual() {
    $fecha = \Carbon\Carbon::now();
    $mes = $fecha->month;
    $dia = $fecha->day;

    if (($mes == 3) || $mes == 4 || ($mes == 5 && $dia <= 12)) return 'I Bimestre';
    if (($mes == 5 && $dia >= 13) || $mes == 6 || ($mes == 7 && $dia <= 21)) return 'II Bimestre';
    if (($mes == 7 && $dia >= 22) || ($mes == 8 && $dia <= 4)) return 'Vacaciones';
    if (($mes == 8 && $dia >= 5) || $mes == 9 || ($mes == 10 && $dia <= 13)) return 'III Bimestre';
    if (($mes == 10 && $dia >= 14) || $mes == 11 || $mes == 12) return 'IV Bimestre';
    
    return 'Fuera de periodo';
}

// 1. Ruta principal: Selector de roles
Route::get('/', function () {
    return view('selector.opcion');
});

// 2. Dashboard Profesor y sus módulos de gestión
Route::middleware(['auth', 'verified'])->prefix('dashboard/profesor')->group(function () {
    
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }
        return view('Interfaz_profesor.profesor');
    })->name('dashboard.profesor');

    Route::get('/cuestionario', [CuestionarioController::class, 'create'])->name('cuestionario.crear');
    Route::post('/cuestionario', [CuestionarioController::class, 'store'])->name('cuestionario.store');
    
    Route::get('/cuestionario/{id}/editar', [CuestionarioController::class, 'edit'])->name('profesor.cuestionario.edit');
    Route::put('/cuestionario/{id}', [CuestionarioController::class, 'update'])->name('profesor.cuestionario.update');

    Route::get('/salas', function () {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }
        $salas = SalaJuego::with(['cuestionario.user'])
            ->whereHas('cuestionario', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->latest()->get();
        return view('cuestionario.salas', compact('salas'));
    })->name('profesor.salas');

    Route::get('/proyectar/{id}', function ($id) {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }
        $sala = SalaJuego::with(['cuestionario.preguntas', 'usuarios'])->findOrFail($id);
        return view('proyectar.proyectar_sala', compact('sala'));
    })->name('profesor.proyectar');

    Route::post('/sala/{id}/iniciar', function ($id) {
        $sala = SalaJuego::findOrFail($id);
        $sala->estado = 'en_curso';
        $sala->save();
        broadcast(new PartidaIniciada($sala->pin));
        return redirect()->route('profesor.proyectar', $id);
    })->name('profesor.iniciar');

    // RUTA DEL PODIO CON TODAS LAS RESPUESTAS PARA EL EXCEL Y BIMESTRE
    Route::get('/sala/{id}/podio', function ($id) {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }
        $sala = SalaJuego::with('cuestionario.preguntas')->findOrFail($id);
        
        if ($sala->estado !== 'finalizada') {
            $sala->estado = 'finalizada';
            $sala->save();
            broadcast(new PartidaFinalizada($sala->pin));
        }

        $userIds = RespuestaEstudiante::where('sala_juego_id', $sala->id)->distinct()->pluck('user_id');
        $alumnosParticipantes = \App\Models\User::whereIn('id', $userIds)->get();

        // Obtenemos todas las respuestas de la sala para cruzar en la vista del podio (Excel)
        $todasLasRespuestas = RespuestaEstudiante::where('sala_juego_id', $sala->id)->get();
        
        // Calculamos el bimestre actual de forma automática
        $bimestreActual = obtenerBimestreActual();

        $resultados = $alumnosParticipantes->map(function ($alumno) use ($sala, $todasLasRespuestas, $bimestreActual) {
            $respuestas = $todasLasRespuestas->where('user_id', $alumno->id);

            $puntajeTotal = 0; $correctas = 0; $tiempoTotal = 0;
            foreach ($respuestas as $respuesta) {
                $tiempoReal = abs($respuesta->tiempo_respuesta_segundos);
                if ($respuesta->es_correcta) {
                    $correctas++;
                    $penalizacion = $tiempoReal * 15; 
                    $puntos = max(500, 1000 - $penalizacion);
                    $puntajeTotal += $puntos;
                }
                $tiempoTotal += $tiempoReal;
            }

            $minutos = floor($tiempoTotal / 60);
            $segundos = round($tiempoTotal - ($minutos * 60));
            $textoTiempo = $minutos > 0 ? "{$minutos} min {$segundos} s" : "{$segundos} s";
            
            $porcentaje = $sala->cuestionario->preguntas->count() > 0 
                ? round(($correctas / $sala->cuestionario->preguntas->count()) * 100) 
                : 0;

            return [
                'user_id' => $alumno->id,
                'nombre' => $alumno->name ?? 'N/A',
                'apellidos' => $alumno->apellidos ?? '-',
                'dni' => $alumno->dni ?? '-',
                'grado_seccion' => $alumno->grado_seccion ?? '-',
                'bimestre' => $bimestreActual,
                'puntaje' => $puntajeTotal,
                'correctas' => $correctas,
                'porcentaje' => $porcentaje,
                'tiempo_formateado' => $textoTiempo
            ];
        })->sortByDesc('puntaje')->values();

        return view('proyectar.podio', compact('sala', 'resultados', 'todasLasRespuestas'));
    })->name('profesor.podio');

    Route::get('/sala/{id}/finalizar', function ($id) {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }
        $sala = SalaJuego::with('cuestionario')->findOrFail($id);
        if ($sala->estado !== 'finalizada') {
            $sala->estado = 'finalizada';
            $sala->save();
            broadcast(new PartidaFinalizada($sala->pin));
        }
        return redirect()->route('profesor.podio', $id);
    })->name('profesor.finalizar');

    Route::get('/sala/{id}/dashboard', function ($id) {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }
        $sala = SalaJuego::with(['cuestionario.preguntas.opciones'])->findOrFail($id);
        
        $respuestas = RespuestaEstudiante::with('user')->where('sala_juego_id', $sala->id)->get();
        
        $userIds = RespuestaEstudiante::where('sala_juego_id', $sala->id)->distinct()->pluck('user_id');
        $totalAlumnosCount = $userIds->count();

        $preguntas = $sala->cuestionario->preguntas->map(function ($p, $i) use ($respuestas) {
            $rs = $respuestas->where('pregunta_id', $p->id);
            $total = $rs->count();
            $aciertos = $rs->where('es_correcta', true)->count();
            return [
                'numero' => $i + 1,
                'texto' => $p->pregunta,
                'total' => $total,
                'aciertos' => $aciertos,
                'porcentaje' => $total > 0 ? round($aciertos * 100 / $total) : 0,
                'detalle' => $rs->map(function ($r) use ($p) {
                    $op = $p->opciones->firstWhere('id', $r->opcion_id);
                    return [
                        'nombre' => $r->user->name ?? $r->user->nickname ?? 'Estudiante',
                        'correcta' => (bool) $r->es_correcta,
                        'marco' => $op->opcion ?? '—',
                        'tiempo' => round(abs($r->tiempo_respuesta_segundos), 1),
                    ];
                })->sortBy('tiempo')->values(),
                'opciones' => $p->opciones->map(fn($o) => [
                    'texto' => $o->opcion,
                    'correcta' => (bool) $o->es_correcta,
                    'votos' => $rs->where('opcion_id', $o->id)->count(),
                ])->values(),
            ];
        })->values();

        $conDatos = $preguntas->where('total', '>', 0);
        $mejor = $conDatos->sortByDesc('porcentaje')->first();
        $peor = $conDatos->sortBy('porcentaje')->first();

        $resumen = [
            'alumnos' => $totalAlumnosCount,
            'acierto_global' => $respuestas->count() > 0
                ? round($respuestas->where('es_correcta', true)->count() * 100 / $respuestas->count()) : 0,
            'tiempo_promedio' => round($respuestas->avg('tiempo_respuesta_segundos') ?? 0, 1),
        ];

        return view('proyectar.dashboard', compact('sala', 'preguntas', 'mejor', 'peor', 'resumen'));
    })->name('profesor.dashboard');

    Route::delete('/salas/{id}', [SalaController::class, 'destroy'])->name('salas.destruir');

    // =========================================================
    // MÓDULO DE GESTIÓN DE USUARIOS (NUEVO)
    // =========================================================
    Route::get('/usuarios', function () {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'docente') {
            abort(403);
        }
        $usuarios = \App\Models\User::latest()->get();
        $bimestreActual = obtenerBimestreActual();
        return view('Interfaz_profesor.usuarios', compact('usuarios', 'bimestreActual'));
    })->name('profesor.usuarios');

    Route::post('/usuarios/crear', function (Request $request) {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'docente') {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dni' => 'required|string|max:15|unique:users,dni',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:estudiante,docente,admin',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'apellidos' => $request->apellidos,
            'dni' => $request->dni,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'grado_seccion' => $request->grado_seccion ?? null,
            // El bimestre no se guarda en BD, se calcula en vivo siempre
        ]);

        return back()->with('success', 'Usuario registrado correctamente.');
    })->name('profesor.usuarios.crear');

    Route::delete('/usuarios/eliminar/{id}', function ($id) {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'docente') {
            abort(403);
        }
        \App\Models\User::findOrFail($id)->delete();
        return back()->with('success', 'Usuario eliminado exitosamente.');
    })->name('profesor.usuarios.eliminar');

});

// 3. Dashboard Estudiante y Rutas de Juego
Route::middleware(['auth', 'verified'])->prefix('dashboard/estudiante')->group(function () {
    
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->role === 'docente' || $user->role === 'admin') {
            return redirect('/dashboard/profesor');
        }
        return view('Interfaz_estudiante.estudiante');
    })->name('dashboard.estudiante');

    Route::get('/pin', function () { return view('PIN.index'); })->name('estudiante.pin');
    Route::get('/skins', function () { return view('skins.index'); })->name('estudiante.skins');

    Route::get('/juego/{pin}', function ($pin) {
        $sala = SalaJuego::with(['cuestionario.preguntas.opciones'])->where('pin', $pin)->firstOrFail();
        $preguntas = ($sala->cuestionario && $sala->cuestionario->preguntas) ? $sala->cuestionario->preguntas : collect();
        return view('Interfaz_estudiante.juego', compact('sala', 'preguntas'));
    })->name('estudiante.juego');

    Route::post('/pregunta/iniciar', function (Request $request) {
        $cacheKey = "sala_{$request->sala_id}_user_" . Auth::id() . "_pregunta_{$request->pregunta_id}";
        Cache::put($cacheKey, now(), now()->addMinutes(10));
        return response()->json(['status' => 'started']);
    })->name('estudiante.pregunta.iniciar');

    Route::post('/responder', function (Request $request) {
        $request->validate([
            'pin' => 'required',
            'pregunta_id' => 'required|exists:preguntas,id',
            'opcion_id' => 'required|exists:opcions,id',
        ]);

        $pin = $request->pin;
        $sala = SalaJuego::where('pin', $pin)->firstOrFail();
        $userId = Auth::id();
        $pregunta = Pregunta::findOrFail($request->pregunta_id);
        
        $opcion = Opcion::findOrFail($request->opcion_id);
        $esCorrecta = $opcion->es_correcta;

        $cacheKey = "sala_{$sala->id}_user_{$userId}_pregunta_{$pregunta->id}";
        $inicio = Cache::get($cacheKey) ?? now()->subSeconds($pregunta->tiempo ?? 30);
        
        $inicioCarbon = \Carbon\Carbon::parse($inicio);
        $tiempoMs = abs(now()->diffInMilliseconds($inicioCarbon));
        $tiempoSegundos = $tiempoMs / 1000;

        $puntos = 0;
        if ($esCorrecta) {
            $penalizacion = $tiempoSegundos * 15; 
            $puntos = max(500, 1000 - $penalizacion);
        }

        RespuestaEstudiante::updateOrCreate(
            [
                'sala_juego_id' => $sala->id,
                'user_id' => $userId,
                'pregunta_id' => $request->pregunta_id,
            ],
            [
                'opcion_id' => $request->opcion_id,
                'es_correcta' => $esCorrecta,
                'tiempo_respuesta_segundos' => $tiempoSegundos,
            ]
        );

        event(new RespuestaEnviada($pin, $userId, $request->pregunta_id, $puntos, $tiempoMs, $esCorrecta));

        return response()->json([
            'status' => 'success',
            'es_correcta' => $esCorrecta,
            'puntos' => $puntos,
            'mensaje' => $esCorrecta ? '¡Respuesta correcta!' : 'Respuesta incorrecta'
        ]);
    })->name('estudiante.responder');

    Route::get('/resultados/{sala_id}', function ($sala_id) {
        $user = Auth::user();
        $sala = SalaJuego::with('cuestionario.preguntas')->findOrFail($sala_id);
        
        $respuestas = RespuestaEstudiante::where('sala_juego_id', $sala->id)
            ->where('user_id', $user->id)
            ->get();

        $puntajeTotal = 0;
        $correctas = 0;
        $incorrectas = 0;
        $datosParaIA = [];
        $todasLasPreguntas = $sala->cuestionario->preguntas;

        foreach ($respuestas as $res) {
            $pregAsociada = $todasLasPreguntas->firstWhere('id', $res->pregunta_id);
            $res->texto_pregunta = $pregAsociada ? $pregAsociada->pregunta : 'Pregunta eliminada';

            if ($res->es_correcta) {
                $correctas++;
                $tiempoReal = abs($res->tiempo_respuesta_segundos);
                $penalizacion = $tiempoReal * 15;
                $puntajeTotal += max(500, 1000 - $penalizacion);
            } else {
                $incorrectas++;
            }
            
            $datosParaIA[] = [
                'pregunta_id' => $res->pregunta_id,
                'correcta' => (bool)$res->es_correcta,
                'tiempo_usado_segundos' => abs($res->tiempo_respuesta_segundos)
            ];
        }

        $feedback = StudentFeedback::firstOrCreate(
            ['sala_juego_id' => $sala->id, 'user_id' => $user->id],
            ['feedback_text' => 'Generando...', 'status' => 'pending']
        );

        if ($feedback->wasRecentlyCreated && $feedback->status === 'pending') {
            GenerateAiFeedback::dispatch($feedback->id, $datosParaIA);
        }

        return view('Interfaz_estudiante.resultados', compact('sala', 'puntajeTotal', 'correctas', 'incorrectas', 'feedback', 'respuestas'));
    })->name('estudiante.resultados');
});

Route::match(['get', 'post'], '/estudiante/unirse', function (Request $request) {
    $pin = $request->input('pin');
    if (!$pin) return redirect()->route('estudiante.pin');

    $sala = SalaJuego::where('pin', $pin)->whereIn('estado', ['activa', 'en_curso'])->first();
    if (!$sala) return redirect()->route('estudiante.pin')->withErrors(['pin' => 'El PIN ingresado no es válido o la sala está cerrada.']);

    $user = Auth::user();
    $sala->usuarios()->syncWithoutDetaching([$user->id]);
    $nombreAlumno = $user->name ?? $user->nickname ?? 'Estudiante';
    broadcast(new AlumnoUnido($nombreAlumno, $pin, $user->id));

    return view('Interfaz_estudiante.sala_activa', compact('sala'));
})->middleware(['auth', 'verified'])->name('estudiante.unirse');

Route::post('/estudiante/salir', function (Request $request) {
    $pin = $request->input('pin');
    $sala = SalaJuego::where('pin', $pin)->first();
    if ($sala) {
        $user = Auth::user();
        $nombreAlumno = $user->name ?? $user->nickname ?? 'Estudiante';
        $sala->usuarios()->detach($user->id);
        broadcast(new AlumnoSalio($nombreAlumno, $pin));
    }
    return redirect()->route('dashboard.estudiante');
})->middleware(['auth', 'verified'])->name('estudiante.salir');

Route::get('/api/sala/{id}/participantes', function ($id) {
    $sala = SalaJuego::with('usuarios')->findOrFail($id);
    return response()->json(['total' => $sala->usuarios->count()]);
})->middleware(['auth', 'verified']);

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'docente' || $user->role === 'admin') {
        return redirect()->route('dashboard.profesor');
    }
    return redirect()->route('dashboard.estudiante');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';