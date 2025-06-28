@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-900">Edit Recurring Transaction</h1>
                    <a href="{{ route('recurring-transactions.show', $recurringTransaction) }}" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="p-6">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('recurring-transactions.update', $recurringTransaction) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Transaction Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                        <select name="type" id="type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Type</option>
                            <option value="income" {{ old('type', $recurringTransaction->type) == 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ old('type', $recurringTransaction->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="transfer" {{ old('type', $recurringTransaction->type) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <input type="text" name="description" id="description" value="{{ old('description', $recurringTransaction->description) }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Enter description" required>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount', $recurringTransaction->amount) }}" step="0.01" min="0" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="0.00" required>
                    </div>

                    <!-- From Account -->
                    <div>
                        <label for="account_id" class="block text-sm font-medium text-gray-700 mb-2">From Account</label>
                        <select name="account_id" id="account_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('account_id', $recurringTransaction->account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} ({{ $account->type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- To Account (for transfers) -->
                    <div id="to-account-field" style="display: {{ old('type', $recurringTransaction->type) == 'transfer' ? 'block' : 'none' }};">
                        <label for="to_account_id" class="block text-sm font-medium text-gray-700 mb-2">To Account</label>
                        <select name="to_account_id" id="to_account_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('to_account_id', $recurringTransaction->to_account_id) == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} ({{ $account->type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" id="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Category (Optional)</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $recurringTransaction->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Frequency -->
                    <div>
                        <label for="frequency" class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                        <select name="frequency" id="frequency" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Frequency</option>
                            <option value="daily" {{ old('frequency', $recurringTransaction->frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ old('frequency', $recurringTransaction->frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ old('frequency', $recurringTransaction->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('frequency', $recurringTransaction->frequency) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $recurringTransaction->start_date->format('Y-m-d')) }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date (Optional)</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $recurringTransaction->end_date?->format('Y-m-d')) }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Leave empty for indefinite recurring</p>
                    </div>

                    <!-- Reference -->
                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">Reference (Optional)</label>
                        <input type="text" name="reference" id="reference" value="{{ old('reference', $recurringTransaction->reference) }}" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Enter reference">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                  placeholder="Enter any additional notes">{{ old('notes', $recurringTransaction->notes) }}</textarea>
                    </div>

                    <!-- Active Status -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $recurringTransaction->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active (uncheck to deactivate this recurring transaction)
                            </label>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('recurring-transactions.show', $recurringTransaction) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Recurring Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide to account field based on transaction type
document.getElementById('type').addEventListener('change', function() {
    const toAccountField = document.getElementById('to-account-field');
    const toAccountSelect = document.getElementById('to_account_id');
    
    if (this.value === 'transfer') {
        toAccountField.style.display = 'block';
        toAccountSelect.required = true;
    } else {
        toAccountField.style.display = 'none';
        toAccountSelect.required = false;
        toAccountSelect.value = '';
    }
});

// Set initial state based on current value
if (document.getElementById('type').value === 'transfer') {
    document.getElementById('to_account_id').required = true;
}
</script>
@endsection