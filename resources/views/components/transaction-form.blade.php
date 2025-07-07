<div class="form-container bg-white rounded-lg shadow-sm" x-data="transactionForm()">
    <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Transaction Type Toggle -->
        <div class="flex bg-gray-100 rounded-lg p-1">
            <button type="button"
                    @click="form.type = 'expense'"
                    :class="form.type === 'expense' ? 'bg-white shadow-sm' : ''"
                    class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all">
                💸 Expense
            </button>
            <button type="button"
                    @click="form.type = 'income'"
                    :class="form.type === 'income' ? 'bg-white shadow-sm' : ''"
                    class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all">
                💰 Income
            </button>
        </div>

        <!-- Smart Description Input -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <input type="text"
                   x-model="form.description"
                   @input="suggestCategory"
                   class="smart-input"
                   placeholder="Enter transaction description..."
                   required>

            <!-- Category Suggestions -->
            <div x-show="suggestions.length > 0" class="mt-3">
                <p class="text-sm text-gray-600 mb-2">💡 Suggested categories:</p>
                <div class="flex flex-wrap">
                    <template x-for="suggestion in suggestions" :key="suggestion.id">
                        <button type="button"
                                @click="selectCategory(suggestion)"
                                class="suggestion-chip"
                                x-text="suggestion.name">
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Amount Input with Calculator -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
            <div class="relative">
                <span class="absolute left-3 top-3 text-gray-500">$</span>
                <input type="text"
                       x-model="form.amount"
                       @input="calculateAmount"
                       class="smart-input pl-8"
                       placeholder="0.00 or 10+5*2"
                       required>
            </div>
            <div x-show="calculatedAmount" class="calculator-display">
                💡 = $<span x-text="calculatedAmount"></span>
            </div>
            <div x-show="calculationError" class="text-red-500 text-sm mt-1">
                ❌ Invalid calculation
            </div>
        </div>

        <!-- Account Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Account</label>
            <select x-model="form.account_id" class="smart-input" required>
                <option value="">Select an account</option>
                @foreach(auth()->user()->accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->type }})</option>
                @endforeach
            </select>
        </div>

        <!-- Category Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select x-model="form.category_id" class="smart-input" required>
                <option value="">Select a category</option>
                @foreach(auth()->user()->categories->where('type', 'expense') as $category)
                    <option value="{{ $category->id }}" x-show="form.type === 'expense'">{{ $category->name }}</option>
                @endforeach
                @foreach(auth()->user()->categories->where('type', 'income') as $category)
                    <option value="{{ $category->id }}" x-show="form.type === 'income'">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Date Input -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
            <input type="date"
                   x-model="form.date"
                   class="smart-input"
                   required>
        </div>

        <!-- Notes (Optional) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
            <textarea x-model="form.notes"
                      class="smart-input"
                      rows="3"
                      placeholder="Add any additional notes..."></textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex space-x-3">
            <button type="submit"
                    :disabled="isSubmitting"
                    class="btn-mobile bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors">
                <span x-show="!isSubmitting">💾 Save Transaction</span>
                <span x-show="isSubmitting">⏳ Saving...</span>
            </button>
            <button type="button"
                    @click="resetForm"
                    class="btn-mobile bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                🔄 Reset
            </button>
        </div>
    </form>
</div>
