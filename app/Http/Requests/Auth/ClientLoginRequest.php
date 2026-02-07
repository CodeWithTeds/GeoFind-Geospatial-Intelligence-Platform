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
      * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('cf-turnstile-response')) {
            $this->merge([
                'cf-turnstile-response' => trim($this->input('cf-turnstile-response')),
            ]);
        }
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
        if (!app()->environment('local', 'testing')) {
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
