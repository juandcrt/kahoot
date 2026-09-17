<?php

namespace App\Actions\Game;

class SubmitAnswerAction {
    public function execute($playerScore, $question, $option, int $points) {
        $playerScore->increment('score', $points);
        return true;
    }
}