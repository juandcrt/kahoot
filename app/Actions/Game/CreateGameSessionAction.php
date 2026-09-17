<?php

namespace App\Actions\Game;
use App\Models\GameSession;

class CreateGameSessionAction {
    public function execute(): GameSession {
        return GameSession::create([
            'pin' => (string) rand(100000, 999999),
            'status' => 'waiting'
        ]);
    }
}