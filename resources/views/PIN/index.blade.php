<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unirse a Sala — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --kahoot-purple-deep: #2a0b5c;
            --kahoot-pink: #e21b3c;
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
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }
        .pin-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 15px;
            color: white;
            font-size: 24px;
            text-align: center;
            font-weight: 700;
            letter-spacing: 4px;
            margin-bottom: 20px;
            outline: none;
            transition: border-color 0.2s;
        }
        .pin-input:focus { border-color: var(--kahoot-gold); }
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #e21b3c 0%, #a8122a 100%);
            color: white;
            font-weight: 700;
            font-size: 16px;
            padding: 14px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 0 #700a1b;
            transition: transform 0.1s;
        }
        .submit-btn:active { transform: translateY(2px); box-shadow: 0 2px 0 #700a1b; }
        .back-link { display: inline-block; margin-top: 20px; color: rgba(255, 255, 255, 0.7); text-decoration: none; font-size: 14px; }
        .back-link:hover { color: white; }
    </style>
</head>
<body>

    <div class="glass-card">
        <h2 style="font-size: 26px; font-weight: 800; margin-bottom: 8px; color: var(--kahoot-gold);">Ingresar PIN</h2>
        <p style="font-size: 14px; color: rgba(255,255,255,0.7); margin-bottom: 25px;">Digita el código proporcionado por tu profesor.</p>

        @if($errors->any())
            <div style="background: rgba(226, 27, 60, 0.3); border: 1px solid rgba(226, 27, 60, 0.5); padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 15px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('estudiante.unirse') }}" method="POST">
            @csrf
            <input type="text" name="pin" class="pin-input" placeholder="123456" maxlength="6" required autocomplete="off">
            <button type="submit" class="submit-btn">Entrar al Juego</button>
        </form>

        <a href="{{ route('dashboard.estudiante') }}" class="back-link">← Volver al Panel</a>
    </div>

</body>
</html>