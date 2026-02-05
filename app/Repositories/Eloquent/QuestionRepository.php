<?php

namespace App\Repositories\Eloquent;

use App\Models\Question;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionRepository implements QuestionRepositoryInterface
{
    protected $model;

    public function __construct(Question $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->latest()->paginate($perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $question = $this->find($id);
        if (!$question) {
            return false;
        }
        return $question->update($data);
    }

    public function delete(int $id): bool
    {
        $question = $this->find($id);
        if (!$question) {
            return false;
        }
        return $question->delete();
    }

    public function findByLevel(int $level): ?Model
    {
        return $this->model->where('level', $level)->first();
    }
}
