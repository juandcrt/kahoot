<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\SalaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalaJuego;
use App\Models\RespuestaEstudiante;
use App\Models\Opcion;
use App\Events\AlumnoUnido;
use App\Events\AlumnoSalio;
use App\Events\PartidaIniciada;
use App\Events\RespuestaEnviadaEvento;

// 1. Ruta principal: Selector de roles
Route::get('/', function () {
    return view('selector.opcion');
});

// 2. Dashboard Profesor y sus módulos de gestión
Route::middleware(['auth', 'verified'])->prefix('dashboard/profesor')->group(function () {
    
    // Panel principal de profesor
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }
        return view('Interfaz_profesor.profesor');
    })->name('dashboard.profesor');

    // Módulo de Cuestionarios (Crear, Guardar y Editar)
    Route::get('/cuestionario', [CuestionarioController::class, 'create'])->name('cuestionario.crear');
    Route::post('/cuestionario', [CuestionarioController::class, 'store'])->name('cuestionario.store');
    
    // Rutas para Editar y Actualizar Cuestionario
    Route::get('/cuestionario/{id}/editar', [CuestionarioController::class, 'edit'])->name('profesor.cuestionario.edit');
    Route::put('/cuestionario/{id}', [CuestionarioController::class, 'update'])->name('profesor.cuestionario.update');

    // Módulo de Gestión de Salas y PINes Activos
    Route::get('/salas', function () {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }

        $salas = SalaJuego::with(['cuestionario.user'])
            ->whereHas('cuestionario', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();

        return view('cuestionario.salas', compact('salas'));
    })->name('profesor.salas');

    // Módulo para Proyectar Sala en tiempo real
    Route::get('/proyectar/{id}', function ($id) {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }

        $sala = SalaJuego::with(['cuestionario.preguntas', 'usuarios'])->findOrFail($id);

        return view('proyectar.proyectar_sala', compact('sala'));
    })->name('profesor.proyectar');

    // RUTA PARA INICIAR EL JUEGO
    Route::post('/sala/{id}/iniciar', function ($id) {
        $sala = SalaJuego::findOrFail($id);
        
        $sala->estado = 'en_curso';
        $sala->save();

        broadcast(new PartidaIniciada($sala->pin));

        return redirect()->route('profesor.proyectar', $id);
    })->name('profesor.iniciar');

    // RUTA PARA FINALIZAR EL JUEGO Y MOSTRAR EL PODIO
    Route::get('/sala/{id}/podio', function ($id) {
        $user = Auth::user();
        if ($user->role !== 'docente' && $user->role !== 'admin') {
            return redirect('/dashboard/estudiante');
        }

        $sala = SalaJuego::with('usuarios', 'cuestionario.preguntas')->findOrFail($id);
        
        if ($sala->estado !== 'finalizada') {
            $sala->estado = 'finalizada';
            $sala->save();
        }

        $resultados = $sala->usuarios->map(function ($alumno) use ($sala) {
            $respuestas = RespuestaEstudiante::where('sala_id', $sala->id)
                ->where('user_id', $alumno->id)
                ->get();

            $puntajeTotal = 0;
            $correctas = 0;
            $tiempoTotal = 0;

            foreach ($respuestas as $respuesta) {
                if ($respuesta->es_correcta) {
                    $correctas++;
                    $penalizacion = $respuesta->tiempo_respuesta_segundos * 15; 
                    $puntos = max(500, 1000 - $penalizacion);
                    $puntajeTotal += $puntos;
                }
                $tiempoTotal += $respuesta->tiempo_respuesta_segundos;
            }

            return [
                'nombre' => $alumno->name ?? $alumno->nickname ?? 'Estudiante',
                'puntaje' => $puntajeTotal,
                'correctas' => $correctas,
                'tiempo_promedio' => $respuestas->count() > 0 ? round($tiempoTotal / $respuestas->count(), 1) : 0
            ];
        })->sortByDesc('puntaje')->values();

        return view('proyectar.podio', compact('sala', 'resultados'));
    })->name('profesor.podio');

    // RUTA PARA ELIMINAR SALA
    Route::delete('/salas/{id}', [SalaController::class, 'destroy'])->name('salas.destruir');
});

// 3. Dashboard Estudiante, Acceso a Salas por PIN
Route::middleware(['auth', 'verified'])->prefix('dashboard/estudiante')->group(function () {
    
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->role === 'docente' || $user->role === 'admin') {
            return redirect('/dashboard/profesor');
        }
        return view('Interfaz_estudiante.estudiante');
    })->name('dashboard.estudiante');

    Route::get('/pin', function () {
        return view('PIN.index');
    })->name('estudiante.pin');

    Route::get('/skins', function () {
        return view('skins.index');
    })->name('estudiante.skins');

    // RUTA DE JUEGO DEL ESTUDIANTE
    Route::get('/juego/{pin}', function ($pin) {
        $sala = SalaJuego::with(['cuestionario.preguntas.opciones'])->where('pin', $pin)->firstOrFail();
        
        $preguntas = ($sala->cuestionario && $sala->cuestionario->preguntas) 
            ? $sala->cuestionario->preguntas 
            : collect();

        return view('Interfaz_estudiante.juego', compact('sala', 'preguntas'));
    })->name('estudiante.juego');

    // RUTA PARA REGISTRAR LA RESPUESTA DEL ESTUDIANTE (VÍA AJAX)
    Route::post('/responder', function (Request $request) {
        $request->validate([
            'pin' => 'required',
            'pregunta_id' => 'required|exists:preguntas,id',
            'opcion_id' => 'required|exists:opciones,id',
            'tiempo_transcurrido' => 'nullable|integer',
        ]);

        $pin = $request->pin;
        $sala = SalaJuego::where('pin', $pin)->firstOrFail();
        $userId = Auth::id();

        $opcion = Opcion::findOrFail($request->opcion_id);
        $esCorrecta = $opcion->es_correcta;

        RespuestaEstudiante::updateOrCreate(
            [
                'sala_id' => $sala->id,
                'user_id' => $userId,
                'pregunta_id' => $request->pregunta_id,
            ],
            [
                'opcion_id' => $request->opcion_id,
                'es_correcta' => $esCorrecta,
                'tiempo_respuesta_segundos' => $request->tiempo_transcurrido ?? 0,
            ]
        );

        broadcast(new RespuestaEnviadaEvento($pin, $userId, $request->pregunta_id));

        return response()->json([
            'status' => 'success',
            'es_correcta' => $esCorrecta,
            'mensaje' => $esCorrecta ? '¡Respuesta correcta!' : 'Respuesta incorrecta'
        ]);
    })->name('estudiante.responder');
});

// RUTA DE UNIÓN A SALA (Soporta GET y POST, y envía userId al evento AlumnoUnido)
Route::match(['get', 'post'], '/estudiante/unirse', function (Request $request) {
    $pin = $request->input('pin');

    if (!$pin) {
        return redirect()->route('estudiante.pin');
    }

    $sala = SalaJuego::where('pin', $pin)->whereIn('estado', ['activa', 'en_curso'])->first();

    if (!$sala) {
        return redirect()->route('estudiante.pin')->withErrors(['pin' => 'El PIN ingresado no es válido o la sala está cerrada.']);
    }

    $user = Auth::user();
    $sala->usuarios()->syncWithoutDetaching([$user->id]);

    $nombreAlumno = $user->name ?? $user->nickname ?? 'Estudiante';
    
    // Se pasa el nombre, el PIN y el ID del usuario al evento de WebSockets
    broadcast(new AlumnoUnido($nombreAlumno, $pin, $user->id));

    return view('Interfaz_estudiante.sala_activa', compact('sala'));
})->middleware(['auth', 'verified'])->name('estudiante.unirse');

// RUTA DE SALIDA DE SALA
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

// RUTA API PARTICIPANTES
Route::get('/api/sala/{id}/participantes', function ($id) {
    $sala = SalaJuego::with('usuarios')->findOrFail($id);

    return response()->json([
        'total' => $sala->usuarios->count(),
    ]);
})->middleware(['auth', 'verified']);

// 4. Redirección por rol
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'docente' || $user->role === 'admin') {
        return redirect()->route('dashboard.profesor');
    }
    return redirect()->route('dashboard.estudiante');
})->middleware(['auth', 'verified'])->name('dashboard');

// 5. Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 6. Logout
Route::get('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';