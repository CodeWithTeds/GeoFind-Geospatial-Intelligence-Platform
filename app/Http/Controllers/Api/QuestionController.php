<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Services\QuestionService;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    

    public function __construct(protected QuestionService $service)
    {
    }

    public function index(): JsonResponse
    {
        $questions = $this->service->getAllQuestions();
        return response()->json($questions);
    }

    public function store(StoreQuestionRequest $request): JsonResponse
    {
        $question = $this->service->createQuestion($request->validated());
        return response()->json($question, 201);
    }

    public function show(int $id): JsonResponse
    {
        $question = $this->service->getQuestionById($id);
        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }
        return response()->json($question);
    }

    public function getByLevel(int $level): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = request()->user();
        
        // If API route is authenticated, check permission
        if ($user && $level > $user->completed_levels + 1) {
            return response()->json(['message' => 'Level locked'], 403);
        }

        $question = $this->service->getQuestionByLevel($level);
        if (!$question) {
            return response()->json(['message' => 'Level not found'], 404);
        }
        return response()->json($question);
    }

    public function submitAnswer(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $result = $this->service->submitAnswer(
                $user,
                $request->input('question_id'),
                $request->input('latitude'),
                $request->input('longitude')
            );
            return response()->json($result);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // Log the actual error for admins
            \Illuminate\Support\Facades\Log::error("Submission Error: " . $e->getMessage());
            
            // Return a sanitized message for users
            // Allow specific logic messages, hide system errors
            $safeMessages = ['Question not found', 'Unauthorized: Level is locked.'];
            $message = in_array($e->getMessage(), $safeMessages) 
                ? $e->getMessage() 
                : 'An unexpected error occurred.';
                
            return response()->json(['message' => $message], 400);
        }
    }

    public function update(UpdateQuestionRequest $request, int $id): JsonResponse
    {
        $updated = $this->service->updateQuestion($id, $request->validated());
        if (!$updated) {
            return response()->json(['message' => 'Question not found or update failed'], 404);
        }
        return response()->json($this->service->getQuestionById($id));
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->deleteQuestion($id);
        if (!$deleted) {
            return response()->json(['message' => 'Question not found'], 404);
        }
        return response()->json(['message' => 'Question deleted successfully']);
    }
}
