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
            --kahoot-green: #26890c;
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
            max-width: 1200px; margin: 0 auto; padding: 40px 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-top-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 30px;
            height: 100%;
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

        /* Layout de dos columnas */
        .columns-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        @media(max-width: 900px) {
            .columns-grid { grid-template-columns: 1fr; }
        }

        .sala-row {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px; padding: 20px; margin-bottom: 15px;
            display: flex; flex-direction: column; gap: 12px;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .sala-row:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .sala-header {
            display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;
        }

        .pin-badge {
            background: var(--kahoot-gold); color: #120324;
            font-weight: 800; font-size: 16px; padding: 4px 12px;
            border-radius: 10px; letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(255, 166, 2, 0.3);
        }

        .sala-actions {
            display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 5px;
        }

        .kahoot-action-btn {
            display: inline-block; text-align: center;
            background: linear-gradient(135deg, #1368ce 0%, #0d4fa4 100%);
            color: white; font-weight: 700; font-size: 13px;
            padding: 8px 14px; border-radius: 10px; text-decoration: none;
            box-shadow: 0 3px 0 #0a3870; transition: all 0.1s ease;
        }
        .kahoot-action-btn:active { transform: translateY(2px); box-shadow: 0 1px 0 #0a3870; }

        .kahoot-podium-btn {
            background: linear-gradient(135deg, #26890c 0%, #1e6d09 100%);
            box-shadow: 0 3px 0 #154d06;
        }
        .kahoot-dashboard-btn {
            background: linear-gradient(135deg, #890c89 0%, #5d055d 100%);
            box-shadow: 0 3px 0 #3d033d;
        }

        .kahoot-edit-btn {
            display: inline-flex; align-items: center; gap: 4px; text-align: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: white; font-weight: 700; font-size: 13px;
            padding: 8px 12px; border-radius: 10px; text-decoration: none;
            transition: background 0.2s;
        }
        .kahoot-edit-btn:hover { background: rgba(255, 255, 255, 0.22); }
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

        @if(session('success'))
            <div style="background: rgba(74, 222, 128, 0.2); border: 1px solid #4ade80; color: #4ade80; padding: 12px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif

        @php
            // Filtramos las salas dinámicamente en activas/en curso vs finalizadas
            $salasActivas = $salas->filter(fn($s) => $s->estado !== 'finalizada');
            $salasFinalizadas = $salas->filter(fn($s) => $s->estado === 'finalizada');
        @endphp

        <!-- LAYOUT DE 2 COLUMNAS -->
        <div class="columns-grid">

            <!-- COLUMNA IZQUIERDA: SALAS ACTIVAS / EN CURSO -->
            <div class="glass-card">
                <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 5px; color: #ffa602;">🟢 Salas Activas y en Curso</h2>
                <p style="color: rgba(255,255,255,0.7); font-size: 13px; margin-bottom: 20px;">Salas listas para iniciar o proyectar en vivo.</p>

                @if($salasActivas->isEmpty())
                    <p style="text-align: center; color: rgba(255,255,255,0.4); padding: 30px 0; font-size: 14px;">No hay salas activas en este momento.</p>
                @else
                    @foreach($salasActivas as $sala)
                        <div class="sala-row">
                            <div class="sala-header">
                                <div>
                                    <h4 style="font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 3px;">{{ $sala->cuestionario->titulo ?? 'Cuestionario' }}</h4>
                                    <span style="font-size: 12px; color: rgba(255,255,255,0.6);">Estado: <strong style="color: #ffa602;">{{ ucfirst($sala->estado) }}</strong></span>
                                </div>
                                <div class="pin-badge">{{ $sala->pin }}</div>
                            </div>

                            <div class="sala-actions">
                                <!-- Botón Editar (Solo visible porque está activa) -->
                                <a href="{{ route('profesor.cuestionario.edit', $sala->cuestionario_id) }}" class="kahoot-edit-btn" title="Editar Cuestionario">
                                    ✏️ Editar
                                </a>

                                <a href="{{ route('profesor.proyectar', $sala->id) }}" class="kahoot-action-btn">Proyectar Sala</a>
                                
                                <form action="{{ route('salas.destruir', $sala->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta sala?');" style="margin-left: auto;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: rgba(226, 27, 60, 0.2); border: 1px solid var(--kahoot-pink); color: white; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; font-weight: bold; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Eliminar sala">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- COLUMNA DERECHA: SALAS FINALIZADAS (HISTORIAL) -->
            <div class="glass-card">
                <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 5px; color: #4ade80;">📁 Historial / Finalizadas</h2>
                <p style="color: rgba(255,255,255,0.7); font-size: 13px; margin-bottom: 20px;">Cuestionarios concluidos con podios y métricas guardadas.</p>

                @if($salasFinalizadas->isEmpty())
                    <p style="text-align: center; color: rgba(255,255,255,0.4); padding: 30px 0; font-size: 14px;">Aún no hay salas finalizadas.</p>
                @else
                    @foreach($salasFinalizadas as $sala)
                        <div class="sala-row" style="border-color: rgba(74, 222, 128, 0.2);">
                            <div class="sala-header">
                                <div>
                                    <h4 style="font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 3px;">{{ $sala->cuestionario->titulo ?? 'Cuestionario' }}</h4>
                                    <span style="font-size: 12px; color: rgba(255,255,255,0.6);">Estado: <strong style="color: #4ade80;">Finalizada</strong></span>
                                </div>
                                <div class="pin-badge" style="background: rgba(255,255,255,0.15); color: #fff;">{{ $sala->pin }}</div>
                            </div>

                            <div class="sala-actions">
                                <!-- ❌ EL BOTÓN DE EDITAR YA NO APARECE AQUÍ -->
                                <a href="{{ route('profesor.podio', $sala->id) }}" class="kahoot-action-btn kahoot-podium-btn">🏆 Ver Podio</a>
                                <a href="{{ route('profesor.dashboard', $sala->id) }}" class="kahoot-action-btn kahoot-dashboard-btn">📊 Métricas</a>
                                
                                <form action="{{ route('salas.destruir', $sala->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este historial?');" style="margin-left: auto;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: rgba(226, 27, 60, 0.2); border: 1px solid var(--kahoot-pink); color: white; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; font-weight: bold; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Eliminar sala">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>
    </div>

</body>
</html>