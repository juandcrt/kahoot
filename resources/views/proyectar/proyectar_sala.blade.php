<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectando Sala — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --kahoot-purple-deep: #2a0b5c;
            --kahoot-gold: #ffa602;
            --kahoot-green: #26890c;
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
            padding: 30px;
        }
        
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .back-btn { color: var(--kahoot-gold); text-decoration: none; font-weight: 600; background: rgba(255, 255, 255, 0.05); padding: 8px 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); transition: 0.2s; }
        .back-btn:hover { background: rgba(255, 255, 255, 0.1); }
        
        .main-projector { text-align: center; max-width: 800px; margin: 0 auto; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; }
        .glass-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 24px; padding: 40px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4); }
        .pin-display { background: rgba(255, 255, 255, 0.1); border: 2px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(20px); border-radius: 20px; padding: 20px 40px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.4); margin: 20px 0; }
        .alumno-badge { background: rgba(255, 255, 255, 0.15); padding: 8px 15px; border-radius: 8px; font-weight: 600; font-size: 18px; transition: all 0.3s ease; }
        
        .start-btn { background: var(--kahoot-green); color: white; border: none; padding: 15px 40px; font-size: 20px; font-weight: 800; border-radius: 12px; cursor: pointer; box-shadow: 0 10px 20px rgba(0,0,0,0.3); transition: background 0.2s, transform 0.1s; display: inline-block; text-decoration: none; }
        .start-btn:hover { background: #1e6d09; }
        .start-btn:active { transform: translateY(4px); box-shadow: 0 2px 10px rgba(0,0,0,0.3); }

        .global-progress-bar { display: flex; justify-content: center; gap: 15px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 20px; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.2); margin-bottom: 40px; flex-wrap: wrap; }
        
        .question-indicator { width: 45px; height: 45px; border-radius: 50%; background: #e21b3c; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; transition: all 0.4s ease; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .question-indicator.completed { background: #26890c; transform: scale(1.15); box-shadow: 0 0 20px #26890c; }
        
        .students-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; width: 100%; max-width: 1200px; margin: 0 auto; }
        .student-card { background: rgba(255, 255, 255, 0.95); color: #333; padding: 20px; border-radius: 16px; text-align: center; font-weight: 700; font-size: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); display: flex; flex-direction: column; gap: 12px; transition: transform 0.2s, box-shadow 0.2s; }
        .student-progress { background: #e0e0e0; padding: 8px 12px; border-radius: 20px; font-size: 15px; color: #555; }
        .btn-finish { background: #e21b3c; color: white; border: none; padding: 15px 40px; font-size: 20px; font-weight: 700; border-radius: 12px; cursor: pointer; box-shadow: 0 6px 0 rgba(0,0,0,0.3); margin-top: 40px; transition: transform 0.1s; }
        .btn-finish:active { transform: translateY(4px); box-shadow: 0 2px 0 rgba(0,0,0,0.3); }
    </style>
</head>
<body>

    <div class="top-bar">
        <a href="{{ route('profesor.salas') }}" class="back-btn">← Volver a Salas</a>
        <div style="font-weight: 700; font-size: 18px; color: var(--kahoot-gold);">Kahoot! Proyector 📊</div>
    </div>

    @if($sala->estado === 'activa')
        <div class="main-projector">
            <div class="glass-card">
                <h2 style="font-size: 36px; font-weight: 800; margin-bottom: 10px; color: var(--ivory);">
                    {{ $sala->cuestionario->titulo ?? 'Cuestionario Activo' }}
                </h2>
                <p style="color: rgba(255,255,255,0.7); font-size: 16px; margin-bottom: 30px;">
                    Ingresa desde tu dispositivo móvil o panel con el siguiente código PIN:
                </p>

                <div class="pin-display">
                    <div style="font-size: 14px; color: rgba(255,255,255,0.6); font-weight: 600; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px;">PIN de la Sala</div>
                    <div style="font-size: 64px; font-weight: 800; color: var(--kahoot-gold); letter-spacing: 8px;">{{ $sala->pin }}</div>
                </div>

                <div style="margin-top: 30px; font-size: 15px; color: rgba(255,255,255,0.7);">
                    Alumnos en la sala: <strong id="contador-alumnos" style="color: var(--kahoot-gold); font-size: 18px;">{{ $sala->usuarios ? $sala->usuarios->count() : 0 }}</strong>
                </div>

                <ul id="lista-alumnos" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; list-style: none; margin-top: 15px; padding: 0;">
                    <?php if (!empty($sala->usuarios)): ?>
                        <?php foreach ($sala->usuarios as$usuario): ?>
                            <li class="alumno-badge">{{ $usuario->name ?? $usuario->nickname ?? 'Estudiante' }}</li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <div style="margin-top: 35px;">
                    <form action="{{ route('profesor.iniciar', $sala->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="start-btn">¡Iniciar Cuestionario 🚀!</button>
                    </form>
                </div>
            </div>
        </div>
    
    @elseif($sala->estado === 'en_curso')
        <div class="global-progress-bar" id="global-progress">
            <?php if (!empty($sala->cuestionario) && !empty($sala->cuestionario->preguntas)): ?>
                <?php foreach ($sala->cuestionario->preguntas as $index =>$pregunta): ?>
                    <div class="question-indicator" id="q-indicator-{{ $pregunta->id }}" title="Pregunta {{ $index + 1 }}">
                        {{ $index + 1 }}
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="text-align: center; font-size: 28px; font-weight: 800; margin-bottom: 25px;">
            Progreso en Vivo (Alumnos: <span id="contador-alumnos-vivo" style="color: var(--kahoot-gold);">{{ $sala->usuarios ? $sala->usuarios->count() : 0 }}</span>)
        </div>
        
        <div class="students-grid" id="students-list">
            <?php if (!empty($sala->usuarios)): ?>
                <?php $totalPreguntasCount = (!empty($sala->cuestionario) && !empty($sala->cuestionario->preguntas)) ?$sala->cuestionario->preguntas->count() : 0; ?>
                <?php foreach ($sala->usuarios as$usuario): ?>
                    <div class="student-card" id="student-{{ $usuario->id }}" data-answered="0">
                        <span>{{ $usuario->name ?? $usuario->nickname }}</span>
                        <div class="student-progress" id="progress-{{ $usuario->id }}">0 / {{ $totalPreguntasCount }} resueltas</div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div style="text-align: center;">
            <form action="{{ route('profesor.podio', $sala->id) }}" method="GET">
                <button type="submit" class="btn-finish">Finalizar y Ver Podio 🏆</button>
            </form>
        </div>

    @elseif($sala->estado === 'finalizada')
        <div class="main-projector">
            <div class="glass-card">
                <h2 style="font-size: 36px; font-weight: 800; margin-bottom: 10px; color: var(--ivory);">
                    Sala Finalizada 🏁
                </h2>
                <p style="color: rgba(255,255,255,0.7); font-size: 18px; margin-bottom: 30px;">
                    Este cuestionario ya ha concluido y no acepta más respuestas.
                </p>
                <div style="text-align: center;">
                    <a href="{{ route('profesor.podio', $sala->id) }}" class="start-btn">Ver Podio y Resultados 🏆</a>
                </div>
            </div>
        </div>
    @endif

    <script type="module">
        const PIN = "{{ $sala->pin }}";
        const estadoSala = "{{ $sala->estado }}";
        const totalPreguntas = {{ (!empty($sala->cuestionario) && !empty($sala->cuestionario->preguntas)) ?$sala->cuestionario->preguntas->count() : 0 }};
        let totalAlumnos = {{ $sala->usuarios ? $sala->usuarios->count() : 0 }};
        
        let respuestasPorPregunta = {};
        const idsPreguntas = @json((!empty($sala->cuestionario) && !empty($sala->cuestionario->preguntas)) ?$sala->cuestionario->preguntas->pluck('id') : []);
        idsPreguntas.forEach(id => {
            respuestasPorPregunta[id] = 0;
        });

        if (typeof window.Echo !== 'undefined') {
            console.log("Conectado a WebSockets. Escuchando canal: sala." + PIN);

            window.Echo.channel(`sala.${PIN}`)
                .listen('.AlumnoUnido', (e) => {
                    console.log("¡Alumno unido detectado en proyector!", e);
                    if (estadoSala === 'activa') {
                        let lista = document.getElementById('lista-alumnos');
                        if (!lista) return;
                        let yaExiste = Array.from(lista.querySelectorAll('.alumno-badge')).some(el => el.innerText.trim() === e.nombreAlumno.trim());
                        
                        if (!yaExiste) {
                            totalAlumnos++;
                            let contador = document.getElementById('contador-alumnos');
                            if (contador) contador.innerText = totalAlumnos;
                            let nuevo = document.createElement('li');
                            nuevo.innerText = e.nombreAlumno;
                            nuevo.className = 'alumno-badge';
                            lista.appendChild(nuevo);
                        }
                    }
                })
                .listen('.AlumnoSalio', (e) => {
                    console.log("Alumno salió:", e);
                    if (estadoSala === 'activa') {
                        let lista = document.getElementById('lista-alumnos');
                        if (!lista) return;
                        let items = Array.from(lista.querySelectorAll('.alumno-badge'));
                        let itemToRemove = items.find(el => el.innerText.trim() === e.nombreAlumno.trim());
                        
                        if (itemToRemove) {
                            itemToRemove.remove();
                            totalAlumnos = Math.max(0, totalAlumnos - 1);
                            let contador = document.getElementById('contador-alumnos');
                            if (contador) contador.innerText = totalAlumnos;
                        }
                    }
                })
                .listen('.PartidaIniciada', (e) => {
                    console.log("¡Partida iniciada detectada por WebSocket!");
                    location.reload();
                })
                .listen('.RespuestaEnviadaEvento', (e) => {
                    if (estadoSala !== 'en_curso') return;

                    const pId = e.preguntaId;
                    const uId = e.userId;

                    respuestasPorPregunta[pId] = (respuestasPorPregunta[pId] || 0) + 1;

                    let studentCard = document.getElementById(`student-${uId}`);
                    if (studentCard) {
                        let answered = parseInt(studentCard.dataset.answered) + 1;
                        studentCard.dataset.answered = answered;
                        
                        let progressText = document.getElementById(`progress-${uId}`);
                        if(progressText) {
                            progressText.innerText = `${answered} / ${totalPreguntas} resueltas`;
                        }
                        
                        studentCard.style.transform = 'scale(1.08)';
                        studentCard.style.boxShadow = '0 0 20px rgba(38, 137, 12, 0.4)';
                        setTimeout(() => {
                            studentCard.style.transform = 'scale(1)';
                            studentCard.style.boxShadow = '0 5px 15px rgba(0,0,0,0.3)';
                        }, 300);
                    }

                    if (respuestasPorPregunta[pId] >= totalAlumnos && totalAlumnos > 0) {
                        const qIndicator = document.getElementById(`q-indicator-${pId}`);
                        if (qIndicator) qIndicator.classList.add('completed');
                    }
                });
        } else {
            console.error("Laravel Echo no está definido.");
        }
    </script>
</body>
</html>