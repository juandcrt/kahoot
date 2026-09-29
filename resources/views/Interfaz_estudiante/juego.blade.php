<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partida en Curso — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --kahoot-red: #e21b3c;
            --kahoot-blue: #1368ce;
            --kahoot-yellow: #d89e00;
            --kahoot-green: #26890c;
            --kahoot-purple-deep: #2a0b5c;
            --ivory: #ffffff;
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Jost', sans-serif;
            background: var(--kahoot-purple-deep);
            color: var(--ivory);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
        }
        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0,0,0,0.2);
            padding: 15px 25px;
            border-radius: 16px;
        }
        .timer-box {
            background: white;
            color: #333;
            font-weight: 800;
            font-size: 24px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .question-container {
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
            width: 100%;
        }
        .question-title {
            font-size: 24px;
            font-weight: 700;
            background: rgba(255,255,255,0.08);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .question-image {
            max-width: 100%;
            max-height: 220px;
            border-radius: 12px;
            margin-bottom: 20px;
            object-fit: contain;
            border: 2px solid rgba(255,255,255,0.2);
        }
        .answers-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            max-width: 700px;
            margin: 0 auto;
            width: 100%;
        }
        .answer-btn {
            border: none;
            padding: 25px;
            font-size: 18px;
            font-weight: 700;
            color: white;
            border-radius: 16px;
            cursor: pointer;
            box-shadow: 0 6px 0 rgba(0,0,0,0.3);
            transition: transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .answer-btn:active { transform: translateY(4px); box-shadow: 0 2px 0 rgba(0,0,0,0.3); }
        .btn-red { background: var(--kahoot-red); }
        .btn-blue { background: var(--kahoot-blue); }
        .btn-yellow { background: var(--kahoot-yellow); }
        .btn-green { background: var(--kahoot-green); }
    </style>
</head>
<body>

    <?php
        // Algoritmo anti-copia: Barajamos las preguntas y las opciones de forma única
        $preguntasBarajadas = $preguntas->shuffle();
        $preguntaActual = $preguntasBarajadas->first();
        $opcionesBarajadas = $preguntaActual ? $preguntaActual->opcions->shuffle() : collect();
    ?>

    @if(!$preguntaActual)
        <div style="text-align: center; margin: auto;">
            <h2>¡El cuestionario ha finalizado! 🎉</h2>
            <p>Revisa la pantalla del proyector para ver los resultados.</p>
        </div>
    @else
        <!-- Cabecera con Puntaje y Temporizador -->
        <div class="game-header">
            <div style="font-weight: 700; font-size: 18px;">Puntaje: <span id="score" style="color: #ffa602;">0</span></div>
            <div class="timer-box" id="timer">{{ $preguntaActual->tiempo ?? 20 }}</div>
        </div>

        <!-- Contenido de la Pregunta e Imagen -->
        <div class="question-container">
            <div class="question-title">{{ $preguntaActual->pregunta }}</div>

            @if($preguntaActual->imagen)
                <div>
                    <img src="{{ asset('storage/' . $preguntaActual->imagen) }}" alt="Imagen de apoyo" class="question-image">
                </div>
            @endif
        </div>

        <!-- Botones de Respuesta con Opciones Barajadas -->
        <div class="answers-grid">
            @php
                $colores = ['btn-red', 'btn-blue', 'btn-yellow', 'btn-green'];
            @endphp

            @foreach($opcionesBarajadas as $index => $opcion)
                <button class="answer-btn {{ $colores[$index % 4] }}" onclick="enviarRespuesta({{ $opcion->es_correcta ? 1 : 0 }})">
                    {{ $opcion->opcion }}
                </button>
            @endforeach
        </div>
    @endif

    <script>
        function enviarRespuesta(esCorrecta) {
            if(esCorrecta === 1) {
                alert("¡Correcto! Sumas puntos 🚀");
            } else {
                alert("¡Incorrecto! ❌");
            }
        }
    </script>

</body>
</html>