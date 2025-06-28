<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Edit Budget') }}
            </h2>
            <a href="{{ route('budgets.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Budgets
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6">
                <form method="POST" action="{{ route('budgets.update', $budget) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">
                            Category <span class="text-red-400">*</span>
                        </label>
                        <select name="category_id" id="category_id" required
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $budget->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Budget Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-300 mb-2">
                            Budget Amount <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400">$</span>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                                   value="{{ old('amount', $budget->amount) }}" required
                                   placeholder="0.00"
                                   class="w-full pl-8 pr-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('amount') border-red-500 @enderror">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Period Type -->
                    <div>
                        <label for="period_type" class="block text-sm font-medium text-gray-300 mb-2">
                            Budget Period <span class="text-red-400">*</span>
                        </label>
                        <select name="period_type" id="period_type" required
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('period_type') border-red-500 @enderror">
                            <option value="monthly" {{ old('period_type', $budget->period_type) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('period_type', $budget->period_type) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                        @error('period_type')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Year -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-300 mb-2">
                            Year <span class="text-red-400">*</span>
                        </label>
                        <select name="year" id="year" required
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('year') border-red-500 @enderror">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ old('year', $budget->year) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        @error('year')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Month (for monthly budgets) -->
                    <div id="month-field" style="display: {{ old('period_type', $budget->period_type) === 'monthly' ? 'block' : 'none' }}">
                        <label for="month" class="block text-sm font-medium text-gray-300 mb-2">
                            Month <span class="text-red-400">*</span>
                        </label>
                        <select name="month" id="month"
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('month') border-red-500 @enderror">
                            <option value="1" {{ old('month', $budget->month) == 1 ? 'selected' : '' }}>January</option>
                            <option value="2" {{ old('month', $budget->month) == 2 ? 'selected' : '' }}>February</option>
                            <option value="3" {{ old('month', $budget->month) == 3 ? 'selected' : '' }}>March</option>
                            <option value="4" {{ old('month', $budget->month) == 4 ? 'selected' : '' }}>April</option>
                            <option value="5" {{ old('month', $budget->month) == 5 ? 'selected' : '' }}>May</option>
                            <option value="6" {{ old('month', $budget->month) == 6 ? 'selected' : '' }}>June</option>
                            <option value="7" {{ old('month', $budget->month) == 7 ? 'selected' : '' }}>July</option>
                            <option value="8" {{ old('month', $budget->month) == 8 ? 'selected' : '' }}>August</option>
                            <option value="9" {{ old('month', $budget->month) == 9 ? 'selected' : '' }}>September</option>
                            <option value="10" {{ old('month', $budget->month) == 10 ? 'selected' : '' }}>October</option>
                            <option value="11" {{ old('month', $budget->month) == 11 ? 'selected' : '' }}>November</option>
                            <option value="12" {{ old('month', $budget->month) == 12 ? 'selected' : '' }}>December</option>
                        </select>
                        @error('month')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  placeholder="Optional notes about this budget..."
                                  class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $budget->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $budget->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 bg-gray-800 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="is_active" class="ml-2 text-sm text-gray-300">
                            Active budget
                        </label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="{{ route('budgets.index') }}"
                           class="px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-gray-800 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-save mr-2"></i>Update Budget
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('period_type').addEventListener('change', function() {
            const monthField = document.getElementById('month-field');
            const monthSelect = document.getElementById('month');

            if (this.value === 'monthly') {
                monthField.style.display = 'block';
                monthSelect.required = true;
            } else {
                monthField.style.display = 'none';
                monthSelect.required = false;
                monthSelect.value = '';
            }
        });
    </script>
</x-app-layout>
