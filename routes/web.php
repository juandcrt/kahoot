<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CuestionarioController;
use App\Http\Controllers\SalaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalaJuego;

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
});

// RUTA CORREGIDA: Registra al alumno y le pasa la sala a la vista
Route::post('/estudiante/unirse', function (Request $request) {
    $pin = $request->input('pin');
    $sala = SalaJuego::where('pin', $pin)->where('estado', 'activa')->first();

    if (!$sala) {
        return back()->withErrors(['pin' => 'El PIN ingresado no es válido o la sala está cerrada.']);
    }

    // REGISTRA AL USUARIO REAL EN LA SALA
    $sala->usuarios()->syncWithoutDetaching([Auth::id()]);

    return view('Interfaz_estudiante.sala_activa', compact('sala'));
})->middleware(['auth', 'verified'])->name('estudiante.unirse');

// RUTA API AÑADIDA: Devuelve el total de conectados para el profesor
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