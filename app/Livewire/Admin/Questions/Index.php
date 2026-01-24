<?php

namespace App\Livewire\Admin\Questions;

use App\Services\QuestionService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public function delete($id, QuestionService $service)
    {
        $service->deleteQuestion($id);
        session()->flash('success', 'Question deleted successfully.');
    }

    public function render(QuestionService $service)
    {
        return view('livewire.admin.questions.index', [
            'questions' => $service->getAllQuestions(10)
        ]);
    }
}
