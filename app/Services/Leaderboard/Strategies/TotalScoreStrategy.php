<?php

namespace App\Services\Leaderboard\Strategies;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TotalScoreStrategy implements RankingStrategy
{
    public function rank(int $limit = 10): Collection
    {
        // Calculate score based on completed levels and total stars
        // We assume UserAnswer has 'stars' and 'is_correct'
        // Users are ranked by Completed Levels (primary) and Total Stars (secondary)
        
        return User::query()
            ->withCount(['answers as total_stars' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(stars), 0)'));
            }])
            ->orderByDesc('completed_levels')
            ->orderByDesc('total_stars')
            ->limit($limit)
            ->get()
            ->map(function ($user, $index) {
                $user->rank = $index + 1;
                $user->total_score = ($user->completed_levels * 1000) + ($user->total_stars * 100);
                return $user;
            });
    }
}
