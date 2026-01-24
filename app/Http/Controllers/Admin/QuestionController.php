<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Services\QuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuestionController extends Controller
{
    protected $service;

    public function __construct(QuestionService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $questions = $this->service->getAllQuestions(10); // Paginate 10 per page
        return view('admin.questions.index', compact('questions'));
    }

    public function create(): View
    {
        return view('admin.questions.create');
    }

    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        $this->service->createQuestion($request->validated());
        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully.');
    }

    public function show(string $id)
    {
        // Not implemented for now, maybe later
        return redirect()->route('admin.questions.index');
    }

    public function edit(int $id): View
    {
        $question = $this->service->getQuestionById($id);
        if (!$question) {
            abort(404);
        }
        return view('admin.questions.edit', compact('question'));
    }

    public function update(UpdateQuestionRequest $request, int $id): RedirectResponse
    {
        $updated = $this->service->updateQuestion($id, $request->validated());
        
        if (!$updated) {
            return back()->with('error', 'Failed to update question.');
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $deleted = $this->service->deleteQuestion($id);
        
        if (!$deleted) {
            return back()->with('error', 'Failed to delete question.');
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
