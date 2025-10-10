<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RadiusValidationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'point_id' => 'required|integer|exists:locations,id',
            'radius' => 'required|numeric|min:0.1|max:5000000000',
            'show_hotels' => 'sometimes|boolean',
            'poi_types' => 'sometimes|array',
            'poi_types.*' => 'sometimes|string|in:tourism,amenity,shop,leisure,historic',
            'limit' => 'sometimes|integer|min:1|max:500',
            'sort_by' => 'sometimes|string|in:distance,name,category',
            'sort_order' => 'sometimes|string|in:asc,desc',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'point_id.required' => 'A center point is required',
            'point_id.exists' => 'The selected center point does not exist',
            'radius.required' => 'A search radius is required',
            'radius.min' => 'The radius must be at least 0.1 kilometers',
            'radius.max' => 'The radius cannot exceed 100 kilometers',
            'poi_types.*.in' => 'Invalid POI type specified. Allowed types: tourism, amenity, shop, leisure, historic',
            'sort_by.in' => 'Invalid sort field. Allowed fields: distance, name, category',
            'sort_order.in' => 'Invalid sort order. Allowed options: asc, desc',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
