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

        // 2. Guardar preguntas, tiempos, imágenes y opciones
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

                $tiempoTotalSegundos = 30; // Valor por defecto general

                $pregunta = Pregunta::create([
                    'cuestionario_id' => $cuestionario->id,
                    'pregunta' => $pData['texto'] ?? 'Pregunta sin texto',
                    'imagen' => $rutaImagen,
                    'tiempo' => $tiempoTotalSegundos,
                ]);

                // Obtener el índice de la respuesta correcta seleccionada por el profesor (radio button)
                $indiceCorrecta = isset($pData['correcta']) ? intval($pData['correcta']) : 0;

                if (isset($pData['opciones'])) {
                    foreach ($pData['opciones'] as $indexOpcion => $opcionTexto) {
                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'opcion' => $opcionTexto,
                            'es_correcta' => ($indexOpcion === $indiceCorrecta), // Compara con la opción elegida
                        ]);
                    }
                }
            }
        }

        // 3. Generar Sala Activa con PIN de 6 dígitos único
        $pinSala = rand(100000, 999999);
        $sala = SalaJuego::create([
            'cuestionario_id' => $cuestionario->id,
            'pin' => $pinSala,
            'estado' => 'activa',
        ]);

        // 4. Redirigir directamente a la vista de proyectar la sala con éxito
        return redirect()->route('profesor.proyectar', $sala->id)
            ->with('success', '¡Cuestionario guardado con éxito! PIN de sala activo: ' . $pinSala);
    }

    // Método para mostrar la vista de edición con los datos actuales
    public function edit($id)
    {
        $cuestionario = Cuestionario::with('preguntas.opciones')->findOrFail($id);
        return view('cuestionario.edit', compact('cuestionario'));
    }

    // Método para actualizar las modificaciones del cuestionario existente
    public function update(Request $request, $id)
    {
        $cuestionario = Cuestionario::findOrFail($id);
        
        $cuestionario->update([
            'titulo' => $request->titulo ?? 'Cuestionario sin título',
        ]);

        // Reemplazar preguntas y alternativas anteriores con las nuevas editadas
        foreach ($cuestionario->preguntas as $preguntaAntigua) {
            $preguntaAntigua->opciones()->delete();
            $preguntaAntigua->delete();
        }

        if ($request->has('preguntas')) {
            foreach ($request->preguntas as $index => $pData) {
                $rutaImagen = null;
                if ($request->hasFile("preguntas.{$index}.imagen")) {
                    $file = $request->file("preguntas.{$index}.imagen");
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $rutaImagen = $file->storeAs('preguntas', $filename, 'public');
                }

                $pregunta = Pregunta::create([
                    'cuestionario_id' => $cuestionario->id,
                    'pregunta' => $pData['texto'] ?? 'Pregunta sin texto',
                    'imagen' => $rutaImagen,
                    'tiempo' => 30,
                ]);

                $indiceCorrecta = isset($pData['correcta']) ? intval($pData['correcta']) : 0;

                if (isset($pData['opciones'])) {
                    foreach ($pData['opciones'] as $indexOpcion => $opcionTexto) {
                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'opcion' => $opcionTexto,
                            'es_correcta' => ($indexOpcion === $indiceCorrecta),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('profesor.salas')->with('success', '¡Cuestionario actualizado con éxito!');
    }
}