<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background: linear-gradient(135deg, var(--kahoot-purple-deep), #4a148c);
            color: var(--ivory);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 15px 25px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 30px;
        }
        .progress-text { font-weight: 700; font-size: 18px; color: #e0e0e0; }
        .timer-box { background: rgba(255, 255, 255, 0.9); color: #333; font-weight: 800; font-size: 22px; padding: 10px 20px; border-radius: 30px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 10px; }
        
        .question-container { text-align: center; max-width: 800px; margin: 0 auto; width: 100%; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; }
        .question-title { font-size: 28px; font-weight: 700; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); padding: 30px; border-radius: 16px; margin-bottom: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: 1px solid rgba(255, 255, 255, 0.2); }
        .question-image { max-width: 100%; height: 250px; border-radius: 12px; margin-bottom: 25px; object-fit: contain; background: rgba(0,0,0,0.2); padding: 10px; border: 1px solid rgba(255,255,255,0.1); }
        
        .answers-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; max-width: 800px; margin: 0 auto 30px auto; width: 100%; }
        .answer-btn { border: none; padding: 30px 20px; font-size: 20px; font-weight: 700; color: white; border-radius: 12px; cursor: pointer; box-shadow: 0 6px 0 rgba(0,0,0,0.3); transition: all 0.1s ease; display: flex; align-items: center; justify-content: center; text-align: center; word-wrap: break-word; }
        .answer-btn:active:not(:disabled) { transform: translateY(6px); box-shadow: 0 0px 0 rgba(0,0,0,0.3); }
        .answer-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: scale(0.98); }
        .btn-red { background: var(--kahoot-red); } .btn-blue { background: var(--kahoot-blue); } .btn-yellow { background: var(--kahoot-yellow); } .btn-green { background: var(--kahoot-green); }

        #end-screen { display: none; text-align: center; margin: auto; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 50px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.2); }
        #end-screen h2 { font-size: 36px; margin-bottom: 15px; }
        #end-screen p { font-size: 20px; color: #ccc; }
    </style>
</head>
<body>

    @php
        $preguntasCollection = isset($preguntas) ? collect($preguntas) : collect([]);$tiempoGlobal = 900; 

        $preguntasData =$preguntasCollection->isNotEmpty() ? $preguntasCollection->shuffle()->map(function($q) {
            return [
                'id' => $q->id,
                'pregunta' => $q->pregunta,
                'imagen' => $q->imagen ? asset('storage/' . $q->imagen) : null,
                'opciones' => ($q->opciones &&$q->opciones->isNotEmpty()) ? $q->opciones->shuffle()->map(function($o) {
                    return [
                        'id' => $o->id,
                        'opcion' => $o->opcion
                    ];
                })->values() : []
            ];
        })->values() : [];
    @endphp

    <div class="game-header" id="game-header">
        <div class="progress-text" id="progress-text">Preparando...</div>
        <div class="timer-box">
            <span>⏱️️</span> <span id="timer-display">15:00</span>
        </div>
    </div>

    <div id="game-container">
        <div class="question-container">
            <div class="question-title" id="question-text">Cargando pregunta...</div>
            <img src="" id="question-image" class="question-image" style="display: none;">
        </div>
        <div class="answers-grid" id="answers-grid">
        </div>
    </div>

    <div id="end-screen">
        <h2>¡Cuestionario Completado! 🚀</h2>
        <p>Has respondido todo. Espera a que el profesor finalice la sala para ver tus resultados y retroalimentación de la IA.</p>
    </div>

    <script>
        const salaPin = '{{ $sala->pin }}';
        const salaId = '{{ $sala->id }}';
        const preguntas = @json($preguntasData);
        let tiempoRestante = {{ $tiempoGlobal }};
        let indiceActual = 0;
        let tiempoInicioPregunta = 0;
        let temporizadorInterval;

        const timerDisplay = document.getElementById('timer-display');
        const progressText = document.getElementById('progress-text');
        const questionText = document.getElementById('question-text');
        const questionImage = document.getElementById('question-image');
        const answersGrid = document.getElementById('answers-grid');
        const gameContainer = document.getElementById('game-container');
        const endScreen = document.getElementById('end-screen');
        const gameHeader = document.getElementById('game-header');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const colores = ['btn-red', 'btn-blue', 'btn-yellow', 'btn-green'];

        function iniciarJuego() {
            if (preguntas.length === 0) { finalizarJuego(); return; }
            iniciarTemporizadorGlobal();
            cargarPregunta();
            escucharWebSockets(); // Inicializar Echo
        }

        function iniciarTemporizadorGlobal() {
            actualizarRelojUI();
            temporizadorInterval = setInterval(() => {
                tiempoRestante--;
                actualizarRelojUI();
                if (tiempoRestante <= 0) {
                    clearInterval(temporizadorInterval);
                    alert("¡Se acabó el tiempo global de 15 minutos!");
                    finalizarJuego();
                }
            }, 1000);
        }

        function actualizarRelojUI() {
            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;
            timerDisplay.textContent = `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
            if (tiempoRestante <= 30) timerDisplay.style.color = 'var(--kahoot-red)';
        }

        async function cargarPregunta() {
            const pregunta = preguntas[indiceActual];
            tiempoInicioPregunta = tiempoRestante;

            // Avisamos al backend que se mostró esta pregunta (para el cronómetro del servidor)
            fetch('/dashboard/estudiante/pregunta/iniciar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ sala_id: salaId, pregunta_id: pregunta.id })
            });

            progressText.textContent = `Pregunta ${indiceActual + 1} de ${preguntas.length}`;
            questionText.textContent = pregunta.pregunta;

            if (pregunta.imagen) {
                questionImage.src = pregunta.imagen;
                questionImage.style.display = 'inline-block';
            } else {
                questionImage.style.display = 'none';
            }

            answersGrid.innerHTML = '';
            pregunta.opciones.forEach((opcion, index) => {
                const btn = document.createElement('button');
                btn.className = `answer-btn ${colores[index % 4]}`;
                btn.textContent = opcion.opcion;
                btn.onclick = () => enviarRespuesta(pregunta.id, opcion.id, btn);
                answersGrid.appendChild(btn);
            });
        }

        async function enviarRespuesta(preguntaId, opcionId, botonSeleccionado) {
            const botones = answersGrid.querySelectorAll('.answer-btn');
            botones.forEach(b => b.disabled = true);
            botonSeleccionado.style.transform = 'scale(0.95)';
            botonSeleccionado.style.opacity = '1';

            try {
                await fetch('/dashboard/estudiante/responder', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ pin: salaPin, pregunta_id: preguntaId, opcion_id: opcionId })
                });
                setTimeout(() => { siguientePregunta(); }, 500);
            } catch (error) {
                console.error("Error al enviar la respuesta:", error);
                siguientePregunta();
            }
        }

        function siguientePregunta() {
            indiceActual++;
            if (indiceActual < preguntas.length) cargarPregunta();
            else finalizarJuego();
        }

        function finalizarJuego() {
            clearInterval(temporizadorInterval);
            gameContainer.style.display = 'none';
            gameHeader.style.display = 'none';
            endScreen.style.display = 'block';
        }

        // Magia para saltar a resultados
        function escucharWebSockets() {
            if (typeof window.Echo !== 'undefined') {
                window.Echo.channel(`sala.${salaPin}`)
                    .listen('.PartidaFinalizada', (e) => {
                        window.location.href = `/dashboard/estudiante/resultados/${salaId}`;
                    });
            }
        }

        window.onload = iniciarJuego;
    </script>
</body>
</html>