<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\StudentFeedback;

class GenerateAiFeedback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $feedbackId;
    public $promptData;

    public function __construct($feedbackId, $promptData)
    {
        $this->feedbackId = $feedbackId;
        $this->promptData = $promptData; // JSON con preguntas, respuestas, tiempos
    }

    public function handle()
    {
        $feedbackModel = StudentFeedback::find($this->feedbackId);
        if (!$feedbackModel) return;

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            $this->fallback($feedbackModel);
            return;
        }

        $prompt = "Eres un profesor motivador. Analiza el siguiente desempeño de un alumno de secundaria y entrégale 2-3 recomendaciones concretas en máximo 250 palabras. Identifica si falló por rapidez impulsiva o lentitud. Datos: " . json_encode($this->promptData);

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                $feedbackModel->update(['feedback_text' => $text, 'status' => 'completed']);
            } else {
                $this->fallback($feedbackModel);
            }
        } catch (\Exception $e) {
            $this->fallback($feedbackModel);
        }
    }

    private function fallback($model) {
        $model->update([
            'feedback_text' => "¡Gran esfuerzo! Sigue practicando los temas donde tomaste más tiempo. Revisa tus apuntes y asegúrate de leer bien cada pregunta antes de responder.",
            'status' => 'completed'
        ]);
    }
}