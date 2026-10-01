<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podio Final — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root { --kahoot-purple-deep: #2a0b5c; --kahoot-gold: #ffa602; --ivory: #ffffff; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Jost', sans-serif; background: linear-gradient(135deg, var(--kahoot-purple-deep), #4a148c); color: var(--ivory); min-height: 100vh; padding: 40px; }
        
        .podium-container { display: flex; justify-content: center; align-items: flex-end; gap: 20px; margin: 50px 0; height: 300px; }
        .podium-place { display: flex; flex-direction: column; align-items: center; width: 160px; animation: popUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; opacity: 0; transform: translateY(50px); }
        .podium-name { font-size: 22px; font-weight: 800; margin-bottom: 10px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        .podium-points { font-size: 18px; font-weight: 700; margin-top: 10px; background: rgba(0,0,0,0.3); padding: 5px 15px; border-radius: 20px; }
        
        .step { width: 100%; border-radius: 12px 12px 0 0; display: flex; justify-content: center; padding-top: 15px; font-size: 50px; font-weight: 800; color: rgba(0,0,0,0.3); box-shadow: 0 -10px 20px rgba(0,0,0,0.3); }
        .step-1 { height: 200px; background: linear-gradient(180deg, #FFDF00 0%, #D4AF37 100%); animation-delay: 0.6s; }
        .step-2 { height: 140px; background: linear-gradient(180deg, #E0E0E0 0%, #A9A9A9 100%); animation-delay: 0.3s; }
        .step-3 { height: 100px; background: linear-gradient(180deg, #CD7F32 0%, #A0522D 100%); animation-delay: 0s; }

        .table-container { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 30px; border-radius: 16px; max-width: 900px; margin: 0 auto; border: 1px solid rgba(255,255,255,0.2); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); font-size: 18px; }
        th { color: var(--kahoot-gold); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        td:nth-child(2) { text-align: left; font-weight: 700; }
        
        .btn-home { background: var(--kahoot-gold); color: #000; border: none; padding: 15px 30px; font-size: 18px; font-weight: 800; border-radius: 12px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 30px; transition: transform 0.2s; }
        .btn-home:hover { transform: scale(1.05); }

        @keyframes popUp { to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div style="text-align: center; max-width: 1000px; margin: 0 auto;">
        <h1 style="font-size: 48px; font-weight: 800; color: var(--kahoot-gold); margin-bottom: 10px;">Podio de Ganadores 🏆</h1>
        <p style="font-size: 22px; color: rgba(255,255,255,0.8); font-weight: 600;">{{ $sala->cuestionario->titulo }}</p>

        <div class="podium-container">
            @if(isset($resultados[1]))
            <div class="podium-place" style="animation-delay: 0.3s;">
                <div class="podium-name" style="color: #E0E0E0;">{{ $resultados[1]['nombre'] }}</div>
                <div class="step step-2">2</div>
                <div class="podium-points">{{ $resultados[1]['puntaje'] }} pts</div>
            </div>
            @endif

            @if(isset($resultados[0]))
            <div class="podium-place" style="animation-delay: 0.6s; z-index: 10;">
                <div class="podium-name" style="font-size: 28px; color: #FFDF00;">{{ $resultados[0]['nombre'] }}</div>
                <div class="step step-1">1</div>
                <div class="podium-points">{{ $resultados[0]['puntaje'] }} pts</div>
            </div>
            @endif

            @if(isset($resultados[2]))
            <div class="podium-place" style="animation-delay: 0s;">
                <div class="podium-name" style="color: #CD7F32;">{{ $resultados[2]['nombre'] }}</div>
                <div class="step step-3">3</div>
                <div class="podium-points">{{ $resultados[2]['puntaje'] }} pts</div>
            </div>
            @endif
        </div>

        <div class="table-container">
            <h2 style="margin-bottom: 10px; font-size: 24px;">Tabla Completa de Posiciones</h2>
            <table>
                <thead>
                    <tr>
                        <th>Pos</th>
                        <th>Alumno</th>
                        <th>Aciertos</th>
                        <th>Tiempo Prom.</th>
                        <th>Puntos Totales</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resultados as $index => $res)
                    <tr style="{{ $index === 0 ? 'background: rgba(255, 215, 0, 0.1);' : '' }}">
                        <td style="font-weight: 800; color: {{ $index === 0 ? '#FFD700' : ($index === 1 ? '#C0C0C0' : ($index === 2 ? '#CD7F32' : 'white')) }};">#{{ $index + 1 }}</td>
                        <td>{{ $res['nombre'] }}</td>
                        <td>{{ $res['correctas'] }} / {{ $sala->cuestionario->preguntas->count() }}</td>
                        <td>{{ $res['tiempo_promedio'] }} s</td>
                        <td style="color: var(--kahoot-gold); font-weight: 800;">{{ $res['puntaje'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="display: flex; justify-content: center; gap: 20px;">
                <a href="{{ route('profesor.dashboard', $sala->id) }}" class="btn-home" style="background: white; color: #2a0b5c;">Ver Estadísticas Detalladas 📊</a>
                <a href="{{ route('dashboard.profesor') }}" class="btn-home">Volver al Panel de Control</a>
            </div>
        </div>
    </div>
</body>
</html>