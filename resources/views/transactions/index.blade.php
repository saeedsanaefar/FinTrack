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
            <!-- Filters -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6 mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Account Filter -->
                        <div>
                            <label for="account_id" class="block text-sm font-medium text-gray-700 mb-1">Account</label>
                            <select name="account_id" id="account_id" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Accounts</option>
                                @foreach(auth()->user()->accounts as $account)
                                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id" id="category_id" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Categories</option>
                                @foreach(auth()->user()->categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" id="type" class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Types</option>
                                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors mr-2">
                                Filter
                            </button>
                            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-800 border border-gray-500 rounded-md text-gray-100 hover:bg-gray-500 transition-colors">
                                Clear
                            </a>
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
    </script>
</x-app-layout>
