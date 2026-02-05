<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'answer_latitude' => 'sometimes|required|numeric|between:-90,90',
            'answer_longitude' => 'sometimes|required|numeric|between:-180,180',
            'tolerance_meters' => 'sometimes|required|integer|min:0',
            'difficulty' => 'sometimes|required|in:easy,medium,hard',
            'level' => 'sometimes|required|integer|min:1',
            'description' => 'nullable|string',
        ];
    }
}
