<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\SalaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalaJuego;
use App\Events\AlumnoUnido;
use App\Events\AlumnoSalio;
use App\Events\PartidaIniciada;

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

    // Módulo de Cuestionarios (Crear y Guardar)
    Route::get('/cuestionario', [CuestionarioController::class, 'create'])->name('cuestionario.crear');
    Route::post('/cuestionario', [CuestionarioController::class, 'store'])->name('cuestionario.store');

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

        $sala = SalaJuego::with('cuestionario')->findOrFail($id);

        return view('proyectar.proyectar_sala', compact('sala'));
    })->name('profesor.proyectar');

    // RUTA PARA INICIAR EL JUEGO: Cambia el estado y avisa a los alumnos por WebSockets
    Route::post('/sala/{id}/iniciar', function ($id) {
        $sala = SalaJuego::findOrFail($id);
        
        $sala->estado = 'en_curso';
        $sala->save();

        broadcast(new PartidaIniciada($sala->pin));

        return redirect()->route('profesor.proyectar', $id);
    })->name('profesor.iniciar');

    // RUTA LIMPIA: Apunta al método destroy del SalaController
    Route::delete('/salas/{id}', [SalaController::class, 'destroy'])->name('salas.destruir');
});

// 3. Dashboard Estudiante, Acceso a Salas por PIN y Skins
Route::middleware(['auth', 'verified'])->prefix('dashboard/estudiante')->group(function () {
    
    // Panel principal de estudiante
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->role === 'docente' || $user->role === 'admin') {
            return redirect('/dashboard/profesor');
        }
        return view('Interfaz_estudiante.estudiante');
    })->name('dashboard.estudiante');

    // Vista de formulario para ingresar PIN
    Route::get('/pin', function () {
        return view('PIN.index');
    })->name('estudiante.pin');

    // Vista de personalización de Skins
    Route::get('/skins', function () {
        return view('skins.index');
    })->name('estudiante.skins');

    // RUTA DE JUEGO DEL ESTUDIANTE
    Route::get('/juego/{pin}', function ($pin) {
        $sala = SalaJuego::with(['cuestionario.preguntas.opcions'])->where('pin', $pin)->firstOrFail();
        $preguntas = $sala->cuestionario->preguntas;

        return view('Interfaz_estudiante.juego', compact('sala', 'preguntas'));
    })->name('estudiante.juego');

    // RUTA PARA REGISTRAR LA RESPUESTA DEL ESTUDIANTE (VÍA AJAX)
    Route::post('/responder', function (Request $request) {
        $esCorrecta = $request->input('es_correcta');
        
        return response()->json([
            'status' => 'success',
            'es_correcta' => $esCorrecta,
            'mensaje' => $esCorrecta ? '¡Respuesta correcta!' : 'Respuesta incorrecta'
        ]);
    })->name('estudiante.responder');
});

// RUTA DE UNIÓN: Permite registrar al alumno si la sala está activa o en curso
Route::post('/estudiante/unirse', function (Request $request) {
    $pin = $request->input('pin');
    $sala = SalaJuego::where('pin', $pin)->whereIn('estado', ['activa', 'en_curso'])->first();

    if (!$sala) {
        return back()->withErrors(['pin' => 'El PIN ingresado no es válido o la sala está cerrada.']);
    }

    // 1. REGISTRA AL USUARIO EN LA SALA
    $sala->usuarios()->syncWithoutDetaching([Auth::id()]);

    // 2. DISPARA EL EVENTO EN TIEMPO REAL HACIA EL PROYECTOR (ENTRADA)
    $nombreAlumno = Auth::user()->name ?? Auth::user()->nickname ?? 'Estudiante';
    broadcast(new AlumnoUnido($nombreAlumno, $pin));

    return view('Interfaz_estudiante.sala_activa', compact('sala'));
})->middleware(['auth', 'verified'])->name('estudiante.unirse');

// RUTA DE SALIDA: Desvincula al estudiante y avisa al proyector en tiempo real
Route::post('/estudiante/salir', function (Request $request) {
    $pin = $request->input('pin');
    $sala = SalaJuego::where('pin', $pin)->first();

    if ($sala) {
        $nombreAlumno = Auth::user()->name ?? Auth::user()->nickname ?? 'Estudiante';
        
        // 1. Desvincula al usuario de la sala en la base de datos
        $sala->usuarios()->detach(Auth::id());

        // 2. Dispara el evento en tiempo real hacia el proyector (SALIDA)
        broadcast(new AlumnoSalio($nombreAlumno, $pin));
    }

    return redirect()->route('dashboard.estudiante');
})->middleware(['auth', 'verified'])->name('estudiante.salir');

// RUTA API: Devuelve el total de conectados para el profesor
Route::get('/api/sala/{id}/participantes', function ($id) {
    $sala = SalaJuego::with('usuarios')->findOrFail($id);

    return response()->json([
        'total' => $sala->usuarios->count(),
    ]);
})->middleware(['auth', 'verified']);

// 4. Ruta inteligente genérica de redirección por rol
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->role === 'docente' || $user->role === 'admin') {
        return redirect()->route('dashboard.profesor');
    }
    return redirect()->route('dashboard.estudiante');
})->middleware(['auth', 'verified'])->name('dashboard');

// 5. Grupo de rutas protegidas para la gestión del perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 6. Ruta de cierre de sesión
Route::get('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Incluye las rutas de autenticación de Breeze
require __DIR__.'/auth.php';