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
            background: linear-gradient(135deg, var(--kahoot-purple-deep), #4a148c);
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
        .pulse-text {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 16px;
            font-weight: 500;
            color: rgba(255,255,255,0.9);
            border: 1px solid rgba(255,255,255,0.1);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.1); }
            70% { box-shadow: 0 0 0 15px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }
        .back-btn {
            display: inline-block;
            width: 100%;
            background: rgba(226, 27, 60, 0.2);
            border: 1px solid rgba(226, 27, 60, 0.4);
            color: #ff8595;
            padding: 15px 20px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .back-btn:hover { 
            background: rgba(226, 27, 60, 0.5); 
            color: white; 
            transform: translateY(-2px);
        }
        .back-btn:active { 
            transform: translateY(2px); 
        }
    </style>
</head>
<body>

    <div class="glass-card">
        <h2 style="font-size: 32px; font-weight: 800; color: var(--kahoot-gold); margin-bottom: 15px;">¡Conectado a la Sala! 🚀</h2>
        
        <p style="color: rgba(255,255,255,0.8); font-size: 18px; margin-bottom: 25px;">
            Estás dentro de la sala con PIN: <strong style="color: var(--kahoot-gold); font-size: 22px; letter-spacing: 2px;">{{ $sala->pin }}</strong>
        </p>
        
        <!-- Animación de pulso para indicar que el sistema está esperando -->
        <div class="pulse-text">
            ⏳ Esperando a que el profesor inicie el cuestionario en tiempo real...
        </div>

        <!-- FORMULARIO DE SALIDA QUE ENVÍA EL PIN POR POST -->
        <form action="{{ route('estudiante.salir') }}" method="POST">
            @csrf
            <input type="hidden" name="pin" value="{{ $sala->pin }}">
            <button type="submit" class="back-btn">Salir de la Sala</button>
        </form>
    </div>

    <!-- SCRIPT DE WEBSOCKETS PARA ESCUCHAR EL INICIO DE LA PARTIDA -->
    <script type="module">
        const pinSala = "{{ $sala->pin }}";
        console.log("Escuchando inicio de partida en sala." + pinSala);

        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel(`sala.${pinSala}`)
                .listen('.PartidaIniciada', (evento) => {
                    console.log("¡El profesor ha dado inicio al cuestionario!");
                    window.location.href = `/dashboard/estudiante/juego/${pinSala}`;
                })
                .listen('PartidaIniciada', (evento) => {
                    console.log("¡El profesor ha dado inicio al cuestionario!");
                    window.location.href = `/dashboard/estudiante/juego/${pinSala}`;
                });
        } else {
            console.error("Laravel Echo no está definido. Asegúrate de compilar con 'npm run dev' y tener Reverb encendido.");
        }
    </script>

</body>
</html>