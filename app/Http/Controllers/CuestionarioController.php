<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuestionario;
use App\Models\Pregunta;
use App\Models\Opcion;
use App\Models\SalaJuego;
use Illuminate\Support\Facades\Auth;

class CuestionarioController extends Controller
{
    public function create()
    {
        return view('cuestionario.cuestionario');
    }

    public function store(Request $request)
    {
        // 1. Crear el cuestionario
        $cuestionario = Cuestionario::create([
            'user_id' => Auth::id(),
            'titulo' => $request->titulo ?? 'Cuestionario sin título',
            'tipo' => 'manual',
        ]);

        // 2. Guardar preguntas y opciones enviadas desde el formulario dinámico
        if ($request->has('preguntas')) {
            foreach ($request->preguntas as $pData) {
                $pregunta = Pregunta::create([
                    'cuestionario_id' => $cuestionario->id,
                    'pregunta' => $pData['texto'] ?? 'Pregunta sin texto',
                ]);

                if (isset($pData['opciones'])) {
                    foreach ($pData['opciones'] as $index => $opcionTexto) {
                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'opcion' => $opcionTexto,
                            'es_correcta' => ($index == 0), // La primera opción es correcta por defecto
                        ]);
                    }
                }
            }
        }

        // 3. Generar Sala Activa con PIN de 6 dígitos único
        $pinSala = rand(100000, 999999);
        SalaJuego::create([
            'cuestionario_id' => $cuestionario->id,
            'pin' => $pinSala,
            'estado' => 'activa',
        ]);

        return redirect()->route('dashboard.profesor')->with('success', '¡Cuestionario guardado! PIN de sala activo: ' . $pinSala);
    }
}