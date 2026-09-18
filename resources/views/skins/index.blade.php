<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Skins — Kahoot 2.0</title>
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
            padding: 40px 20px;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }
        .skins-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }
        .skin-card {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: all 0.2s;
        }
        .skin-card.active { border-color: var(--kahoot-gold); background: rgba(255, 166, 2, 0.1); }
        .skin-avatar { font-size: 50px; margin-bottom: 10px; }
        .equip-btn {
            background: linear-gradient(135deg, #1368ce 0%, #0d4fa4 100%);
            color: white; font-weight: 700; font-size: 13px;
            padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer;
            box-shadow: 0 3px 0 #0a3870; margin-top: 15px; width: 100%;
            transition: transform 0.1s;
        }
        .equip-btn:active { transform: translateY(2px); }
        .equipped-text {
            font-size: 12px; color: var(--kahoot-gold); margin-top: 15px; font-weight: 700;
        }
        .back-btn { display: inline-block; color: var(--kahoot-gold); text-decoration: none; font-weight: 600; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="container">
        <a href="{{ route('dashboard.estudiante') }}" class="back-btn">← Volver al Panel</a>
        
        <div class="glass-card">
            <h2 style="font-size: 28px; font-weight: 800; margin-bottom: 5px; color: var(--kahoot-gold);">Personalizar Skins 🎨</h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 14px;">Elige tu aspecto favorito para destacar en las salas de juego grupales.</p>

            <div class="skins-grid">
                <!-- Skin 1 -->
                <div class="skin-card active" data-skin="Zorro Espacial" data-emoji="🦊">
                    <div class="skin-avatar">🦊</div>
                    <div style="font-weight: 700; font-size: 16px;">Zorro Espacial</div>
                    <div class="equipped-text">Equipado</div>
                </div>

                <!-- Skin 2 -->
                <div class="skin-card" data-skin="Cyber Bot" data-emoji="🤖">
                    <div class="skin-avatar">🤖</div>
                    <div style="font-weight: 700; font-size: 16px;">Cyber Bot</div>
                    <button class="equip-btn" onclick="equiparSkin(this)">Equipar</button>
                </div>

                <!-- Skin 3 -->
                <div class="skin-card" data-skin="Neko Gamer" data-emoji="🐱">
                    <div class="skin-avatar">🐱</div>
                    <div style="font-weight: 700; font-size: 16px;">Neko Gamer</div>
                    <button class="equip-btn" onclick="equiparSkin(this)">Equipar</button>
                </div>

                <!-- Skin 4 -->
                <div class="skin-card" data-skin="León Dorado" data-emoji="🦁">
                    <div class="skin-avatar">🦁</div>
                    <div style="font-weight: 700; font-size: 16px;">León Dorado</div>
                    <button class="equip-btn" onclick="equiparSkin(this)">Equipar</button>
                </div>

                <!-- Skin 5 -->
                <div class="skin-card" data-skin="Ninja Galáctico" data-emoji="🥷">
                    <div class="skin-avatar">🥷</div>
                    <div style="font-weight: 700; font-size: 16px;">Ninja Galáctico</div>
                    <button class="equip-btn" onclick="equiparSkin(this)">Equipar</button>
                </div>

                <!-- Skin 6 -->
                <div class="skin-card" data-skin="Astronauta" data-emoji="👨‍🚀">
                    <div class="skin-avatar">👨‍🚀</div>
                    <div style="font-weight: 700; font-size: 16px;">Astronauta</div>
                    <button class="equip-btn" onclick="equiparSkin(this)">Equipar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function equiparSkin(button) {
            // Obtener la tarjeta actual
            const selectedCard = button.closest('.skin-card');
            const skinName = selectedCard.getAttribute('data-skin');
            const skinEmoji = selectedCard.getAttribute('data-emoji');

            // Guardar en localStorage para que el panel principal lo recuerde
            localStorage.setItem('selectedSkinName', skinName);
            localStorage.setItem('selectedSkinEmoji', skinEmoji);

            // Quitar el estado activo y restaurar botones de todas las tarjetas
            document.querySelectorAll('.skin-card').forEach(card => {
                card.classList.remove('active');
                const btnOrText = card.querySelector('.equip-btn, .equipped-text');
                if (btnOrText) {
                    if (btnOrText.classList.contains('equipped-text')) {
                        // Reemplazar texto de equipado por botón de equipar
                        const parent = btnOrText.parentElement;
                        btnOrText.remove();
                        const newBtn = document.createElement('button');
                        newBtn.className = 'equip-btn';
                        newBtn.innerText = 'Equipar';
                        newBtn.onclick = function() { equiparSkin(newBtn); };
                        parent.appendChild(newBtn);
                    }
                }
            });

            // Activar la tarjeta seleccionada
            selectedCard.classList.add('active');
            button.remove();

            // Agregar texto de equipado
            const textDiv = document.createElement('div');
            textDiv.className = 'equipped-text';
            textDiv.innerText = 'Equipado';
            selectedCard.appendChild(textDiv);
        }
    </script>
</body>
</html>