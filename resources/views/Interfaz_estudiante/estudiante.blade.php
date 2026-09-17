<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estudiante — Kahoot 2.0</title>
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
            max-width: 1100px; margin: 0 auto; padding: 40px 20px;
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
            margin-bottom: 40px; padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .dash-logo {
            font-size: 28px; font-weight: 800; letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 30%, #ffa602 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .user-info { display: flex; align-items: center; gap: 15px; }

        .logout-btn {
            background: rgba(226, 27, 60, 0.2);
            border: 1px solid rgba(226, 27, 60, 0.4);
            color: #ff8595;
            padding: 8px 18px; border-radius: 10px;
            font-weight: 600; font-size: 14px; text-decoration: none;
            transition: all 0.2s ease;
        }
        .logout-btn:hover { background: rgba(226, 27, 60, 0.4); color: white; }

        .grid-cards {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;
            margin-top: 25px;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px; padding: 25px;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .action-card:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.25);
        }

        .action-title { font-size: 20px; font-weight: 700; margin-bottom: 8px; color: var(--kahoot-gold); }
        .action-desc { font-size: 14px; color: rgba(255, 255, 255, 0.7); line-height: 1.5; margin-bottom: 20px; }

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
        <!-- Barra de Navegación -->
        <div class="dash-nav">
            <a href="#" class="dash-logo">Kahoot! 2.0 (Estudiante)</a>
            <div class="user-info">
                <span style="font-weight: 600; font-size: 15px; color: rgba(255,255,255,0.9);">
                    Hola, {{ Auth::user()->name }}
                </span>
                <a href="{{ route('logout') }}" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>

        <!-- Bienvenida -->
        <div class="glass-card">
            <h2 style="font-size: 28px; font-weight: 800; margin-bottom: 10px;">¡Panel de Estudiante 🎓!</h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 15px; line-height: 1.6;">
                Listo para competir y sumar puntos en clase. Ingresa tu PIN de juego para unirte a una sala o personaliza tu perfil con skins exclusivas.
            </p>
        </div>

        <!-- Módulos específicos de Estudiante -->
        <div class="glass-card" style="padding-top: 25px;">
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 5px;">Acciones de Alumno</h3>
            <p style="font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 20px;">Selecciona una opción para comenzar:</p>

            <div class="grid-cards">
                <div class="action-card">
                    <div class="action-title">Unirse con PIN</div>
                    <div class="action-desc">Coloca el código que dictó tu profesor para entrar a la sala en tiempo real y competir.</div>
                    <a href="#" class="kahoot-action-btn" style="background: linear-gradient(135deg, #e21b3c 0%, #b3122e 100%); box-shadow: 0 4px 0 #800c1e;">Ingresar PIN</a>
                </div>

                <div class="action-card">
                    <div class="action-title">Personalizar Skins</div>
                    <div class="action-desc">Elige y desbloquea aspectos divertidos para destacar en tus partidas grupales.</div>
                    <a href="#" class="kahoot-action-btn" style="background: linear-gradient(135deg, #ffa602 0%, #d98b00 100%); box-shadow: 0 4px 0 #9e6400;">Mis Skins</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>