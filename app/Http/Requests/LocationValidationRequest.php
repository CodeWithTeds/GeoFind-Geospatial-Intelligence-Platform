<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LocationValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Validate coordinates
     *
     * @param float $latitude
     * @param float $longitude
     * @return bool
     * @throws ValidationException
     */
    public function validateCoordinates(float $latitude, float $longitude): bool
    {
        $validator = Validator::make(
            ['latitude' => $latitude, 'longitude' => $longitude],
            [
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 2 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'latitude.required' => 'The latitude field is required.',
            'latitude.numeric' => 'The latitude must be a number.',
            'latitude.between' => 'The latitude must be between -90 and 90 degrees.',
            'longitude.required' => 'The longitude field is required.',
            'longitude.numeric' => 'The longitude must be a number.',
            'longitude.between' => 'The longitude must be between -180 and 180 degrees.',
        ];
    }
}
