<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Actions\Game\CreateGameSessionAction;
use App\Models\GameSession;
use App\Services\GameStateService;

class GameController extends Controller {
    public function dashboard(CreateGameSessionAction $action) {
        $session = GameSession::firstOrCreate(['status' => 'waiting'], [
            'pin' => rand(100000, 999999)
        ]);
        return view('teacher.dashboard', compact('session'));
    }

    public function data(GameSession $session, GameStateService $stateService) {
        return response()->json($stateService->getLiveDashboardData($session));
    }
}