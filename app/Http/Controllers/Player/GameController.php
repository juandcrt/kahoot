<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Game\JoinGameSessionAction;

class GameController extends Controller {
    public function showJoin() {
        return view('quiz.start');
    }

    public function join(Request $request, JoinGameSessionAction $action) {
        $request->validate(['pin' => 'required', 'nickname' => 'required']);
        $player = $action->execute($request->pin, $request->nickname);
        
        session(['player_score_id' => $player->id]);
        return redirect()->route('quiz.play');
    }
}