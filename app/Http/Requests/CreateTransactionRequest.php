<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
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
        $this->merge([
            'description' => strip_tags($this->description ?? ''),
            'notes' => strip_tags($this->notes ?? ''),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-\.\,\!\?]+$/',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'notes' => 'nullable|string|max:1000',
            'account_id' => 'required|exists:accounts,id,user_id,' . auth()->id(),
            'category_id' => 'required|exists:categories,id,user_id,' . auth()->id(),
            'type' => 'required|in:income,expense',
            'transaction_date' => 'required|date|before_or_equal:today',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'description.regex' => 'Description contains invalid characters.',
            'account_id.exists' => 'Selected account does not belong to you.',
            'category_id.exists' => 'Selected category does not belong to you.',
        ];
    }
}
