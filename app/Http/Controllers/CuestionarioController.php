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

        // 2. Guardar preguntas, tiempos convertidos a segundos, imágenes y opciones
        if ($request->has('preguntas')) {
            foreach ($request->preguntas as $index => $pData) {
                
                $rutaImagen = null;

                // Verificar si se subió una imagen para esta pregunta específica
                if ($request->hasFile("preguntas.{$index}.imagen")) {
                    $file = $request->file("preguntas.{$index}.imagen");
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    // Guarda la imagen en storage/app/public/preguntas
                    $rutaImagen = $file->storeAs('preguntas', $filename, 'public');
                }

                // Calcular el tiempo total en segundos combinando minutos y segundos
                $minutos = isset($pData['minutos']) ? intval($pData['minutos']) : 0;
                $segundos = isset($pData['segundos']) ? intval($pData['segundos']) : 30;
                $tiempoTotalSegundos = ($minutos * 60) + $segundos;

                if ($tiempoTotalSegundos <= 0) {
                    $tiempoTotalSegundos = 30; // Valor por defecto si ambos están en 0
                }

                $pregunta = Pregunta::create([
                    'cuestionario_id' => $cuestionario->id,
                    'pregunta' => $pData['texto'] ?? 'Pregunta sin texto',
                    'imagen' => $rutaImagen,
                    'tiempo' => $tiempoTotalSegundos,
                ]);

                if (isset($pData['opciones'])) {
                    foreach ($pData['opciones'] as $indexOpcion => $opcionTexto) {
                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'opcion' => $opcionTexto,
                            'es_correcta' => ($indexOpcion == 0), // La primera opción es correcta por defecto
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