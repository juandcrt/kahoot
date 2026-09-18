<x-app-layout>
    <div class="glass-card">
        <h2 style="font-size: 28px; font-weight: 800; margin-bottom: 10px; color: #ffa602;">¡Bienvenido al Panel de Estudiante!</h2>
        <p style="color: rgba(255,255,255,0.7); font-size: 15px; line-height: 1.6;">
            Has iniciado sesión correctamente. Desde aquí puedes unirte a las salas de juego interactivas con tu PIN o personalizar los aspectos de tu personaje para destacar en el aula.
        </p>
    </div>

    <div class="glass-card" style="padding-top: 25px; margin-top: 20px;">
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 5px;">Acciones de Alumno</h3>
        <p style="font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 20px;">Selecciona una opción para comenzar:</p>

        <div class="grid-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
            
            <!-- Módulo: Unirse con PIN -->
            <div class="action-card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 25px;">
                <div class="action-title" style="font-size: 18px; font-weight: 700; color: #ffa602; margin-bottom: 8px;">Unirse con PIN</div>
                <div class="action-desc" style="font-size: 13px; color: rgba(255,255,255,0.7); margin-bottom: 20px;">Coloca el código que dictó tu profesor para entrar a la sala en tiempo real y competir.</div>
                <a href="{{ route('estudiante.pin') }}" class="kahoot-action-btn" style="display: inline-block; text-align: center; width: 100%; background: linear-gradient(135deg, #e21b3c 0%, #b3122e 100%); color: white; font-weight: 700; padding: 12px; border-radius: 10px; text-decoration: none; box-shadow: 0 4px 0 #800c1e;">Ingresar PIN</a>
            </div>

            <!-- Módulo: Personalizar Skins -->
            <div class="action-card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 25px;">
                <div class="action-title" style="font-size: 18px; font-weight: 700; color: #ffa602; margin-bottom: 8px;">Personalizar Skins</div>
                <div class="action-desc" style="font-size: 13px; color: rgba(255,255,255,0.7); margin-bottom: 20px;">Elige y desbloquea aspectos divertidos para destacar en tus partidas grupales.</div>
                <a href="{{ route('estudiante.skins') }}" class="kahoot-action-btn" style="display: inline-block; text-align: center; width: 100%; background: linear-gradient(135deg, #ffa602 0%, #d98c00 100%); color: #2a0b5c; font-weight: 700; padding: 12px; border-radius: 10px; text-decoration: none; box-shadow: 0 4px 0 #a66a00;">Mis Skins</a>
            </div>

        </div>
    </div>
</x-app-layout>