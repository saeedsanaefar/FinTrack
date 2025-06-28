<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Create Category') }}
                    </h2>
                    <p class="text-sm text-gray-600">Organize your transactions with custom categories</p>
                </div>
            </div>
            <x-secondary-button onclick="window.location.href='{{ route('categories.index') }}'">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Categories
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 overflow-hidden shadow-lg sm:rounded-xl border border-gray-700">
                <div class="p-8 text-gray-100">
                    <form method="POST" action="{{ route('categories.store') }}" class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <div class="space-y-2">
                            <x-input-label for="name" :value="__('Category Name')" :required="true" />
                            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="Enter category name" icon="tag" />
                            <p class="text-xs text-gray-500">Choose a descriptive name for your category</p>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div class="space-y-2">
                            <x-input-label for="type" :value="__('Category Type')" :required="true" />
                            <select id="type" name="type" class="block w-full bg-gray-800 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500" required>
                                <option value="">📊 Select Category Type</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>💰 Income</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>💸 Expense</option>
                            </select>
                            <p class="text-xs text-gray-500">Choose whether this category is for income or expenses</p>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Icon Selector -->
                        <div class="space-y-2">
                            <x-input-label for="icon" :value="__('Category Icon')" />
                            <x-icon-selector name="icon" :value="old('icon', 'fas fa-tag')" />
                            <p class="text-xs text-gray-500">Choose an icon to represent this category</p>
                            <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                        </div>

                        <!-- Color Picker -->
                        <div class="space-y-2">
                            <x-input-label for="color" :value="__('Category Color')" />
                            <x-color-picker name="color" :value="old('color', '#3B82F6')" />
                            <p class="text-xs text-gray-500">Pick a color to easily identify this category</p>
                            <x-input-error :messages="$errors->get('color')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="block w-full bg-gray-800 border-gray-600 text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500 resize-none" placeholder="Add a description for this category...">{{ old('description') }}</textarea>
                            <p class="text-xs text-gray-500">Optional notes about this category</p>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Sort Order -->
                        <div class="space-y-2">
                            <x-input-label for="sort_order" :value="__('Sort Order')" />
                            <x-text-input id="sort_order" class="block w-full" type="number" name="sort_order" :value="old('sort_order', 0)" min="0" placeholder="0" />
                            <p class="text-xs text-gray-500">Lower numbers appear first in lists</p>
                            <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                        </div>

                        <!-- Is Active -->
                        <div class="space-y-2">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all duration-200">
                                    <label for="is_active" class="ml-3 block text-sm font-medium text-gray-900">
                                        ✅ Active Category
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 ml-8">Active categories are available for new transactions</p>
                            </div>
                            <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <x-secondary-button onclick="window.location.href='{{ route('categories.index') }}'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Cancel
                            </x-secondary-button>
                            <x-primary-button class="ml-4">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('Create Category') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
