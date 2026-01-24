<?php

namespace App\Services;

use App\Repositories\Contracts\QuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionService
{
    protected $repository;

    public function __construct(QuestionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllQuestions(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function getQuestionById(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function createQuestion(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function updateQuestion(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function deleteQuestion(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
