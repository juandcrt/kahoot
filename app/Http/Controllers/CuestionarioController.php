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

        // 2. Guardar preguntas, tiempos, imágenes opcionales y opciones enviadas desde el formulario dinámico
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

                $pregunta = Pregunta::create([
                    'cuestionario_id' => $cuestionario->id,
                    'pregunta' => $pData['texto'] ?? 'Pregunta sin texto',
                    'imagen' => $rutaImagen,
                    'tiempo' => $pData['tiempo'] ?? 20, // Tiempo por defecto de 20 segundos si no se define
                ]);

                if (isset($pData['opciones'])) {
                    foreach ($pData['opciones'] as $indexOpcion => $opcionTexto) {
                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'opcion' => $opcionTexto,
                            'es_correcta' => ($indexOpcion == 0), // La primera opción es correcta por defecto (puedes ajustarlo según tu vista)
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