<x-app-layout>
    <div class="glass-card">
        <h2 style="font-size: 28px; font-weight: 800; margin-bottom: 10px;">¡Bienvenido al Panel de Control!</h2>
        <p style="color: rgba(255,255,255,0.7); font-size: 15px; line-height: 1.6;">
            Has iniciado sesión correctamente. Desde aquí podrás gestionar las salas de juego, subir material en PDF o unirte como estudiante para competir en los rankings por salón.
        </p>
    </div>

    <div class="glass-card" style="padding-top: 25px;">
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 5px;">Módulos del Sistema</h3>
        <p style="font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 20px;">Selecciona una opción para comenzar a probar las herramientas:</p>

        <div class="grid-cards">
            <div class="action-card">
                <div class="action-title">Gestión de Cuestionarios</div>
                <div class="action-desc">Sube temas en PDF para generar preguntas automáticas o créalas de forma manual estilo formulario.</div>
                <a href="#" class="kahoot-action-btn">Crear Cuestionario</a>
            </div>

            <div class="action-card">
                <div class="action-title">Unirse a Partida (PIN)</div>
                <div class="action-desc">Ingresa el código PIN proporcionado por tu profesor para unirte a la sala en tiempo real y sumar puntos.</div>
                <a href="#" class="kahoot-action-btn" style="background: linear-gradient(135deg, #e21b3c 0%, #b3122e 100%); box-shadow: 0 4px 0 #800c1e;">Ingresar PIN</a>
            </div>
        </div>
    </div>
</x-app-layout>