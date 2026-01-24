<?php

namespace App\Services;

use App\Repositories\Contracts\QuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Cache;

class QuestionService
{
    protected $repository;

    public function __construct(QuestionRepositoryInterface $repository)
    {
        $this->repository = $repository;
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
