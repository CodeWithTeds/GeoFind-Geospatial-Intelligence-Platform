<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'answer_latitude' => 'required|numeric|between:-90,90',
            'answer_longitude' => 'required|numeric|between:-180,180',
            'tolerance_meters' => 'required|integer|min:0',
            'difficulty' => 'required|in:easy,medium,hard',
            'description' => 'nullable|string',
        ];
    }
}
