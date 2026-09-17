<?php

namespace App\Services;
use App\Models\GameSession;

class GameStateService {
    public function getLiveDashboardData(GameSession $session): array {
        return [
            'status' => $session->status,
            'ranking' => $session->scores()->orderByDesc('score')->get()
        ];
    }
}
