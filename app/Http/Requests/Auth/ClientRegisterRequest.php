<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use App\Rules\Turnstile;

class ClientRegisterRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // Apply Turnstile only in production/non-local environments and not during unit tests
        if (!app()->environment('local') && !app()->runningUnitTests()) {
            $rules['cf-turnstile-response'] = ['required', new Turnstile];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Please complete the CAPTCHA verification.',
        ];
    }
}
