<?php

namespace App\Support\Scoring;

class KahootScoreFormula {
    public function calculate(bool $isCorrect, int $timeRemaining, int $totalTime): int {
        if (!$isCorrect) return 0;
        $baseScore = 1000;
        $timeBonus = ($totalTime > 0) ? ($timeRemaining / $totalTime) * 500 : 0;
        return (int) round($baseScore + $timeBonus);
    }
}