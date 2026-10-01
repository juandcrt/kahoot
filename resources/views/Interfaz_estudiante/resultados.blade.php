<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tus Resultados — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root { --kahoot-purple-deep: #2a0b5c; --kahoot-gold: #ffa602; --kahoot-green: #26890c; --kahoot-red: #e21b3c; --ivory: #ffffff; }
        body { font-family: 'Jost', sans-serif; background: linear-gradient(135deg, var(--kahoot-purple-deep), #4a148c); color: var(--ivory); min-height: 100vh; padding: 30px; display: flex; flex-direction: column; align-items: center; }
        .glass-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 24px; padding: 40px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4); width: 100%; max-width: 600px; text-align: center; margin-bottom: 20px;}
        .score-box { background: rgba(0,0,0,0.2); border-radius: 16px; padding: 20px; margin: 20px 0; border: 2px solid var(--kahoot-gold); }
        .feedback-box { background: rgba(255,255,255,0.95); color: #333; padding: 25px; border-radius: 16px; text-align: left; margin-top: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 14px; margin: 5px; }
        .badge-correct { background: var(--kahoot-green); color: white; }
        .badge-wrong { background: var(--kahoot-red); color: white; }
        .btn-home { background: var(--kahoot-gold); color: #000; border: none; padding: 15px 30px; font-size: 18px; font-weight: 800; border-radius: 12px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="glass-card">
        <h1 style="font-size: 32px; font-weight: 800;">¡Cuestionario Finalizado! 🏁</h1>
        <p style="font-size: 18px; color: rgba(255,255,255,0.8); margin-top: 10px;">Aquí tienes el resumen de tu desempeño, {{ Auth::user()->name }}.</p>

        <div class="score-box">
            <div style="font-size: 16px; color: var(--kahoot-gold); font-weight: 700; text-transform: uppercase;">Puntaje Total</div>
            <div style="font-size: 54px; font-weight: 800;">{{ $puntajeTotal ?? 0 }} <span style="font-size: 20px;">pts</span></div>
        </div>

        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <div class="badge badge-correct">✔️ {{ $correctas ?? 0 }} Correctas</div>
            <div class="badge badge-wrong">❌ {{ $incorrectas ?? 0 }} Incorrectas</div>
        </div>

        <!-- Sección de IA Gemini -->
        <div class="feedback-box">
            <h3 style="font-size: 20px; font-weight: 800; color: var(--kahoot-purple-deep); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                ✨ Retroalimentación de la IA
            </h3>
            
            @if(isset($feedback) && $feedback->status === 'completed')
                <p style="font-size: 16px; line-height: 1.6;">{{ $feedback->feedback_text }}</p>
            @elseif(isset($feedback) && $feedback->status === 'pending')
                <p style="font-size: 16px; font-style: italic; color: #666;">Generando consejos personalizados con IA... Recarga la página en unos segundos.</p>
            @else
                <p style="font-size: 16px; line-height: 1.6;">¡Gran esfuerzo! Sigue practicando. Asegúrate de leer bien cada pregunta antes de responder para mejorar tu precisión en la próxima sala.</p>
            @endif
        </div>

        <a href="{{ route('dashboard.estudiante') }}" class="btn-home">Volver al Inicio</a>
    </div>

</body>
</html>