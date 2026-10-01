<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analítico — Kahoot 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Jost',sans-serif; background:linear-gradient(135deg,#2a0b5c,#4a148c); color:#fff; min-height:100vh; padding:30px; }
        .max { max-width:1000px; margin:0 auto; }
        .top { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; gap:10px; flex-wrap:wrap; }
        .back { color:#ffa602; text-decoration:none; font-weight:600; background:rgba(255,255,255,.05); padding:8px 16px; border-radius:10px; border:1px solid rgba(255,255,255,.1); }
        h1 { text-align:center; font-size:36px; font-weight:800; color:#ffa602; margin-bottom:25px; }
        .cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:15px; margin-bottom:25px; }
        .card { background:rgba(255,255,255,.95); color:#333; border-radius:16px; padding:20px; text-align:center; font-weight:600; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .card .n { font-size:36px; font-weight:800; color:#e21b3c; }
        .glass { background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.18); backdrop-filter: blur(15px); border-radius:20px; padding:25px; margin-bottom:20px; }
        .glass h2 { font-size:22px; margin-bottom:5px; }
        .ok { border-left:6px solid #26890c; }
        .mal { border-left:6px solid #e21b3c; }
        .pct { font-size:15px; color:rgba(255,255,255,.85); margin-bottom:12px; }
        .fila { display:flex; justify-content:space-between; gap:10px; padding:8px 0; border-top:1px solid rgba(255,255,255,.1); font-size:15px; align-items: center; }
        .fila small { color:rgba(255,255,255,.6); }
        .barra { background:rgba(255,255,255,.15); border-radius:20px; height:12px; margin-top:6px; overflow:hidden; }
        .barra div { height:100%; background:#ffa602; transition: width 0.4s ease; }
        .barra.rojo div { background:#e21b3c; }
        .barra.verde div { background:#26890c; }
        .q { margin-bottom:14px; }
        .sub { font-weight:700; margin:15px 0 8px; color:#ffa602; }
    </style>
</head>
<body>
<div class="max">
    <div class="top">
        <a href="{{ route('profesor.salas') }}" class="back">← Volver a Salas</a>
        <a href="{{ route('profesor.podio', $sala->id) }}" class="back">🏆 Ver Podio</a>
    </div>

    <h1>Dashboard: {{ $sala->cuestionario->titulo ?? 'Cuestionario' }}</h1>

    <div class="cards">
        <div class="card"><div class="n">{{ $resumen['alumnos'] }}</div>Alumnos Participantes</div>
        <div class="card"><div class="n">{{ $resumen['acierto_global'] }}%</div>Acierto Global</div>
        <div class="card"><div class="n">{{ $resumen['tiempo_promedio'] }} s</div>Tiempo Promedio</div>
    </div>

    @if(!$mejor)
        <div class="glass"><h2>Aún no hay respuestas registradas en esta sala.</h2></div>
    @else
        <div class="glass ok">
            <h2>✅ Pregunta más acertada (P{{ $mejor['numero'] }})</h2>
            <div class="pct">{{ $mejor['texto'] }} — <strong>{{ $mejor['porcentaje'] }}% de acierto</strong> ({{ $mejor['aciertos'] }}/{{ $mejor['total'] }} alumnos)</div>
            @foreach($mejor['detalle'] as $d)
                <div class="fila">
                    <span>{{ $d['correcta'] ? '✔️' : '❌' }} {{ $d['nombre'] }} <small>— marcó: {{ $d['marco'] }}</small></span>
                    <strong>⏱ {{ $d['tiempo'] }} s</strong>
                </div>
            @endforeach
        </div>

        <div class="glass mal">
            <h2>⚠️ Pregunta menos acertada (P{{ $peor['numero'] }})</h2>
            <div class="pct">{{ $peor['texto'] }} — <strong>{{ $peor['porcentaje'] }}% de acierto</strong> ({{ $peor['aciertos'] }}/{{ $peor['total'] }} alumnos). Ideal para reforzar en clase.</div>

            <div class="sub">¿Qué opciones marcaron los alumnos?</div>
            @foreach($peor['opciones'] as $o)
                <div class="q">
                    <div style="display:flex;justify-content:space-between;">
                        <span>{{ $o['correcta'] ? '✔️ [Correcta]' : '' }} {{ $o['texto'] }}</span>
                        <strong>{{ $o['votos'] }} votos</strong>
                    </div>
                    <div class="barra {{ $o['correcta'] ? 'verde' : 'rojo' }}">
                        <div style="width:{{ $peor['total'] > 0 ? round($o['votos'] * 100 / $peor['total']) : 0 }}%"></div>
                    </div>
                </div>
            @endforeach

            <div class="sub">Tiempos individuales de los alumnos en esta pregunta</div>
            @foreach($peor['detalle'] as $d)
                <div class="fila">
                    <span>{{ $d['correcta'] ? '✔️' : '❌' }} {{ $d['nombre'] }} <small>— marcó: {{ $d['marco'] }}</small></span>
                    <strong>⏱ {{ $d['tiempo'] }} s</strong>
                </div>
            @endforeach
        </div>
    @endif

    <div class="glass">
        <h2 style="margin-bottom:15px;">📊 Porcentaje de acierto global por pregunta</h2>
        @foreach($preguntas as $p)
            <div class="q">
                <div style="display:flex;justify-content:space-between;gap:10px;">
                    <span>P{{ $p['numero'] }}: {{ \Illuminate\Support\Str::limit($p['texto'], 65) }}</span>
                    <strong>{{ $p['porcentaje'] }}%</strong>
                </div>
                <div class="barra"><div style="width:{{ $p['porcentaje'] }}%"></div></div>
            </div>
        @endforeach
    </div>
</div>
</body>
</html>