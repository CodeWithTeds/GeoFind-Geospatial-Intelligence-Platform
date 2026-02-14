<?php

namespace App\Services;

use App\Repositories\Contracts\QuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\UserAnswer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QuestionService
{
    protected $repository;
    protected $scoreService;

    public function __construct(QuestionRepositoryInterface $repository)
    {
        $this->repository = $repository;
        // Manual instantiation or property injection to avoid circular deps if any, 
        // though Service Container is preferred.
        // We'll use the ScoreService dynamically or instantiate it.
        $this->scoreService = new \App\Services\ScoreService(new \App\Services\Leaderboard\Strategies\TotalScoreStrategy());
    }

    public function submitAnswer(User $user, int $questionId, float $lat, float $lng): array
    {
        // 1. Check if already answered
        $existingAnswer = UserAnswer::where('user_id', $user->id)
            ->where('question_id', $questionId)
            ->first();

        if ($existingAnswer) {
            // If already answered CORRECTLY, return existing result (prevent farming)
            if ($existingAnswer->is_correct) {
                $question = $this->repository->find($questionId);
                return $this->formatResult($existingAnswer, $question, true);
            }

            // If answered INCORRECTLY, allow retry (delete old attempt)
            $existingAnswer->delete();
        }

        // 2. Get Question
        $question = $this->repository->find($questionId);
        if (!$question) {
            throw new \Exception("Question not found");
        }

        // Security: Prevent answering questions for locked levels
        // This prevents "pre-farming" answers for levels the user hasn't reached yet.
        if ($question->level > $user->completed_levels + 1) {
             throw new \Exception("Unauthorized: Level is locked.");
        }

        // 3. Calculate Distance (Haversine)
        $distanceMeters = $this->calculateDistance(
            $lat,
            $lng,
            $question->answer_latitude,
            $question->answer_longitude
        );

        // 4. Calculate Stars
        $stars = 0;
        $isCorrect = false;
        $tolerance = $question->tolerance_meters;

        if ($distanceMeters <= $tolerance) {
            $stars = 3;
            $isCorrect = true;
        } elseif ($distanceMeters <= $tolerance * 2) {
            $stars = 2;
            $isCorrect = true;
        } elseif ($distanceMeters <= $tolerance * 5) { // Lenient pass
            $stars = 1;
            $isCorrect = true;
        } else {
            $stars = 0;
            $isCorrect = false;
        }

        // 5. Save Answer
        $answer = UserAnswer::create([
            'user_id' => $user->id,
            'question_id' => $questionId,
            'answer_latitude' => $lat,
            'answer_longitude' => $lng,
            'stars' => $stars > 0 ? $stars : null, // Schema allows null, check constraint 1-3
            'is_correct' => $isCorrect,
            'answered_at' => now(),
        ]);

        // 6. Update User Progress if correct
        if ($isCorrect) {
            // Only update if this was the next level (or current max level)
            // But usually we just increment if it matches current level
            if ($question->level == $user->completed_levels + 1) {
                $user->increment('completed_levels');
            }
            
            // Notify Leaderboard
            $this->scoreService->notifyScoreUpdate();
        }

        return $this->formatResult($answer, $question, false, $distanceMeters);
    }

    protected function formatResult(UserAnswer $answer, Question $question, bool $alreadyAnswered, ?float $distance = null): array
    {
        if ($distance === null) {
            $distance = $this->calculateDistance(
                $answer->answer_latitude,
                $answer->answer_longitude,
                $question->answer_latitude,
                $question->answer_longitude
            );
        }

        return [
            'is_correct' => $answer->is_correct,
            'stars' => $answer->stars ?? 0,
            'distance_meters' => round($distance),
            'distance_formatted' => $this->formatDistance($distance),
            'correct_location' => $answer->is_correct ? [
                'latitude' => $question->answer_latitude,
                'longitude' => $question->answer_longitude,
            ] : null,
            'already_answered' => $alreadyAnswered,
            'next_level' => $answer->is_correct ? $question->level + 1 : null,
        ];
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Meters

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    protected function formatDistance($meters)
    {
        if ($meters < 1000) {
            return round($meters) . ' m';
        }
        return round($meters / 1000, 2) . ' km';
    }

    public function getAllQuestions(int $perPage = 15): LengthAwarePaginator
    {
        // Pagination results are hard to cache effectively due to page/sort/filter variations.
        // Relying on database indexes (added in migration) for performance here.
        return $this->repository->paginate($perPage);
    }

    public function getQuestionById(int $id): ?Model
    {
        return Cache::remember("question_{$id}", 3600, function () use ($id) {
            return $this->repository->find($id);
        });
    }

    public function getQuestionByLevel(int $level): ?Model
    {
        return $this->repository->findByLevel($level);
    }

    public function createQuestion(array $data): Model
    {
        $question = $this->repository->create($data);
        $this->clearDashboardCache();
        return $question;
    }

    public function updateQuestion(int $id, array $data): bool
    {
        $updated = $this->repository->update($id, $data);
        if ($updated) {
            Cache::forget("question_{$id}");
        }
        return $updated;
    }

    public function deleteQuestion(int $id): bool
    {
        $deleted = $this->repository->delete($id);
        if ($deleted) {
            Cache::forget("question_{$id}");
            $this->clearDashboardCache();
        }
        return $deleted;
    }

    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard_stats', 600, function () {
            return [
                'total_questions' => \App\Models\Question::count(),
                // Add more stats here as needed
            ];
        });
    }

    protected function clearDashboardCache(): void
    {
        Cache::forget('dashboard_stats');
    }
}
