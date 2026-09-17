<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Kahoot 2.0</title>
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

        /* ── ORBES FLOTANTES ATMOSFÉRICOS (Inspirados en tu diseño) ── */
        .bg-orbs {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background: radial-gradient(circle at 50% 20%, #6830cc 0%, #2a0b5c 60%, #120324 100%);
            overflow: hidden;
        }
        .orb {
            position: absolute; border-radius: 50%; filter: blur(70px); opacity: 0.5;
            animation: floatOrb 15s ease-in-out infinite alternate;
        }
        .o1 { width: 450px; height: 450px; top: -10%; left: -10%; background: rgba(226, 27, 60, 0.35); }
        .o2 { width: 500px; height: 500px; bottom: -15%; right: -10%; background: rgba(19, 104, 206, 0.35); animation-delay: -5s; }
        .o3 { width: 350px; height: 350px; top: 30%; right: 20%; background: rgba(255, 166, 2, 0.25); animation-delay: -10s; }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -40px) scale(1.08); }
            100% { transform: translate(-20px, 30px) scale(0.95); }
        }

        /* ── TARJETA DE CRISTAL LÍQUIDO (Glassmorphism) ── */
        .glass-card {
            position: relative; z-index: 10;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-top-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            width: 100%;
            max-width: 420px;
            padding: 40px 36px;
        }

        /* ── INPUTS ESTILO CRISTAL ── */
        .glass-input {
            background: rgba(255, 255, 255, 0.07);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: white;
            font-family: 'Jost', sans-serif;
            font-size: 15px;
            font-weight: 500;
            width: 100%;
            padding: 14px 18px;
            transition: all 0.3s ease;
            outline: none;
        }
        .glass-input::placeholder { color: rgba(255, 255, 255, 0.4); }
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--kahoot-gold);
            box-shadow: 0 0 0 4px rgba(255, 166, 2, 0.2);
        }

        /* ── BOTÓN 3D ESTILO KAHOOT CON TOQUE PREMIUM ── */
        .kahoot-btn {
            background: linear-gradient(135deg, #1368ce 0%, #0d4fa4 100%);
            color: white;
            font-family: 'Jost', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 12px;
            border: none;
            width: 100%;
            padding: 14px;
            cursor: pointer;
            box-shadow: 0 6px 0 #0a3870, 0 10px 20px rgba(0,0,0,0.3);
            transition: all 0.15s ease;
        }
        .kahoot-btn:hover {
            filter: brightness(1.1);
        }
        .kahoot-btn:active {
            transform: translateY(4px);
            box-shadow: 0 2px 0 #0a3870, 0 5px 10px rgba(0,0,0,0.3);
        }

        .form-label {
            display: block; font-size: 13px; font-weight: 600; 
            letter-spacing: 0.5px; color: rgba(255, 255, 255, 0.8); margin-bottom: 8px;
        }
    </style>
</head>
<body>

    <!-- Fondo con orbes animados -->
    <div class="bg-orbs">
        <div class="orb o1"></div>
        <div class="orb o2"></div>
        <div class="orb o3"></div>
    </div>

    <!-- Tarjeta Centralizada tipo Glassmorphism -->
    <div class="glass-card">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="font-size: 42px; font-weight: 800; letter-spacing: -1px; background: linear-gradient(135deg, #fff 30%, #ffa602 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0 0 4px 0;">Kahoot!</h1>
            <p style="color: rgba(255,255,255,0.6); font-weight: 400; font-size: 14px; margin: 0;">Plataforma Educativa 2.0</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div style="margin-bottom: 20px;">
                <label for="email" class="form-label">Correo institucional</label>
                <input id="email" class="glass-input" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="tucorreo@senati.pe" />
                <x-input-error :messages="$errors->get('email')" style="color: #ff6b81; font-weight: 600; font-size: 12px; margin-top: 6px;" />
            </div>

            <!-- Password -->
            <div style="margin-bottom: 20px;">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" class="glass-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" style="color: #ff6b81; font-weight: 600; font-size: 12px; margin-top: 6px;" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; font-size: 13px;">
                <label for="remember_me" style="display: flex; align-items: center; cursor: pointer; color: rgba(255,255,255,0.8); font-weight: 500;">
                    <input id="remember_me" type="checkbox" name="remember" style="margin-right: 8px; accent-color: var(--kahoot-gold);">
                    Recordarme
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request')" style="font-weight: 600; color: #ffa602; text-decoration: none; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">
                        ¿Olvidaste tu clave?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="kahoot-btn">
                    Ingresar al Sistema
                </button>
            </div>
        </form>
    </div>

</body>
</html>