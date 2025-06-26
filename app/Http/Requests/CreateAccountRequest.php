<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:checking,savings,credit_card,cash,investment',
            'balance' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Account name is required.',
            'type.required' => 'Account type is required.',
            'type.in' => 'Please select a valid account type.',
            'balance.required' => 'Initial balance is required.',
            'balance.numeric' => 'Balance must be a valid number.',
            'balance.min' => 'Balance cannot be negative.',
            'currency.size' => 'Currency must be a 3-letter code (e.g., USD).',
        ];
    }
}
