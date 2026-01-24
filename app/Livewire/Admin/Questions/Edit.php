<?php

namespace App\Livewire\Admin\Questions;

use App\Models\Question;
use App\Services\QuestionService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Edit extends Component
{
    public Question $question;

    public $title;
    public $description;
    public $answer_latitude;
    public $answer_longitude;
    public $tolerance_meters;
    public $difficulty;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'answer_latitude' => 'required|numeric|between:-90,90',
        'answer_longitude' => 'required|numeric|between:-180,180',
        'tolerance_meters' => 'required|integer|min:1',
        'difficulty' => 'required|in:easy,medium,hard',
    ];

    public function mount(Question $question)
    {
        $this->question = $question;
        $this->title = $question->title;
        $this->description = $question->description;
        $this->answer_latitude = $question->answer_latitude;
        $this->answer_longitude = $question->answer_longitude;
        $this->tolerance_meters = $question->tolerance_meters;
        $this->difficulty = $question->difficulty;
    }

    public function update(QuestionService $service)
    {
        $this->validate();

        $service->updateQuestion($this->question->id, [
            'title' => $this->title,
            'description' => $this->description,
            'answer_latitude' => $this->answer_latitude,
            'answer_longitude' => $this->answer_longitude,
            'tolerance_meters' => $this->tolerance_meters,
            'difficulty' => $this->difficulty,
        ]);

        session()->flash('success', 'Question updated successfully.');

        return redirect()->route('admin.questions.index');
    }

    public function render()
    {
        return view('livewire.admin.questions.edit');
    }
}
