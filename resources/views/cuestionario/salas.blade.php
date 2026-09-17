<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas Activas — Kahoot 2.0</title>
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
        .o3 { width: 450px; height: 450px; top: 40%; right: 15%; background: rgba(255, 166, 2, 0.2); animation-delay: -10s; }

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

        .sala-row {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px; padding: 20px; margin-bottom: 15px;
            display: flex; justify-content: space-between; align-items: center;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .sala-row:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .pin-badge {
            background: var(--kahoot-gold); color: #120324;
            font-weight: 800; font-size: 20px; padding: 8px 16px;
            border-radius: 12px; letter-spacing: 2px;
            box-shadow: 0 4px 10px rgba(255, 166, 2, 0.3);
        }

        .kahoot-action-btn {
            display: inline-block; text-align: center;
            background: linear-gradient(135deg, #1368ce 0%, #0d4fa4 100%);
            color: white; font-weight: 700; font-size: 14px;
            padding: 10px 20px; border-radius: 10px; text-decoration: none;
            box-shadow: 0 4px 0 #0a3870; transition: all 0.1s ease;
        }
        .kahoot-action-btn:active { transform: translateY(2px); box-shadow: 0 2px 0 #0a3870; }
    </style>
</head>
<body>

    <div class="bg-orbs">
        <div class="orb o1"></div>
        <div class="orb o2"></div>
        <div class="orb o3"></div>
    </div>

    <div class="main-container">
        <div class="dash-nav">
            <a href="{{ route('dashboard.profesor') }}" class="dash-logo">← Volver al Panel</a>
            <span style="font-weight: 600; color: rgba(255,255,255,0.8);">Gestión de Salas</span>
        </div>

        <div class="glass-card">
            <h2 style="font-size: 26px; font-weight: 800; margin-bottom: 5px;">Salas Activas y PINes</h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin-bottom: 25px;">Comparte el PIN de 6 dígitos con tus alumnos para que puedan ingresar desde su panel.</p>

            @if($salas->isEmpty())
                <p style="text-align: center; color: rgba(255,255,255,0.5); padding: 40px 0;">No hay salas activas todavía. Crea un cuestionario para generar tu primer PIN.</p>
            @else
                @foreach($salas as $sala)
                    <div class="sala-row">
                        <div>
                            <h4 style="font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 5px;">{{ $sala->cuestionario->titulo }}</h4>
                            <span style="font-size: 13px; color: rgba(255,255,255,0.6);">Estado: <strong style="color: #4ade80;">{{ ucfirst($sala->estado) }}</strong></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <div class="pin-badge">{{ $sala->pin }}</div>
                            <a href="#" class="kahoot-action-btn">Proyectar Sala</a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

</body>
</html>