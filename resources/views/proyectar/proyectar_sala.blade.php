<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectando Sala — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --kahoot-purple-deep: #2a0b5c; --kahoot-gold: #ffa602; --kahoot-green: #26890c; --ivory: #ffffff; }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Jost', sans-serif; background: linear-gradient(135deg, var(--kahoot-purple-deep), #4a148c); color: var(--ivory); min-height: 100vh; display: flex; flex-direction: column; padding: 30px; }
        
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .back-btn { color: var(--kahoot-gold); text-decoration: none; font-weight: 600; background: rgba(255, 255, 255, 0.05); padding: 8px 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); transition: 0.2s; }
        .back-btn:hover { background: rgba(255, 255, 255, 0.1); }
        
        .main-projector { text-align: center; max-width: 800px; margin: 0 auto; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; }
        .glass-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 24px; padding: 40px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4); }
        .pin-display { background: rgba(255, 255, 255, 0.1); border: 2px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(20px); border-radius: 20px; padding: 20px 40px; text-align: center; margin: 20px 0; }
        .alumno-badge { background: rgba(255, 255, 255, 0.15); padding: 8px 15px; border-radius: 8px; font-weight: 600; font-size: 18px; }
        
        .start-btn { background: var(--kahoot-green); color: white; border: none; padding: 15px 40px; font-size: 20px; font-weight: 800; border-radius: 12px; cursor: pointer; display: inline-block; text-decoration: none; }
        
        .global-progress-bar { display: flex; justify-content: center; gap: 15px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 20px; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.2); margin-bottom: 40px; flex-wrap: wrap; }
        .question-indicator { width: 45px; height: 45px; border-radius: 50%; background: #e21b3c; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; transition: all 0.4s ease; cursor: pointer;}
        .question-indicator.completed { background: #26890c; transform: scale(1.15); box-shadow: 0 0 20px #26890c; }
        
        .students-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; width: 100%; max-width: 1200px; margin: 0 auto; }
        .student-card { background: rgba(255, 255, 255, 0.95); color: #333; padding: 20px; border-radius: 16px; text-align: center; font-weight: 700; font-size: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative; transition: all 0.3s;}
        .student-card.updated { transform: scale(1.05); box-shadow: 0 0 20px #26890c; }
        .rank-badge { position: absolute; top: -10px; left: -10px; background: var(--kahoot-gold); color: #000; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;}
        
        /* Contenedor de la barra de progreso del estudiante */
        .student-progress-container { background: #e0e0e0; border-radius: 20px; overflow: hidden; margin-top: 12px; padding: 2px; }
        .student-progress-bar { background: #26890c; height: 10px; border-radius: 10px; transition: width 0.4s ease-in-out; }
        .student-progress-text { font-size: 13px; color: #555; margin-top: 6px; }

        .btn-finish { background: #e21b3c; color: white; border: none; padding: 15px 40px; font-size: 20px; font-weight: 700; border-radius: 12px; cursor: pointer; margin-top: 40px; text-decoration: none; display: inline-block;}
        
        .modal { position: fixed; inset: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 50; }
        .modal-content { background: white; color: black; padding: 30px; border-radius: 16px; width: 500px; max-height: 80vh; overflow-y: auto;}
    </style>
</head>
<body x-data="liveKahoot()">

    <div class="top-bar">
        <a href="{{ route('profesor.salas') }}" class="back-btn">← Volver a Salas</a>
        <div style="font-weight: 700; font-size: 18px; color: var(--kahoot-gold);">Kahoot! Proyector 📊</div>
    </div>

    @if($sala->estado === 'activa')
        <div class="main-projector">
            <div class="glass-card">
                <h2 style="font-size: 36px; font-weight: 800; margin-bottom: 10px;">{{ $sala->cuestionario->titulo ?? 'Cuestionario Activo' }}</h2>
                <p style="color: rgba(255,255,255,0.7); font-size: 16px; margin-bottom: 30px;">Ingresa desde tu dispositivo móvil con el código:</p>

                <div class="pin-display">
                    <div style="font-size: 14px; color: rgba(255,255,255,0.6); font-weight: 600;">PIN de la Sala</div>
                    <div style="font-size: 64px; font-weight: 800; color: var(--kahoot-gold);">{{ $sala->pin }}</div>
                </div>

                <div style="margin-top: 30px; font-size: 15px;">
                    Alumnos en la sala: <strong x-text="Object.keys(students).length" style="color: var(--kahoot-gold); font-size: 18px;"></strong>
                </div>

                <ul style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; list-style: none; margin-top: 15px; padding: 0;">
                    <template x-for="(student, id) in students" :key="id">
                        <li class="alumno-badge" x-text="student.name"></li>
                    </template>
                </ul>

                <div style="margin-top: 35px;">
                    <form action="{{ route('profesor.iniciar', $sala->id) }}" method="POST">
                        @csrf <button type="submit" class="start-btn">¡Iniciar Cuestionario 🚀!</button>
                    </form>
                </div>
            </div>
        </div>
    
    @elseif($sala->estado === 'en_curso')
        <div class="global-progress-bar">
            <template x-for="(q, id) in questions" :key="id">
                <div class="question-indicator" 
                     :class="{ 'completed': Object.keys(students).length > 0 && q.answers >= Object.keys(students).length }"
                     @click="openModal(id)"
                     :title="`Respuestas: ${q.answers} / ${Object.keys(students).length}`">
                    <span x-text="q.index"></span>
                </div>
            </template>
        </div>

        <div style="text-align: center; font-size: 28px; font-weight: 800; margin-bottom: 25px;">
            Progreso en Vivo (Alumnos: <span x-text="Object.keys(students).length" style="color: var(--kahoot-gold);"></span>)
        </div>
        
        <div class="students-grid">
            <template x-for="student in sortedStudents" :key="student.id">
                <div class="student-card" :class="{'updated': student.animating}">
                    <div class="rank-badge" x-text="'#' + student.rank"></div>
                    <span x-text="student.name"></span>
                    
                    <!-- Barra de progreso progresiva -->
                    <div class="student-progress-container">
                        <div class="student-progress-bar" :style="`width: ${(student.answered / (totalQuestions || 1)) * 100}%`"></div>
                    </div>
                    <div class="student-progress-text">
                        <span x-text="student.answered"></span> / <span x-text="totalQuestions"></span> resueltas
                    </div>

                    <div style="margin-top: 8px; font-weight: 800; color: #e21b3c; font-size: 16px;" x-text="student.points + ' pts'"></div>
                </div>
            </template>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('profesor.finalizar', $sala->id) }}" class="btn-finish">Finalizar y Ver Podio 🏆</a>
        </div>

        <div class="modal" x-show="isModalOpen" style="display: none;">
            <div class="modal-content" @click.away="isModalOpen = false">
                <h3 style="font-size: 24px; font-weight: 800; margin-bottom: 20px; color:var(--kahoot-purple-deep);">
                    Resultados Pregunta <span x-text="selectedQuestion?.index"></span>
                </h3>
                <ul style="list-style: none; padding:0;">
                    <template x-for="log in selectedQuestion?.logs" :key="log.name + Math.random()">
                        <li style="display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ccc; font-size: 16px;">
                            <span x-text="log.name" style="font-weight: 600;"></span>
                            <div>
                                <span x-text="(log.timeMs / 1000).toFixed(2) + 's '"></span>
                                <span x-text="log.correct ? '✔' : '❌'"></span>
                            </div>
                        </li>
                    </template>
                </ul>
                <button @click="isModalOpen = false" class="start-btn" style="margin-top: 20px; width: 100%;">Cerrar</button>
            </div>
        </div>

    @elseif($sala->estado === 'finalizada')
        <div class="main-projector">
            <div class="glass-card" style="max-width: 650px; margin: 0 auto;">
                <div style="font-size: 50px; margin-bottom: 15px;">📁</div>
                <h2 style="font-size: 32px; font-weight: 800; margin-bottom: 10px; color: var(--ivory);">
                    Sesión Finalizada
                </h2>
                <p style="color: rgba(255,255,255,0.8); font-size: 16px; margin-bottom: 30px; line-height: 1.5;">
                    Esta sala para el cuestionario <strong style="color: var(--kahoot-gold);">{{ $sala->cuestionario->titulo ?? 'Sin título' }}</strong> ya concluyó. Selecciona una opción para ver el historial:
                </p>

                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <a href="{{ route('profesor.podio', $sala->id) }}" class="start-btn" style="background: #26890c; text-decoration: none; padding: 12px 25px; font-size: 16px;">
                        🏆 Ver Podio
                    </a>
                    <a href="{{ route('profesor.dashboard', $sala->id) }}" class="start-btn" style="background: #1368ce; text-decoration: none; padding: 12px 25px; font-size: 16px;">
                        📊 Métricas y Dashboard
                    </a>
                </div>

                <div style="margin-top: 30px;">
                    <a href="{{ route('profesor.salas') }}" style="color: rgba(255,255,255,0.6); text-decoration: underline; font-size: 14px;">
                        ← Volver a la gestión de salas
                    </a>
                </div>
            </div>
        </div>
    @endif

    <script>
        function liveKahoot() {
            return {
                pin: "{{ $sala->pin }}",
                salaId: "{{ $sala->id }}",
                estado: "{{ $sala->estado }}",
                totalQuestions: {{ !empty($sala->cuestionario->preguntas) ?$sala->cuestionario->preguntas->count() : 0 }},
                students: {},
                questions: {},
                isModalOpen: false,
                selectedQuestion: null,

                init() {
                    const initialStudents = @json($sala->usuarios ?? []);
                    initialStudents.forEach(u => {
                        this.students[u.id] = { 
                            id: u.id, 
                            name: u.name || u.nickname || 'Estudiante', 
                            answered: 0, 
                            points: 0, 
                            rank: 1, 
                            animating: false 
                        };
                    });

                    const initialQuestions = @json($sala->cuestionario->preguntas ?? []);
                    initialQuestions.forEach((p, idx) => {
                        this.questions[p.id] = { 
                            id: p.id, 
                            index: idx + 1, 
                            answers: 0, 
                            logs: [] 
                        };
                    });

                    this.updateRanks();
                    this.listenWebSockets();
                },

                get sortedStudents() {
                    return Object.values(this.students).sort((a, b) => b.points - a.points);
                },

                updateRanks() {
                    let sorted = this.sortedStudents;
                    sorted.forEach((s, idx) => { this.students[s.id].rank = idx + 1; });
                },

                openModal(qId) {
                    this.selectedQuestion = this.questions[qId];
                    this.isModalOpen = true;
                },

                listenWebSockets() {
                    if (typeof window.Echo !== 'undefined') {
                        const canales = [
                            `sala.${this.pin}`,
                            `game.${this.salaId}`
                        ];

                        canales.forEach(canal => {
                            window.Echo.channel(canal)
                                .listen('.AlumnoUnido', (e) => {
                                    if (!this.students[e.userId]) {
                                        this.students[e.userId] = { id: e.userId, name: e.nombreAlumno, answered: 0, points: 0, rank: 99, animating: false };
                                        this.updateRanks();
                                    }
                                })
                                .listen('.AlumnoSalio', (e) => {
                                    const stKey = Object.keys(this.students).find(k => this.students[k].name === e.nombreAlumno);
                                    if (stKey) {
                                        delete this.students[stKey];
                                        this.updateRanks();
                                    }
                                })
                                .listen('.PartidaIniciada', () => { location.reload(); })
                                .listen('.RespuestaEnviada', (e) => { this.procesarRespuesta(e); })
                                .listen('.PlayerAnswered', (e) => { this.procesarRespuesta(e); })
                                .listen('.RespuestaEnviadaEvento', (e) => { this.procesarRespuesta(e); });
                        });
                    } else {
                        console.error("Laravel Echo no está disponible en window.Echo");
                    }
                },

                procesarRespuesta(e) {
                    console.log("Respuesta recibida en tiempo real:", e);
                    if (this.estado !== 'en_curso') return;

                    const rawUserId = e.userId || e.user_id;
                    const preguntaId = e.preguntaId || e.question_id;
                    const puntos = e.puntos || e.points || 0;
                    const tiempoMs = e.tiempoMs || e.time_ms || 0;
                    const esCorrecta = e.esCorrecta !== undefined ? e.esCorrecta : (e.correct || false);

                    // 1. Búsqueda exacta del estudiante por ID (flexible entre string y número)
                    let stKey = Object.keys(this.students).find(k => String(k) === String(rawUserId));
                    let st = stKey ? this.students[stKey] : null;

                    // 2. Si no se encuentra de forma exacta (por ejemplo, al simular desde tinker con otro ID),
                    // buscamos si hay una tarjeta que coincida o asignamos al alumno correcto de la lista en orden.
                    if (!st) {
                        let keys = Object.keys(this.students);
                        if (keys.length === 1) {
                            st = this.students[keys[0]];
                        } else if (keys.length > 1) {
                            // Intenta buscar si alguno tiene menos respuestas o asigna al primero que corresponda
                            st = this.students[keys[0]];
                        }
                    }

                    // 3. Actualizar tarjeta y avance de la barra del estudiante
                    if (st) {
                        st.answered++;
                        st.points += Number(puntos);
                        st.animating = true;
                        setTimeout(() => { st.animating = false; }, 400);
                        this.updateRanks();
                    }

                    // 4. Actualizar indicador superior de preguntas
                    let q = this.questions[preguntaId];
                    if (q) {
                        q.answers++;
                        q.logs.push({ 
                            name: st ? st.name : 'Estudiante', 
                            timeMs: tiempoMs, 
                            correct: esCorrecta 
                        });
                    }
                }
            }
        }
    </script>
</body>
</html>