<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecciona tu Rol — Kahoot 2.0</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .bg-orbs {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background: radial-gradient(circle at 50% 20%, #6830cc 0%, #2a0b5c 60%, #120324 100%);
            overflow: hidden;
        }
        .orb {
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.5;
            animation: floatOrb 15s ease-in-out infinite alternate;
        }
        .o1 { width: 500px; height: 500px; top: -10%; left: -10%; background: rgba(226, 27, 60, 0.35); }
        .o2 { width: 550px; height: 550px; bottom: -15%; right: -10%; background: rgba(19, 104, 206, 0.35); animation-delay: -5s; }
        .o3 { width: 400px; height: 400px; top: 35%; right: 20%; background: rgba(255, 166, 2, 0.25); animation-delay: -10s; }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(35px, -45px) scale(1.08); }
            100% { transform: translate(-25px, 35px) scale(0.95); }
        }

        .selector-container {
            position: relative; z-index: 10;
            width: 100%; max-width: 800px; padding: 20px;
            text-align: center;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-top-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border-radius: 28px;
            padding: 45px 35px;
        }

        .roles-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;
            margin-top: 35px;
        }

        .role-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px; padding: 30px 20px;
            text-decoration: none; color: white;
            display: flex; flex-direction: column; align-items: center;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .role-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--kahoot-gold);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }

        .role-icon { font-size: 50px; margin-bottom: 15px; }
        .role-title { font-size: 22px; font-weight: 700; margin-bottom: 8px; color: var(--ivory); }
        .role-desc { font-size: 13px; color: rgba(255, 255, 255, 0.6); line-height: 1.5; margin-bottom: 25px; }

        .role-btn {
            background: linear-gradient(135deg, #1368ce 0%, #0d4fa4 100%);
            color: white; font-weight: 700; font-size: 14px;
            padding: 10px 24px; border-radius: 12px;
            box-shadow: 0 4px 0 #0a3870; transition: all 0.1s ease;
            width: 100%; text-align: center;
        }
        .role-card:hover .role-btn { filter: brightness(1.1); }
    </style>
</head>
<body>

    <div class="bg-orbs">
        <div class="orb o1"></div>
        <div class="orb o2"></div>
        <div class="orb o3"></div>
    </div>

    <div class="selector-container">
        <div class="glass-card">
            <h1 style="font-size: 44px; font-weight: 800; letter-spacing: -1px; background: linear-gradient(135deg, #fff 30%, #ffa602 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 8px;">Kahoot! 2.0</h1>
            <p style="color: rgba(255,255,255,0.7); font-size: 15px; font-weight: 400;">Elige cómo deseas ingresar a la plataforma educativa</p>

            <div class="roles-grid">
                <!-- Redirige directo al login formal -->
                <a href="{{ route('login') }}" class="role-card">
                    <div class="role-icon">👨‍🏫</div>
                    <div class="role-title">Docente / Admin</div>
                    <div class="role-desc">Crea cuestionarios, sube material en PDF y administra el rendimiento de tus estudiantes.</div>
                    <div class="role-btn" style="background: linear-gradient(135deg, #46178f 0%, #2a0b5c 100%); box-shadow: 0 4px 0 #1b053d;">Ingresar como Docente</div>
                </a>

                <a href="{{ route('login') }}" class="role-card">
                    <div class="role-icon">🎓</div>
                    <div class="role-title">Estudiante</div>
                    <div class="role-desc">Únete a salas interactivas mediante PIN, compite en tiempo real y personaliza tus skins.</div>
                    <div class="role-btn" style="background: linear-gradient(135deg, #e21b3c 0%, #b3122e 100%); box-shadow: 0 4px 0 #800c1e;">Ingresar como Estudiante</div>
                </a>
            </div>
        </div>
    </div>

</body>
</html>