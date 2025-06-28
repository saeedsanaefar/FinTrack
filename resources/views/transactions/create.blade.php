<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Add New Transaction') }}
            </h2>
            <a href="{{ route('transactions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Transactions
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 overflow-hidden shadow-xl sm:rounded-xl border border-gray-700">
                <div class="bg-gradient-to-r from-gray-800 to-gray-700 px-8 py-6 border-b border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-100 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Transaction
                    </h3>
                    <p class="text-sm text-gray-300 mt-1">Add a new income, expense, or transfer transaction</p>
                </div>
                <div class="p-8 bg-gray-900">
                    <form method="POST" action="{{ route('transactions.store') }}" id="transactionForm" class="space-y-6">
                        @csrf

                        <!-- Transaction Type -->
                        <div class="space-y-2">
                            <x-input-label for="type" :value="__('Transaction Type')" :required="true" />
                            <select id="type" name="type" class="block w-full bg-gray-800 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500" required onchange="updateCategoryOptions()">
                                <option value="" class="text-gray-500">Choose transaction type...</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>💰 Income</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>💸 Expense</option>
                                <option value="transfer" {{ old('type') == 'transfer' ? 'selected' : '' }}>🔄 Transfer</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Account -->
                        <div class="space-y-2">
                            <x-input-label for="account_id" :value="__('From Account')" :required="true" />
                            <select id="account_id" name="account_id" class="block w-full bg-gray-800 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500" required onchange="updateAccountBalance()">
                                <option value="" class="text-gray-500">Select account...</option>
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
                            <div id="account-balance" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800" style="display: none;">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                    </svg>
                                    Current Balance: <span id="balance-amount" class="font-semibold"></span>
                                </div>
                            </div>
                        </div>

                        <!-- To Account (for transfers) -->
                        <div class="space-y-2" id="to-account-section" style="display: none;">
                            <x-input-label for="to_account_id" :value="__('To Account')" :required="true" />
                            <select id="to_account_id" name="to_account_id" class="block w-full bg-gray-800 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500">
                                <option value="" class="text-gray-500">Select destination account...</option>
                                @foreach(auth()->user()->accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ number_format($account->balance, 2) }} {{ $account->currency }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('to_account_id')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div class="space-y-2" id="category-section">
                            <x-input-label for="category_id" :value="__('Category')" :required="true" />
                            <select id="category_id" name="category_id" class="block w-full bg-gray-800 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500">
                                <option value="" class="text-gray-500">Select category...</option>
                                @foreach(auth()->user()->categories as $category)
                                    <option value="{{ $category->id }}"
                                            data-type="{{ $category->type }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->icon }} {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Amount -->
                        <div class="space-y-2">
                            <x-input-label for="amount" :value="__('Amount')" :required="true" />
                            <div class="relative">
                                <span id="currency-symbol" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">$</span>
                                <x-text-input id="amount" class="block w-full pl-10 pr-4 py-3 text-lg font-medium" type="number" name="amount" :value="old('amount')" step="0.01" min="0.01" required placeholder="0.00" />
                            </div>
                            <p class="text-xs text-gray-500">Enter the transaction amount</p>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <x-input-label for="description" :value="__('Description')" :required="true" />
                            <x-text-input id="description" class="block w-full" type="text" name="description" :value="old('description')" required placeholder="What was this transaction for?" />
                            <p class="text-xs text-gray-500">Brief description of the transaction</p>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Transaction Date -->
                        <div class="space-y-2">
                            <x-input-label for="date" :value="__('Transaction Date')" :required="true" />
                            <x-text-input id="date" class="block w-full" type="date" name="date" :value="old('date', date('Y-m-d'))" max="{{ date('Y-m-d') }}" required />
                            <p class="text-xs text-gray-500">When did this transaction occur?</p>
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <!-- Reference (Optional) -->
                        <div class="space-y-2">
                            <x-input-label for="reference" :value="__('Reference')" />
                            <x-text-input id="reference" class="block w-full" type="text" name="reference" :value="old('reference')" placeholder="Check number, invoice number, etc." />
                            <p class="text-xs text-gray-500">Optional reference number or identifier</p>
                            <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                        </div>

                        <!-- Notes (Optional) -->
                        <div class="space-y-2">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" class="block w-full bg-gray-800 border-gray-600 text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500 resize-none" placeholder="Additional notes about this transaction...">{{ old('notes') }}</textarea>
                            <p class="text-xs text-gray-500">Optional additional details or notes</p>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Recurring Transaction -->
                        <div class="space-y-2">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <input id="is_recurring" name="is_recurring" type="checkbox" value="1"
                                           {{ old('is_recurring') ? 'checked' : '' }}
                                           class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all duration-200"
                                           onchange="toggleRecurringOptions()">
                                    <label for="is_recurring" class="ml-3 block text-sm font-medium text-gray-900">
                                        🔄 Make this a recurring transaction
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 ml-8">Automatically create this transaction on a schedule</p>
                            </div>
                        </div>

                        <!-- Recurring Options -->
                        <div id="recurring-options" class="space-y-4 bg-blue-50 border border-blue-200 rounded-lg p-4" style="display: none;">
                            <h4 class="text-sm font-semibold text-blue-900 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                Recurring Settings
                            </h4>

                            <div class="space-y-2">
                                <x-input-label for="recurring_frequency" :value="__('Frequency')" :required="true" />
                                <select id="recurring_frequency" name="recurring_frequency" class="block w-full bg-gray-800 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500">
                                    <option value="weekly" {{ old('recurring_frequency') == 'weekly' ? 'selected' : '' }}>📅 Weekly</option>
                                    <option value="monthly" {{ old('recurring_frequency') == 'monthly' ? 'selected' : '' }}>🗓️ Monthly</option>
                                    <option value="quarterly" {{ old('recurring_frequency') == 'quarterly' ? 'selected' : '' }}>📊 Quarterly</option>
                                    <option value="yearly" {{ old('recurring_frequency') == 'yearly' ? 'selected' : '' }}>🎯 Yearly</option>
                                </select>
                                <x-input-error :messages="$errors->get('recurring_frequency')" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <x-input-label for="recurring_end_date" :value="__('End Date')" />
                                <x-text-input id="recurring_end_date" class="block w-full" type="date" name="recurring_end_date" :value="old('recurring_end_date')" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                                <p class="text-xs text-blue-600">Leave empty for indefinite recurring</p>
                                <x-input-error :messages="$errors->get('recurring_end_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <x-secondary-button onclick="window.location.href='{{ route('transactions.index') }}'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Cancel
                            </x-secondary-button>
                            <x-primary-button class="ml-4">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
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
