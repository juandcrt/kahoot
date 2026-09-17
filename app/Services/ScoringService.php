<?php

namespace App\Services;
use App\Support\Scoring\KahootScoreFormula;

class ScoringService {
    protected KahootScoreFormula $formula;

    public function __construct(KahootScoreFormula $formula) {
        $this->formula = $formula;
    }

    public function calculateScore(bool $isCorrect, int $timeRemaining, int $totalTime): int {
        return $this->formula->calculate($isCorrect, $timeRemaining, $totalTime);
    }
}