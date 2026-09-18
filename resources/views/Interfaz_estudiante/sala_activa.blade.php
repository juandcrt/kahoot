<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala Activa — Kahoot 2.0</title>
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
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }
        .back-btn {
            display: inline-block;
            margin-top: 25px;
            background: rgba(226, 27, 60, 0.2);
            border: 1px solid rgba(226, 27, 60, 0.4);
            color: #ff8595;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }
        .back-btn:hover { background: rgba(226, 27, 60, 0.4); color: white; }
    </style>
</head>
<body>

    <div class="glass-card">
        <h2 style="font-size: 28px; font-weight: 800; color: var(--kahoot-gold); margin-bottom: 10px;">¡Conectado a la Sala! 🚀</h2>
        <p style="color: rgba(255,255,255,0.8); font-size: 15px; margin-bottom: 20px;">
            Estás dentro de la sala con PIN: <strong style="color: var(--kahoot-gold);">{{ $sala->pin }}</strong>
        </p>
        <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; color: rgba(255,255,255,0.7);">
            Esperando a que el profesor inicie el cuestionario en tiempo real...
        </div>
        <a href="{{ route('dashboard.estudiante') }}" class="back-btn">Salir de la Sala</a>
    </div>

</body>
</html>