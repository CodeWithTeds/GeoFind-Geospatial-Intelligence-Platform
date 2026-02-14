<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ScoreService;
use App\Services\Leaderboard\Strategies\TotalScoreStrategy;

class Leaderboard extends Component
{
    // Use the concrete strategy for now, can be injected via container in a real app
    // but for Livewire simple instantiation, we'll compose it here.
    
    public function render()
    {
        // Composition Root / Dependency Injection
        $strategy = new TotalScoreStrategy();
        $scoreService = new ScoreService($strategy);
        
        $leaderboard = $scoreService->getLeaderboard(100);
        
        // Split into Top 3 (Podium) and Rest (List)
        $top3 = $leaderboard->take(3);
        $rest = $leaderboard->slice(3);

        return view('livewire.leaderboard', [
            'top3' => $top3,
            'rest' => $rest
        ])->layout('layouts.client');
    }
}
