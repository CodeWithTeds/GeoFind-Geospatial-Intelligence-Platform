<?php

namespace App\Services\Game;

use App\Repositories\Contracts\QuestionRepositoryInterface;
use App\Models\UserAnswer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class LevelService
{
    protected $questionRepository;

    public function __construct(QuestionRepositoryInterface $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * Get all levels with their status (locked/unlocked/completed) for the current user.
     *
     * @return Collection
     */
    public function getLevelsForUser(): Collection
    {
        $user = Auth::user();
        $completedLevels = $user->completed_levels;

        // Fetch all questions, ordered by level
        $questions = $this->questionRepository->all()->sortBy('level');

        // Fetch user's answers to get stars
        $userAnswers = UserAnswer::where('user_id', $user->id)
            ->where('is_correct', true)
            ->get()
            ->keyBy('question_id');

        return $questions->map(function ($question) use ($completedLevels, $userAnswers) {
            $levelNumber = $question->level;
            $stars = 0;

            // Determine status
            if ($levelNumber <= $completedLevels) {
                $status = 'completed';
                if (isset($userAnswers[$question->id])) {
                    $stars = $userAnswers[$question->id]->stars;
                }
            } elseif ($levelNumber == $completedLevels + 1) {
                $status = 'unlocked';
            } else {
                $status = 'locked';
            }

            return [
                'level' => $levelNumber,
                'question_id' => $question->id,
                'title' => $question->title,
                'difficulty' => $question->difficulty,
                'status' => $status,
                'description' => $question->description,
                'stars' => $stars,
            ];
        });
    }

    /**
     * Get the current progress percentage for the user.
     * 
     * @return int
     */
    public function getUserProgress(): int
    {
        $user = Auth::user();
        $totalLevels = $this->questionRepository->all()->count();

        if ($totalLevels === 0) {
            return 0;
        }

        return min(100, round(($user->completed_levels / $totalLevels) * 100));
    }
}
