<?php

namespace App\Actions\Game;

use App\Events\PlayerAnswered; // O la clase de evento que estés usando

class SubmitAnswerAction {
    public function execute($playerScore, $question, $option, int $points) {
        $playerScore->increment('score', $points);

        // AQUÍ ESTÁ LO QUE FALTA: Disparar el evento por WebSockets
        // (Asegúrate de ajustar los parámetros según lo que reciba tu evento PlayerAnswered)
        broadcast(new PlayerAnswered($playerScore, $question, $option));

        return true;
    }
}