<?php

namespace App\Actions\Game;

use App\Models\GameSession;
use App\Models\PlayerScore;
use App\Events\AlumnoUnido; // 1. IMPORTANTE: Importamos el evento que creaste

class JoinGameSessionAction {
    public function execute(string $pin, string $nickname): PlayerScore {
        $session = GameSession::where('pin', $pin)->where('status', '!=', 'finished')->firstOrFail();
        
        // 2. Guardamos el resultado en una variable ($player)
        $player = PlayerScore::firstOrCreate([
            'game_session_id' => $session->id,
            'nickname' => $nickname
        ]);

        // 3. EL GRITO AL FRONTEND: Disparamos el evento por WebSockets
        // Usamos el nickname dinámico y el pin dinámico
        broadcast(new AlumnoUnido($player->nickname, $pin));

        // 4. Retornamos el jugador para que el controlador siga su flujo normal
        return $player;
    }
}