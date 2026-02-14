<?php

namespace App\Services\Leaderboard\Strategies;

use Illuminate\Support\Collection;

interface RankingStrategy
{
    /**
     * Rank users based on specific criteria.
     *
     * @param int $limit
     * @return Collection
     */
    public function rank(int $limit = 10): Collection;
}
