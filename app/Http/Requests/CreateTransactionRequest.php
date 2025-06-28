<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTransactionRequest extends FormRequest
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
        $rules = [
            'account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'type' => 'required|in:income,expense,transfer',
            'date' => 'required|date|before_or_equal:today',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:is_recurring,true|in:daily,weekly,monthly,yearly',
            'recurring_end_date' => 'nullable|required_if:is_recurring,true|date|after:date',
        ];

        // Add transfer-specific validation
        if ($this->input('type') === 'transfer') {
            $rules['transfer_account_id'] = [
                'required',
                'integer',
                'different:account_id',
                Rule::exists('accounts', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'account_id.exists' => 'The selected account does not exist or does not belong to you.',
            'category_id.exists' => 'The selected category does not exist or does not belong to you.',
            'transfer_account_id.exists' => 'The selected transfer account does not exist or does not belong to you.',
            'transfer_account_id.different' => 'The transfer account must be different from the source account.',
            'transfer_account_id.required' => 'A transfer account is required for transfer transactions.',
            'amount.min' => 'The amount must be at least 0.01.',
            'amount.max' => 'The amount cannot exceed 999,999,999.99.',
            'date.before_or_equal' => 'The transaction date cannot be in the future.',
            'recurring_frequency.required_if' => 'Recurring frequency is required when transaction is recurring.',
            'recurring_end_date.required_if' => 'Recurring end date is required when transaction is recurring.',
            'recurring_end_date.after' => 'Recurring end date must be after the transaction date.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'account_id' => 'account',
            'category_id' => 'category',
            'transfer_account_id' => 'transfer account',
            'recurring_frequency' => 'recurring frequency',
            'recurring_end_date' => 'recurring end date',
        ];
    }
}
