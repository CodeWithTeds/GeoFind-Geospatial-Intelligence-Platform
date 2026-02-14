<?php

namespace App\Services;

use App\Services\Leaderboard\Strategies\RankingStrategy;
use Illuminate\Support\Collection;
use App\Events\LeaderboardUpdated;

class ScoreService
{
    protected $rankingStrategy;

    public function __construct(RankingStrategy $rankingStrategy)
    {
        $this->rankingStrategy = $rankingStrategy;
    }

    /**
     * Get the current leaderboard.
     */
    public function getLeaderboard(int $limit = 50): Collection
    {
        return $this->rankingStrategy->rank($limit);
    }

    /**
     * Notify system of a score update.
     * This follows the observer/pub-sub pattern to decouple score updates from UI.
     */
    public function notifyScoreUpdate(): void
    {
        // In a real WebSocket setup, we would broadcast this event.
        // For Livewire polling, this might just log or invalidate cache.
        // We'll keep it as a placeholder for proper event-driven architecture.
        event(new LeaderboardUpdated());
    }
    
    /**
     * Set a new ranking strategy dynamically.
     */
    public function setStrategy(RankingStrategy $strategy): void
    {
        $this->rankingStrategy = $strategy;
    }
}
