<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CreateBudgetRequest extends FormRequest
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
        $budgetId = $this->route('budget') ? $this->route('budget')->id : null;
        
        return [
            'category_id' => [
                'required',
                'exists:categories,id',
                Rule::unique('budgets')
                    ->where('user_id', auth()->id())
                    ->where('period_type', $this->input('period_type'))
                    ->where('year', $this->input('year'))
                    ->where('month', $this->input('month'))
                    ->ignore($budgetId)
            ],
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'period_type' => 'required|in:monthly,yearly',
            'year' => [
                'required',
                'integer',
                'min:2020',
                'max:' . (Carbon::now()->year + 5)
            ],
            'month' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
                'required_if:period_type,monthly'
            ],
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category for this budget.',
            'category_id.exists' => 'The selected category does not exist.',
            'category_id.unique' => 'You already have a budget for this category in the selected period.',
            'amount.required' => 'The budget amount is required.',
            'amount.numeric' => 'The budget amount must be a valid number.',
            'amount.min' => 'The budget amount must be at least $0.01.',
            'amount.max' => 'The budget amount cannot exceed $999,999,999.99.',
            'period_type.required' => 'Please select a budget period type.',
            'period_type.in' => 'The budget period must be either monthly or yearly.',
            'year.required' => 'The budget year is required.',
            'year.integer' => 'The year must be a valid number.',
            'year.min' => 'The year cannot be earlier than 2020.',
            'year.max' => 'The year cannot be more than 5 years in the future.',
            'month.required_if' => 'The month is required for monthly budgets.',
            'month.integer' => 'The month must be a valid number.',
            'month.min' => 'The month must be between 1 and 12.',
            'month.max' => 'The month must be between 1 and 12.',
            'notes.max' => 'The notes cannot exceed 1000 characters.',
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
            'category_id' => 'category',
            'period_type' => 'budget period',
            'is_active' => 'active status',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values if not provided
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'year' => $this->input('year', Carbon::now()->year),
        ]);

        // Set month to null for yearly budgets
        if ($this->input('period_type') === 'yearly') {
            $this->merge(['month' => null]);
        } elseif ($this->input('period_type') === 'monthly' && !$this->has('month')) {
            $this->merge(['month' => Carbon::now()->month]);
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate that the category belongs to the authenticated user
            if ($this->filled('category_id')) {
                $category = \App\Models\Category::find($this->input('category_id'));
                if ($category && $category->user_id !== auth()->id()) {
                    $validator->errors()->add('category_id', 'The selected category does not belong to you.');
                }
            }

            // Validate that monthly budgets have a valid month
            if ($this->input('period_type') === 'monthly' && !$this->filled('month')) {
                $validator->errors()->add('month', 'Month is required for monthly budgets.');
            }

            // Validate that yearly budgets don't have a month
            if ($this->input('period_type') === 'yearly' && $this->filled('month')) {
                $validator->errors()->add('month', 'Month should not be specified for yearly budgets.');
            }
        });
    }
}