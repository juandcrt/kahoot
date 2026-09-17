<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuestionario — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --kahoot-purple: #46178f;
            --kahoot-purple-deep: #2a0b5c;
            --kahoot-pink: #e21b3c;
            --kahoot-blue: #1368ce;
            --kahoot-gold: #ffa602;
            --ivory: #ffffff;
        }

        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Jost', sans-serif;
            background: var(--kahoot-purple-deep);
            color: var(--ivory);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        .bg-orbs {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background: radial-gradient(circle at 50% 20%, #6830cc 0%, #2a0b5c 60%, #120324 100%);
            overflow: hidden;
        }
        .orb {
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.4;
            animation: floatOrb 15s ease-in-out infinite alternate;
        }
        .o1 { width: 550px; height: 550px; top: -10%; left: -10%; background: rgba(226, 27, 60, 0.3); }
        .o2 { width: 600px; height: 600px; bottom: -15%; right: -10%; background: rgba(19, 104, 206, 0.3); animation-delay: -5s; }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, -50px) scale(1.1); }
            100% { transform: translate(-30px, 40px) scale(0.95); }
        }

        .main-container {
            position: relative; z-index: 10;
            max-width: 900px; margin: 0 auto; padding: 40px 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-top-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 35px;
            margin-bottom: 25px;
        }

        .dash-nav {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .dash-logo {
            font-size: 24px; font-weight: 800;
            background: linear-gradient(135deg, #fff 30%, #ffa602 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .tab-buttons {
            display: flex; gap: 15px; margin-bottom: 25px;
        }
        .tab-btn {
            flex: 1; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.12);
            color: white; padding: 12px; font-weight: 700; border-radius: 12px; cursor: pointer;
            transition: all 0.2s ease; text-align: center; font-family: 'Jost', sans-serif;
        }
        .tab-btn.active {
            background: linear-gradient(135deg, #46178f 0%, #2a0b5c 100%);
            border-color: var(--kahoot-gold);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .form-group { margin-bottom: 15px; }
        .form-label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--kahoot-gold); }
        .form-input {
            width: 100%; background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px;
            padding: 12px 15px; color: white; font-family: 'Jost', sans-serif; font-size: 15px;
            outline: none; transition: border-color 0.2s;
        }
        .form-input:focus { border-color: var(--kahoot-gold); }

        .question-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-top: 5px solid var(--kahoot-gold);
            border-radius: 16px; padding: 25px; margin-bottom: 25px;
            position: relative;
        }

        .option-row {
            display: flex; align-items: center; gap: 10px; margin-bottom: 10px;
        }

        .delete-option-btn, .delete-question-btn {
            background: none; border: none; color: rgba(255,255,255,0.5);
            font-size: 18px; cursor: pointer; padding: 5px 10px; transition: color 0.2s;
        }
        .delete-option-btn:hover, .delete-question-btn:hover { color: #ff4d4d; }

        .add-option-link {
            background: none; border: none; color: var(--kahoot-gold);
            font-weight: 600; font-size: 14px; cursor: pointer; margin-top: 5px;
            display: inline-block; font-family: 'Jost', sans-serif; text-decoration: underline;
        }

        .kahoot-btn {
            background: linear-gradient(135deg, #1368ce 0%, #0d4fa4 100%);
            color: white; font-weight: 700; font-size: 15px;
            padding: 12px 25px; border-radius: 12px; border: none; cursor: pointer;
            box-shadow: 0 4px 0 #0a3870; transition: all 0.1s ease; width: 100%;
        }
        .kahoot-btn:active { transform: translateY(2px); box-shadow: 0 2px 0 #0a3870; }

        .file-dropzone {
            border: 2px dashed rgba(255, 255, 255, 0.25);
            border-radius: 16px; padding: 30px 20px; text-align: center;
            background: rgba(255, 255, 255, 0.03); cursor: pointer;
            transition: background 0.2s;
        }
        .file-dropzone:hover { background: rgba(255, 255, 255, 0.07); }

        .extracted-preview {
            margin-top: 25px; background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 166, 2, 0.3); border-radius: 16px; padding: 20px;
            display: none;
        }
        .preview-item {
            background: rgba(255, 255, 255, 0.05); border-left: 4px solid var(--kahoot-gold);
            padding: 12px 15px; border-radius: 8px; margin-bottom: 12px; font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="bg-orbs">
        <div class="orb o1"></div>
        <div class="orb o2"></div>
    </div>

    <div class="main-container">
        <div class="dash-nav">
            <a href="{{ route('dashboard.profesor') }}" class="dash-logo">← Volver al Panel</a>
            <span style="font-weight: 600; color: rgba(255,255,255,0.8);">Creación de Cuestionario</span>
        </div>

        <div class="glass-card">
            <h2 style="font-size: 26px; font-weight: 800; margin-bottom: 5px;">Configura tu Evaluación</h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin-bottom: 25px;">Diseña preguntas interactivas estilo formulario o sube tu archivo Excel/PDF.</p>

            <div class="tab-buttons">
                <button type="button" class="tab-btn active" onclick="switchTab('manual')">✍️ Manual (Formulario)</button>
                <button type="button" class="tab-btn" onclick="switchTab('archivo')">📁 Subir Excel o PDF</button>
            </div>

            <!-- FORMULARIO MANUAL CONECTADO A BASE DE DATOS -->
            <div id="tab-manual" class="tab-content active">
                <form action="{{ route('cuestionario.store') }}" method="POST">
                    @csrf
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label class="form-label">Título del Cuestionario</label>
                        <input type="text" name="titulo" class="form-input" placeholder="Ej. Examen de Algoritmos" required>
                    </div>

                    <div id="questions-container">
                        <!-- Tarjeta de Pregunta Inicial (Index 0) -->
                        <div class="question-card" id="q-card-0">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <span style="font-weight: 700; color: var(--kahoot-gold);">Pregunta 1</span>
                                <button type="button" class="delete-question-btn" onclick="removeQuestion('q-card-0')" title="Eliminar pregunta">🗑️ Eliminar</button>
                            </div>

                            <div class="form-group">
                                <input type="text" name="preguntas[0][texto]" class="form-input" placeholder="Pregunta sin título" required>
                            </div>

                            <div class="options-container" id="options-0">
                                <div class="option-row">
                                    <span style="color: rgba(255,255,255,0.6);">⚪</span>
                                    <input type="text" name="preguntas[0][opciones][]" class="form-input" placeholder="Opción 1 (Correcta)" required style="flex:1;">
                                    <button type="button" class="delete-option-btn" onclick="removeOption(this)">✕</button>
                                </div>
                                <div class="option-row">
                                    <span style="color: rgba(255,255,255,0.6);">⚪</span>
                                    <input type="text" name="preguntas[0][opciones][]" class="form-input" placeholder="Opción 2" required style="flex:1;">
                                    <button type="button" class="delete-option-btn" onclick="removeOption(this)">✕</button>
                                </div>
                            </div>

                            <button type="button" class="add-option-link" onclick="addOption(0, 'options-0')">+ Agregar opción</button>
                        </div>
                    </div>

                    <button type="button" onclick="addQuestion()" class="kahoot-btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); box-shadow: none; margin-bottom: 20px;">+ Agregar nueva pregunta</button>
                    <button type="submit" class="kahoot-btn" style="background: linear-gradient(135deg, #46178f 0%, #2a0b5c 100%); box-shadow: 0 4px 0 #1b053d;">Guardar Cuestionario y Generar Sala</button>
                </form>
            </div>

            <!-- SUBIDA DE ARCHIVOS -->
            <div id="tab-archivo" class="tab-content">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Título del Cuestionario por Archivo</label>
                        <input type="text" id="fileTitle" class="form-input" placeholder="Ej. Encuesta de Satisfacción" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sube tu documento (Excel .xlsx o PDF .pdf)</label>
                        <div class="file-dropzone" onclick="document.getElementById('fileInput').click()">
                            <div style="font-size: 35px; margin-bottom: 8px;">📄📊</div>
                            <div id="fileLabelText" style="font-weight: 700; font-size: 15px; margin-bottom: 5px;">Haz clic para seleccionar el archivo</div>
                            <div style="font-size: 12px; color: rgba(255,255,255,0.5);">Se detectarán automáticamente las preguntas</div>
                            <input type="file" id="fileInput" name="documento" accept=".xlsx, .xls, .pdf" style="display: none;" onchange="handleFileSelect(event)">
                        </div>
                    </div>

                    <div id="extractedPreview" class="extracted-preview">
                        <h3 style="font-size: 16px; font-weight: 700; color: var(--kahoot-gold); margin-bottom: 12px;">✨ Preguntas detectadas en tu archivo:</h3>
                        <div id="previewList"></div>
                    </div>

                    <button type="submit" class="kahoot-btn" style="margin-top: 20px; background: linear-gradient(135deg, #ffa602 0%, #d98b00 100%); box-shadow: 0 4px 0 #9e6400; color: #120324;">Confirmar y Crear Cuestionario</button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            if(tab === 'manual') {
                document.querySelectorAll('.tab-btn')[0].classList.add('active');
                document.getElementById('tab-manual').classList.add('active');
            } else {
                document.querySelectorAll('.tab-btn')[1].classList.add('active');
                document.getElementById('tab-archivo').classList.add('active');
            }
        }

        let questionIndex = 0;

        // Función para agregar una nueva tarjeta de pregunta estilo Google Forms dinámicamente con los nombres correctos para Laravel
        function addQuestion() {
            questionIndex++;
            const cardId = 'q-card-' + questionIndex;
            const optionsId = 'options-' + questionIndex;

            const container = document.getElementById('questions-container');
            const card = document.createElement('div');
            card.className = 'question-card';
            card.id = cardId;
            card.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <span style="font-weight: 700; color: var(--kahoot-gold);">Pregunta ${questionIndex + 1}</span>
                    <button type="button" class="delete-question-btn" onclick="removeQuestion('${cardId}')" title="Eliminar pregunta">🗑️ Eliminar</button>
                </div>

                <div class="form-group">
                    <input type="text" name="preguntas[${questionIndex}][texto]" class="form-input" placeholder="Pregunta sin título" required>
                </div>

                <div class="options-container" id="${optionsId}">
                    <div class="option-row">
                        <span style="color: rgba(255,255,255,0.6);">⚪</span>
                        <input type="text" name="preguntas[${questionIndex}][opciones][]" class="form-input" placeholder="Opción 1" required style="flex:1;">
                        <button type="button" class="delete-option-btn" onclick="removeOption(this)">✕</button>
                    </div>
                </div>

                <button type="button" class="add-option-link" onclick="addOption(${questionIndex}, '${optionsId}')">+ Agregar opción</button>
            `;
            container.appendChild(card);
        }

        // Función para eliminar una tarjeta de pregunta completa
        function removeQuestion(cardId) {
            const card = document.getElementById(cardId);
            if(document.querySelectorAll('.question-card').length > 1) {
                card.remove();
            } else {
                alert("Debes tener al menos una pregunta en el cuestionario.");
            }
        }

        // Función para agregar una opción dentro de una pregunta específica
        function addOption(qIdx, optionsContainerId) {
            const container = document.getElementById(optionsContainerId);
            const optionCount = container.querySelectorAll('.option-row').length + 1;

            const row = document.createElement('div');
            row.className = 'option-row';
            row.innerHTML = `
                <span style="color: rgba(255,255,255,0.6);">⚪</span>
                <input type="text" name="preguntas[${qIdx}][opciones][]" class="form-input" placeholder="Opción ${optionCount}" required style="flex:1;">
                <button type="button" class="delete-option-btn" onclick="removeOption(this)">✕</button>
            `;
            container.appendChild(row);
        }

        // Función para eliminar una opción individual con la X
        function removeOption(btn) {
            const row = btn.parentElement;
            const container = row.parentElement;
            if(container.querySelectorAll('.option-row').length > 1) {
                row.remove();
            } else {
                alert("La pregunta debe tener al menos una opción.");
            }
        }

        // Simulación de lectura del Excel
        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            document.getElementById('fileLabelText').innerText = "Archivo cargado: " + file.name;

            const titleInput = document.getElementById('fileTitle');
            if(!titleInput.value) {
                let cleanName = file.name.replace(/\.[^/.]+$/, "").replace(/[_]/g, " ");
                titleInput.value = cleanName.charAt(0).toUpperCase() + cleanName.slice(1);
            }

            const previewContainer = document.getElementById('previewList');
            previewContainer.innerHTML = '';

            const realExcelQuestions = [
                { q: "¿Cuál es tu rango de edad?", opciones: "Menos de 18, 18 - 25, 26 - 35, 36 - 45, 46 - 60, Más de 60" },
                { q: "¿Cuál es tu género?", opciones: "Masculino, Femenino, Prefiero no decirlo, Otro" },
                { q: "¿Cuál es tu nivel educativo más alto alcanzado?", opciones: "Educación primaria, Educación secundaria, Técnico/Superior, Universitario, Postgrado" },
                { q: "¿Con qué frecuencia usas este producto/servicio?", opciones: "Nunca, Rara vez, A veces, Frecuentemente, Siempre" },
                { q: "¿Qué tan satisfecho/a estás en general?", opciones: "Muy insatisfecho, Insatisfecho, Neutral, Satisfecho, Muy satisfecho" }
            ];

            realExcelQuestions.forEach((item, index) => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `
                    <div style="font-weight: 700; color: #fff; margin-bottom: 4px;">P${index + 1}: ${item.q}</div>
                    <div style="font-size: 13px; color: rgba(255,255,255,0.7);">📋 Opciones detectadas: <span style="color: var(--kahoot-gold);">${item.opciones}</span></div>
                `;
                previewContainer.appendChild(div);
            });

            document.getElementById('extractedPreview').style.display = 'block';
        }
    </script>
</body>
</html>