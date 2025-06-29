<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Transactions') }}
            </h2>
            <button onclick="openTransactionModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Transaction
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Enhanced Filters with Alpine.js -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6 mb-6" x-data="transactionFilters()">
                <div class="p-6">
                    <form @submit.prevent="applyFilters" class="space-y-4">
                        <!-- Text Search -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-300 mb-1">Search</label>
                                <input type="text" id="search" x-model="filters.search" 
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Search descriptions...">
                            </div>
                            <div>
                                <label for="date_range" class="block text-sm font-medium text-gray-300 mb-1">Date Range</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="date" x-model="filters.start_date" 
                                        class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <input type="date" x-model="filters.end_date" 
                                        class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Amount Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="min_amount" class="block text-sm font-medium text-gray-300 mb-1">Amount Range</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" x-model="filters.min_amount" step="0.01" placeholder="Min"
                                        class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <input type="number" x-model="filters.max_amount" step="0.01" placeholder="Max"
                                        class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Existing Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Account Filter -->
                            <div>
                                <label for="account_id" class="block text-sm font-medium text-gray-300 mb-1">Account</label>
                                <select x-model="filters.account_id" id="account_id" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Accounts</option>
                                    @foreach(auth()->user()->accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-300 mb-1">Category</label>
                                <select x-model="filters.category_id" id="category_id" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Categories</option>
                                    @foreach(auth()->user()->categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Type Filter -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-300 mb-1">Type</label>
                                <select x-model="filters.type" id="type" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Types</option>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-end">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors mr-2">
                                    <i class="fas fa-search mr-1"></i>Filter
                                </button>
                                <button type="button" @click="clearFilters" class="px-4 py-2 bg-gray-600 text-gray-100 rounded-md hover:bg-gray-500 transition-colors">
                                    <i class="fas fa-times mr-1"></i>Clear
                                </button>
                            </div>
                        </div>
                        
                        <!-- Quick Filters -->
                        <div class="border-t border-gray-600 pt-4">
                            <h4 class="text-sm font-medium text-gray-300 mb-3">Quick Filters</h4>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="applyQuickFilter('this_month')" 
                                    class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm hover:bg-blue-700 transition-colors">
                                    This Month
                                </button>
                                <button type="button" @click="applyQuickFilter('last_month')" 
                                    class="bg-purple-600 text-white px-3 py-1 rounded-full text-sm hover:bg-purple-700 transition-colors">
                                    Last Month
                                </button>
                                <button type="button" @click="applyQuickFilter('this_year')" 
                                    class="bg-green-600 text-white px-3 py-1 rounded-full text-sm hover:bg-green-700 transition-colors">
                                    This Year
                                </button>
                                <button type="button" @click="applyQuickFilter('income_only')" 
                                    class="bg-emerald-600 text-white px-3 py-1 rounded-full text-sm hover:bg-emerald-700 transition-colors">
                                    Income Only
                                </button>
                                <button type="button" @click="applyQuickFilter('expenses_only')" 
                                    class="bg-red-600 text-white px-3 py-1 rounded-full text-sm hover:bg-red-700 transition-colors">
                                    Expenses Only
                                </button>
                                <button type="button" @click="applyQuickFilter('under_50')" 
                                    class="bg-yellow-600 text-white px-3 py-1 rounded-full text-sm hover:bg-yellow-700 transition-colors">
                                    Under $50
                                </button>
                                <button type="button" @click="applyQuickFilter('50_to_200')" 
                                    class="bg-orange-600 text-white px-3 py-1 rounded-full text-sm hover:bg-orange-700 transition-colors">
                                    $50-$200
                                </button>
                                <button type="button" @click="applyQuickFilter('over_200')" 
                                    class="bg-pink-600 text-white px-3 py-1 rounded-full text-sm hover:bg-pink-700 transition-colors">
                                    Over $200
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6 mb-6 overflow-hidden sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->date ? $transaction->date->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->account->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($transaction->category)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                          style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}">
                                                        {{ $transaction->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">Uncategorized</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' :
                                                       ($transaction->type === 'expense' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium
                                                {{ $transaction->type === 'income' ? 'text-green-600' :
                                                   ($transaction->type === 'expense' ? 'text-red-600' : 'text-blue-600') }}">
                                                {{ $transaction->type === 'income' ? '+' : ($transaction->type === 'expense' ? '-' : '') }}
                                                {{ number_format($transaction->amount, 2) }} {{ $transaction->account->currency }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                                    <a href="{{ route('transactions.edit', $transaction) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto h-12 w-12 text-gray-400">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first transaction.</p>
                            <div class="mt-6">
                                <button onclick="openTransactionModal()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Add Transaction
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Modal -->
    <div id="transactionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-gray-900">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-100">Add New Transaction</h3>
                    <button onclick="closeTransactionModal()" class="text-gray-400 hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('transactions.store') }}" id="transactionModalForm" class="space-y-4">
                    @csrf

                    <!-- Transaction Type -->
                    <div>
                        <label for="modal_type" class="block text-sm font-medium text-gray-300 mb-1">Transaction Type</label>
                        <select id="modal_type" name="type" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required onchange="updateModalCategoryOptions()">
                            <option value="">Choose transaction type...</option>
                            <option value="income">💰 Income</option>
                            <option value="expense">💸 Expense</option>
                            <option value="transfer">🔄 Transfer</option>
                        </select>
                    </div>

                    <!-- Account -->
                    <div>
                        <label for="modal_account_id" class="block text-sm font-medium text-gray-300 mb-1">From Account</label>
                        <select id="modal_account_id" name="account_id" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select account...</option>
                            @foreach(auth()->user()->accounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->name }} ({{ number_format($account->balance, 2) }} {{ $account->currency }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- To Account (for transfers) -->
                    <div id="modal-to-account-section" style="display: none;">
                        <label for="modal_to_account_id" class="block text-sm font-medium text-gray-300 mb-1">To Account</label>
                        <select id="modal_to_account_id" name="to_account_id" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select destination account...</option>
                            @foreach(auth()->user()->accounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->name }} ({{ number_format($account->balance, 2) }} {{ $account->currency }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category -->
                    <div id="modal-category-section">
                        <label for="modal_category_id" class="block text-sm font-medium text-gray-300 mb-1">Category</label>
                        <select id="modal_category_id" name="category_id" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select category...</option>
                            @foreach(auth()->user()->categories as $category)
                                <option value="{{ $category->id }}" data-type="{{ $category->type }}">
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="modal_amount" class="block text-sm font-medium text-gray-300 mb-1">Amount</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400">$</span>
                            <input type="number" id="modal_amount" name="amount" step="0.01" min="0.01" class="w-full pl-8 pr-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="0.00" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="modal_description" class="block text-sm font-medium text-gray-300 mb-1">Description</label>
                        <input type="text" id="modal_description" name="description" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Transaction description">
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="modal_date" class="block text-sm font-medium text-gray-300 mb-1">Date</label>
                        <input type="date" id="modal_date" name="date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <!-- Reference -->
                    <div>
                        <label for="modal_reference" class="block text-sm font-medium text-gray-300 mb-1">Reference</label>
                        <input type="text" id="modal_reference" name="reference" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Check number, invoice number, etc.">
                        <p class="text-xs text-gray-400 mt-1">Optional reference number or identifier</p>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="modal_notes" class="block text-sm font-medium text-gray-300 mb-1">Notes</label>
                        <textarea id="modal_notes" name="notes" rows="2" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none" placeholder="Additional notes about this transaction..."></textarea>
                        <p class="text-xs text-gray-400 mt-1">Optional additional details or notes</p>
                    </div>

                    <!-- Recurring Transaction -->
                    <div class="flex items-center">
                        <input id="modal_is_recurring" name="is_recurring" type="checkbox" value="1" class="h-4 w-4 text-blue-600 bg-gray-800 border-gray-600 rounded focus:ring-blue-500 focus:ring-2" onchange="toggleModalRecurringOptions()">
                        <label for="modal_is_recurring" class="ml-2 text-sm text-gray-300">
                            🔄 Make this a recurring transaction
                        </label>
                    </div>

                    <!-- Recurring Options -->
                    <div id="modal-recurring-options" class="space-y-3 bg-blue-900 bg-opacity-50 border border-blue-600 rounded-lg p-3" style="display: none;">
                        <h4 class="text-sm font-semibold text-blue-300">Recurring Settings</h4>

                        <div>
                            <label for="modal_recurring_frequency" class="block text-sm font-medium text-gray-300 mb-1">Frequency</label>
                            <select id="modal_recurring_frequency" name="recurring_frequency" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="weekly">📅 Weekly</option>
                                <option value="monthly" selected>🗓️ Monthly</option>
                                <option value="quarterly">📊 Quarterly</option>
                                <option value="yearly">🎯 Yearly</option>
                            </select>
                        </div>

                        <div>
                            <label for="modal_recurring_end_date" class="block text-sm font-medium text-gray-300 mb-1">End Date</label>
                            <input type="date" id="modal_recurring_end_date" name="recurring_end_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-300 mt-1">Leave empty for indefinite recurring</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeTransactionModal()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            Add Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openTransactionModal() {
            document.getElementById('transactionModal').style.display = 'block';
        }

        function closeTransactionModal() {
            document.getElementById('transactionModal').style.display = 'none';
            document.getElementById('transactionModalForm').reset();
            document.getElementById('modal-to-account-section').style.display = 'none';
            document.getElementById('modal-category-section').style.display = 'block';
            document.getElementById('modal-recurring-options').style.display = 'none';
        }

        function toggleModalRecurringOptions() {
            const checkbox = document.getElementById('modal_is_recurring');
            const options = document.getElementById('modal-recurring-options');
            const frequencySelect = document.getElementById('modal_recurring_frequency');

            if (checkbox.checked) {
                options.style.display = 'block';
                frequencySelect.required = true;
            } else {
                options.style.display = 'none';
                frequencySelect.required = false;
            }
        }

        function updateModalCategoryOptions() {
            const type = document.getElementById('modal_type').value;
            const categorySelect = document.getElementById('modal_category_id');
            const toAccountSection = document.getElementById('modal-to-account-section');
            const categorySection = document.getElementById('modal-category-section');

            if (type === 'transfer') {
                toAccountSection.style.display = 'block';
                categorySection.style.display = 'none';
                document.getElementById('modal_to_account_id').required = true;
                categorySelect.required = false;
            } else {
                toAccountSection.style.display = 'none';
                categorySection.style.display = 'block';
                document.getElementById('modal_to_account_id').required = false;
                categorySelect.required = true;

                // Filter categories by type
                const options = categorySelect.querySelectorAll('option');
                options.forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                    } else {
                        const categoryType = option.getAttribute('data-type');
                        if (type === '' || categoryType === type || categoryType === 'both') {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                });
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('transactionModal');
            if (event.target === modal) {
                closeTransactionModal();
            }
        }

        // Alpine.js component for transaction filters
        function transactionFilter() {
            return {
                filters: {
                    search: '',
                    account_id: '',
                    category_id: '',
                    type: '',
                    start_date: '',
                    end_date: '',
                    min_amount: '',
                    max_amount: ''
                },
                loading: false,
                transactions: [],
                pagination: {},

                init() {
                    // Initialize with current URL parameters
                    const urlParams = new URLSearchParams(window.location.search);
                    this.filters.search = urlParams.get('search') || '';
                    this.filters.account_id = urlParams.get('account_id') || '';
                    this.filters.category_id = urlParams.get('category_id') || '';
                    this.filters.type = urlParams.get('type') || '';
                    this.filters.start_date = urlParams.get('start_date') || '';
                    this.filters.end_date = urlParams.get('end_date') || '';
                    this.filters.min_amount = urlParams.get('min_amount') || '';
                    this.filters.max_amount = urlParams.get('max_amount') || '';
                },

                applyFilters() {
                    this.loading = true;
                    
                    // Build query string
                    const params = new URLSearchParams();
                    Object.keys(this.filters).forEach(key => {
                        if (this.filters[key]) {
                            params.append(key, this.filters[key]);
                        }
                    });
                    
                    // Redirect with filters
                    window.location.href = '{{ route("transactions.index") }}?' + params.toString();
                },

                clearFilters() {
                    this.filters = {
                        search: '',
                        account_id: '',
                        category_id: '',
                        type: '',
                        start_date: '',
                        end_date: '',
                        min_amount: '',
                        max_amount: ''
                    };
                    window.location.href = '{{ route("transactions.index") }}';
                },

                applyQuickFilter(type) {
                    const today = new Date();
                    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
                    const endOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                    
                    switch(type) {
                        case 'today':
                            this.filters.start_date = this.formatDate(new Date());
                            this.filters.end_date = this.formatDate(new Date());
                            break;
                        case 'week':
                            this.filters.start_date = this.formatDate(startOfWeek);
                            this.filters.end_date = this.formatDate(endOfWeek);
                            break;
                        case 'month':
                            this.filters.start_date = this.formatDate(startOfMonth);
                            this.filters.end_date = this.formatDate(endOfMonth);
                            break;
                        case 'income':
                            this.filters.type = 'income';
                            break;
                        case 'expense':
                            this.filters.type = 'expense';
                            break;
                        case 'transfer':
                            this.filters.type = 'transfer';
                            break;
                        case 'low':
                            this.filters.max_amount = '50';
                            break;
                        case 'medium':
                            this.filters.min_amount = '50';
                            this.filters.max_amount = '200';
                            break;
                        case 'high':
                            this.filters.min_amount = '200';
                            break;
                    }
                    this.applyFilters();
                },

                formatDate(date) {
                    return date.toISOString().split('T')[0];
                }
            }
        }
    </script>
</x-app-layout>
