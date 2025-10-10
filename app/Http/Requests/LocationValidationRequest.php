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
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'point1_id' => ['required', 'integer', 'exists:locations,id'],
            'point2_id' => ['required', 'integer', 'exists:locations,id', 'different:point1_id'],
            'radius' => ['required', 'numeric', 'min:0'],
            'start_location' => ['required', 'string', 'min:3'],
            'end_location' => ['required', 'string', 'min:3', 'different:start_location'],
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
            'latitude.required' => 'The latitude field is required.',
            'latitude.numeric' => 'The latitude must be a number.',
            'latitude.between' => 'The latitude must be between -90 and 90 degrees.',
            'longitude.required' => 'The longitude field is required.',
            'longitude.numeric' => 'The longitude must be a number.',
            'longitude.between' => 'The longitude must be between -180 and 180 degrees.',
            'point1_id.required' => 'The first point is required.',
            'point1_id.exists' => 'The selected first point does not exist.',
            'point2_id.required' => 'The second point is required.',
            'point2_id.exists' => 'The selected second point does not exist.',
            'point2_id.different' => 'The second point must be different from the first point.',
            'radius.required' => 'The radius field is required.',
            'radius.numeric' => 'The radius must be a number.',
            'radius.min' => 'The radius must be greater than 0.',
            'start_location.required' => 'The start location is required.',
            'start_location.min' => 'The start location must be at least 3 characters.',
            'end_location.required' => 'The end location is required.',
            'end_location.min' => 'The end location must be at least 3 characters.',
            'end_location.different' => 'The end location must be different from the start location.',
        ];
    }
}
