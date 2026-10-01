<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cuestionario — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --kahoot-purple-deep: #2a0b5c;
            --kahoot-gold: #ffa602;
            --ivory: #ffffff;
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Jost', sans-serif; background: var(--kahoot-purple-deep); color: var(--ivory); min-height: 100vh; padding: 40px 20px; }
        .main-container { max-width: 900px; margin: 0 auto; }
        .glass-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 24px; padding: 35px; margin-bottom: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.4); }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--kahoot-gold); }
        .form-input { width: 100%; background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; padding: 12px 15px; color: white; font-family: 'Jost'; font-size: 15px; outline: none; }
        .question-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.15); border-top: 5px solid var(--kahoot-gold); border-radius: 16px; padding: 25px; margin-bottom: 25px; }
        .option-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .correct-radio { transform: scale(1.3); cursor: pointer; accent-color: var(--kahoot-gold); }
        .kahoot-btn { background: linear-gradient(135deg, #1368ce 0%, #0d4fa4 100%); color: white; font-weight: 700; padding: 15px; border-radius: 12px; border: none; cursor: pointer; width: 100%; font-size: 16px; box-shadow: 0 4px 0 #0a3870; }
        .kahoot-btn:active { transform: translateY(2px); box-shadow: 0 2px 0 #0a3870; }
    </style>
</head>
<body>
    <div class="main-container">
        <div style="margin-bottom: 20px;">
            <a href="{{ route('profesor.salas') }}" style="color: var(--kahoot-gold); text-decoration: none; font-weight: 700;">← Volver a Salas</a>
        </div>
        <div class="glass-card">
            <h2 style="font-size: 26px; font-weight: 800; margin-bottom: 20px;">✏️ Editando Cuestionario</h2>
            
            <form action="{{ route('profesor.cuestionario.update', $cuestionario->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group" style="margin-bottom: 25px;">
                    <label class="form-label">Título del Cuestionario</label>
                    <input type="text" name="titulo" class="form-input" value="{{ $cuestionario->titulo }}" required>
                </div>

                <div id="questions-container">
                    @foreach($cuestionario->preguntas as $pIndex => $pregunta)
                    <div class="question-card">
                        <div style="font-weight: 700; color: var(--kahoot-gold); margin-bottom: 10px;">Pregunta {{ $pIndex + 1 }}</div>
                        <div class="form-group">
                            <label class="form-label">Texto de la Pregunta</label>
                            <input type="text" name="preguntas[{{ $pIndex }}][texto]" class="form-input" value="{{ $pregunta->pregunta }}" required>
                        </div>

                        <label class="form-label" style="margin-top: 15px;">Opciones de Respuesta (Marca la correcta)</label>
                        <div>
                            @foreach($pregunta->opciones as $oIndex => $opcion)
                            <div class="option-row">
                                <input type="radio" name="preguntas[{{ $pIndex }}][correcta]" value="{{ $oIndex }}" class="correct-radio" {{ $opcion->es_correcta ? 'checked' : '' }} required title="Marcar como correcta">
                                <input type="text" name="preguntas[{{ $pIndex }}][opciones][]" class="form-input" value="{{ $opcion->opcion }}" required style="flex:1;">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="submit" class="kahoot-btn">💾 Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>