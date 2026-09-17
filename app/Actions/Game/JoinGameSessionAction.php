<?php

namespace App\Actions\Game;
use App\Models\GameSession;
use App\Models\PlayerScore;

class JoinGameSessionAction {
    public function execute(string $pin, string $nickname): PlayerScore {
        $session = GameSession::where('pin', $pin)->where('status', '!=', 'finished')->firstOrFail();
        
        return PlayerScore::firstOrCreate([
            'game_session_id' => $session->id,
            'nickname' => $nickname
        ]);
    }
}