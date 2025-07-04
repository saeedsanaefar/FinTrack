<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category') ? $this->route('category')->id : null;
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })->ignore($categoryId)
            ],
            'type' => 'required|in:income,expense,both',
            'color' => 'nullable|string|regex:/^#[A-Fa-f0-9]{6}$/',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
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
            'name.required' => 'The category name is required.',
            'name.unique' => 'You already have a category with this name.',
            'type.required' => 'The category type is required.',
            'type.in' => 'The category type must be income, expense, or both.',
            'color.regex' => 'The color must be a valid hex color code (e.g., #FF0000).',
            'icon.max' => 'The icon name cannot exceed 50 characters.',
            'description.max' => 'The description cannot exceed 500 characters.',
            'sort_order.min' => 'The sort order must be at least 0.',
            'sort_order.max' => 'The sort order cannot exceed 999.',
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
            'sort_order' => 'sort order',
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
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
