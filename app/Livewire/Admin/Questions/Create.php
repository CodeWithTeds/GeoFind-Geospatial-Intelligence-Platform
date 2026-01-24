<?php

namespace App\Livewire\Admin\Questions;

use App\Services\QuestionService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Create extends Component
{
    public $title;
    public $description;
    public $answer_latitude;
    public $answer_longitude;
    public $tolerance_meters = 50;
    public $difficulty = 'medium';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'answer_latitude' => 'required|numeric|between:-90,90',
        'answer_longitude' => 'required|numeric|between:-180,180',
        'tolerance_meters' => 'required|integer|min:1',
        'difficulty' => 'required|in:easy,medium,hard',
    ];

    public function save(QuestionService $service)
    {
        $this->validate();

        $service->createQuestion([
            'title' => $this->title,
            'description' => $this->description,
            'answer_latitude' => $this->answer_latitude,
            'answer_longitude' => $this->answer_longitude,
            'tolerance_meters' => $this->tolerance_meters,
            'difficulty' => $this->difficulty,
        ]);

        session()->flash('success', 'Question created successfully.');

        return redirect()->route('admin.questions.index');
    }

    public function render()
    {
        return view('livewire.admin.questions.create');
    }
}
