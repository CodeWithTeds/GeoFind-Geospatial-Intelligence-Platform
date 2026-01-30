<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\Turnstile;

class ClientLoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];

        // Apply Turnstile only in production/non-local environments
        // The user explicitly said: "THE CLOUDFLARE IS FOR PRODUCTION ONLY NOT FOR LOCAL!"
        if (!app()->environment('local') && !app()->runningUnitTests()) {
            $rules['cf-turnstile-response'] = ['required', new Turnstile];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Please complete the CAPTCHA verification.',
        ];
    }
}
