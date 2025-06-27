<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Transaction') }}
            </h2>
            <a href="{{ route('transactions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Transactions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('transactions.store') }}" id="transactionForm">
                        @csrf

                        <!-- Transaction Type -->
                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Transaction Type')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required onchange="updateCategoryOptions()">
                                <option value="">Select Transaction Type</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="transfer" {{ old('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Account -->
                        <div class="mb-4">
                            <x-input-label for="account_id" :value="__('From Account')" />
                            <select id="account_id" name="account_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required onchange="updateAccountBalance()">
                                <option value="">Select Account</option>
                                @foreach(auth()->user()->accounts as $account)
                                    <option value="{{ $account->id }}" 
                                            data-balance="{{ $account->balance }}" 
                                            data-currency="{{ $account->currency }}"
                                            {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ number_format($account->balance, 2) }} {{ $account->currency }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('account_id')" class="mt-2" />
                            <div id="account-balance" class="mt-2 text-sm text-gray-600" style="display: none;">
                                Current Balance: <span id="balance-amount"></span>
                            </div>
                        </div>

                        <!-- To Account (for transfers) -->
                        <div class="mb-4" id="to-account-section" style="display: none;">
                            <x-input-label for="to_account_id" :value="__('To Account')" />
                            <select id="to_account_id" name="to_account_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Destination Account</option>
                                @foreach(auth()->user()->accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ number_format($account->balance, 2) }} {{ $account->currency }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('to_account_id')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div class="mb-4" id="category-section">
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Category</option>
                                @foreach(auth()->user()->categories as $category)
                                    <option value="{{ $category->id }}" 
                                            data-type="{{ $category->type }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <x-input-label for="amount" :value="__('Amount')" />
                            <div class="relative">
                                <span id="currency-symbol" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                <x-text-input id="amount" class="block mt-1 w-full pl-8" type="number" name="amount" :value="old('amount')" step="0.01" min="0.01" required placeholder="0.00" />
                            </div>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')" required placeholder="Enter transaction description" />
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Transaction Date -->
                        <div class="mb-4">
                            <x-input-label for="transaction_date" :value="__('Transaction Date')" />
                            <x-text-input id="transaction_date" class="block mt-1 w-full" type="date" name="transaction_date" :value="old('transaction_date', date('Y-m-d'))" max="{{ date('Y-m-d') }}" required />
                            <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                        </div>

                        <!-- Reference (Optional) -->
                        <div class="mb-4">
                            <x-input-label for="reference" :value="__('Reference (Optional)')" />
                            <x-text-input id="reference" class="block mt-1 w-full" type="text" name="reference" :value="old('reference')" placeholder="Check number, invoice number, etc." />
                            <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                        </div>

                        <!-- Notes (Optional) -->
                        <div class="mb-4">
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Additional notes about this transaction...">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Recurring Transaction -->
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input id="is_recurring" name="is_recurring" type="checkbox" value="1" 
                                       {{ old('is_recurring') ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       onchange="toggleRecurringOptions()">
                                <label for="is_recurring" class="ml-2 block text-sm text-gray-900">
                                    This is a recurring transaction
                                </label>
                            </div>
                        </div>

                        <!-- Recurring Options -->
                        <div id="recurring-options" style="display: none;">
                            <div class="mb-4">
                                <x-input-label for="recurring_frequency" :value="__('Frequency')" />
                                <select id="recurring_frequency" name="recurring_frequency" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="weekly" {{ old('recurring_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('recurring_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('recurring_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="yearly" {{ old('recurring_frequency') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                                <x-input-error :messages="$errors->get('recurring_frequency')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="recurring_end_date" :value="__('End Date (Optional)')" />
                                <x-text-input id="recurring_end_date" class="block mt-1 w-full" type="date" name="recurring_end_date" :value="old('recurring_end_date')" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                                <p class="text-sm text-gray-600 mt-1">Leave empty for indefinite recurring</p>
                                <x-input-error :messages="$errors->get('recurring_end_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('transactions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Create Transaction') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCategoryOptions() {
            const type = document.getElementById('type').value;
            const categorySelect = document.getElementById('category_id');
            const categorySection = document.getElementById('category-section');
            const toAccountSection = document.getElementById('to-account-section');
            
            // Show/hide sections based on type
            if (type === 'transfer') {
                categorySection.style.display = 'none';
                toAccountSection.style.display = 'block';
                document.getElementById('to_account_id').required = true;
                categorySelect.required = false;
            } else {
                categorySection.style.display = 'block';
                toAccountSection.style.display = 'none';
                document.getElementById('to_account_id').required = false;
                categorySelect.required = true;
            }
            
            // Filter categories by type
            const options = categorySelect.querySelectorAll('option');
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                    return;
                }
                
                const optionType = option.getAttribute('data-type');
                if (type === '' || optionType === type) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Reset category selection if current selection is not valid for new type
            const currentOption = categorySelect.querySelector('option:checked');
            if (currentOption && currentOption.getAttribute('data-type') !== type && type !== '') {
                categorySelect.value = '';
            }
        }
        
        function updateAccountBalance() {
            const accountSelect = document.getElementById('account_id');
            const balanceDiv = document.getElementById('account-balance');
            const balanceAmount = document.getElementById('balance-amount');
            const currencySymbol = document.getElementById('currency-symbol');
            
            const selectedOption = accountSelect.options[accountSelect.selectedIndex];
            
            if (selectedOption.value) {
                const balance = selectedOption.getAttribute('data-balance');
                const currency = selectedOption.getAttribute('data-currency');
                
                balanceAmount.textContent = `${parseFloat(balance).toLocaleString()} ${currency}`;
                currencySymbol.textContent = currency === 'USD' ? '$' : currency;
                balanceDiv.style.display = 'block';
            } else {
                balanceDiv.style.display = 'none';
            }
        }
        
        function toggleRecurringOptions() {
            const checkbox = document.getElementById('is_recurring');
            const options = document.getElementById('recurring-options');
            
            if (checkbox.checked) {
                options.style.display = 'block';
            } else {
                options.style.display = 'none';
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCategoryOptions();
            updateAccountBalance();
            toggleRecurringOptions();
        });
    </script>
</x-app-layout>