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
            padding: 30px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pin-display {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 20px 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .main-projector {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }
        .back-btn {
            color: var(--kahoot-gold);
            text-decoration: none;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.05);
            padding: 8px 16px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .back-btn:hover { background: rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body>

    <!-- Barra Superior -->
    <div class="top-bar">
        <a href="{{ route('profesor.salas') }}" class="back-btn">← Volver a Salas</a>
        <div style="font-weight: 700; font-size: 18px; color: var(--kahoot-gold);">Kahoot! Proyector 📊</div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-projector">
        <div class="glass-card">
            <h2 style="font-size: 36px; font-weight: 800; margin-bottom: 10px; color: var(--ivory);">
                {{ $sala->cuestionario->titulo ?? 'Cuestionario Activo' }}
            </h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 16px; margin-bottom: 30px;">
                Ingresa desde tu dispositivo móvil o panel con el siguiente código PIN:
            </p>

            <!-- Caja Gigante de PIN -->
            <div class="pin-display">
                <div style="font-size: 14px; color: rgba(255,255,255,0.6); font-weight: 600; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px;">PIN de la Sala</div>
                <div style="font-size: 64px; font-weight: 800; color: var(--kahoot-gold); letter-spacing: 8px;">{{ $sala->pin }}</div>
            </div>

            <div style="margin-top: 40px; font-size: 15px; color: rgba(255,255,255,0.7);">
                Alumnos en la sala: <strong style="color: var(--kahoot-gold); font-size: 18px;">0</strong> (Esperando conexiones...)
            </div>
        </div>
    </div>

    <!-- Pie de página vacío para centrar balance -->
    <div></div>

</body>
</html>