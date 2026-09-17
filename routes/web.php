<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CuestionarioController;
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
});

// 3. Dashboard Estudiante y Acceso a Salas por PIN
Route::middleware(['auth', 'verified'])->prefix('dashboard/estudiante')->group(function () {
    Route::get('/', function () {
        $user = Auth::user();
        if ($user->role === 'docente' || $user->role === 'admin') {
            return redirect('/dashboard/profesor');
        }
        return view('Interfaz_estudiante.estudiante');
    })->name('dashboard.estudiante');
});

Route::post('/estudiante/unirse', function (Request $request) {
    $pin = $request->input('pin');
    $sala = SalaJuego::where('pin', $pin)->where('estado', 'activa')->first();

    if (!$sala) {
        return back()->withErrors(['pin' => 'El PIN ingresado no es válido o la sala está cerrada.']);
    }

    return view('Interfaz_estudiante.sala_activa', ['sala' => $sala]);
})->middleware(['auth', 'verified'])->name('estudiante.unirse');

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