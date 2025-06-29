<x-app-layout>
    <div class="container mx-auto px-4 py-6" x-data="transactionSearch()">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Search Transactions</h1>
            <p class="text-gray-600">Find and filter your transactions with advanced search options</p>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form @submit.prevent="searchTransactions" class="space-y-4">
                <!-- Text Search -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search
                            Description</label>
                        <input type="text" id="search" x-model="filters.search"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Search transaction descriptions...">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Transaction
                            Type</label>
                        <select id="type" x-model="filters.type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" id="start_date" x-model="filters.start_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" id="end_date" x-model="filters.end_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Amount Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-1">Minimum
                            Amount</label>
                        <input type="number" id="min_amount" x-model="filters.min_amount" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label for="max_amount" class="block text-sm font-medium text-gray-700 mb-1">Maximum
                            Amount</label>
                        <input type="number" id="max_amount" x-model="filters.max_amount" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="1000.00">
                    </div>
                </div>

                <!-- Category and Account -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category" x-model="filters.category_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="account" class="block text-sm font-medium text-gray-700 mb-1">Account</label>
                        <select id="account" x-model="filters.account_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Accounts</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Search Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <button type="button" @click="clearFilters"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times mr-2"></i>Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Filters</h3>
            <div class="flex flex-wrap gap-2">
                <button @click="applyQuickFilter('today')"
                    class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm hover:bg-green-200">
                    Today
                </button>
                <button @click="applyQuickFilter('this_week')"
                    class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm hover:bg-blue-200">
                    This Week
                </button>
                <button @click="applyQuickFilter('this_month')"
                    class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm hover:bg-purple-200">
                    This Month
                </button>
                <button @click="applyQuickFilter('last_30_days')"
                    class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm hover:bg-yellow-200">
                    Last 30 Days
                </button>
                <button @click="applyQuickFilter('high_amount')"
                    class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm hover:bg-red-200">
                    High Amount (>$500)
                </button>
                <button @click="applyQuickFilter('low_amount')"
                    class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm hover:bg-gray-200">
                    Low Amount (<$50) </button>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">Searching transactions...</p>
        </div>

        <!-- Results -->
        <div x-show="!loading && transactions.length > 0" class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Search Results (<span x-text="transactions.length"></span> transactions found)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="transaction in transactions" :key="transaction.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="formatDate(transaction.date)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="transaction.description"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="transaction.category?.name || 'N/A'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="transaction.account?.name || 'N/A'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                    :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'"
                                    x-text="formatCurrency(transaction.amount, transaction.type)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a :href="`/transactions/${transaction.id}/edit`"
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button @click="deleteTransaction(transaction.id)"
                                        class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- No Results -->
        <div x-show="!loading && searched && transactions.length === 0"
            class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-search text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
            <p class="text-gray-600">Try adjusting your search criteria or clearing filters to see more results.</p>
        </div>
    </div>

    <script>
        function transactionSearch() {
            return {
                loading: false,
                searched: false,
                transactions: [],
                filters: {
                    search: '',
                    type: '',
                    start_date: '',
                    end_date: '',
                    min_amount: '',
                    max_amount: '',
                    category_id: '',
                    account_id: ''
                },

                async searchTransactions() {
                    this.loading = true;
                    this.searched = true;

                    try {
                        const params = new URLSearchParams();
                        Object.keys(this.filters).forEach(key => {
                            if (this.filters[key]) {
                                params.append(key, this.filters[key]);
                            }
                        });

                        const response = await fetch(`/api/search/transactions?${params}`);
                        const data = await response.json();
                        this.transactions = data.transactions || [];
                    } catch (error) {
                        console.error('Search failed:', error);
                        alert('Search failed. Please try again.');
                    } finally {
                        this.loading = false;
                    }
                },

                clearFilters() {
                    this.filters = {
                        search: '',
                        type: '',
                        start_date: '',
                        end_date: '',
                        min_amount: '',
                        max_amount: '',
                        category_id: '',
                        account_id: ''
                    };
                    this.transactions = [];
                    this.searched = false;
                },

                applyQuickFilter(type) {
                    const today = new Date();
                    const formatDate = (date) => date.toISOString().split('T')[0];

                    switch (type) {
                        case 'today':
                            this.filters.start_date = formatDate(today);
                            this.filters.end_date = formatDate(today);
                            break;
                        case 'this_week':
                            const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
                            this.filters.start_date = formatDate(startOfWeek);
                            this.filters.end_date = formatDate(new Date());
                            break;
                        case 'this_month':
                            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                            this.filters.start_date = formatDate(startOfMonth);
                            this.filters.end_date = formatDate(new Date());
                            break;
                        case 'last_30_days':
                            const thirtyDaysAgo = new Date(today.setDate(today.getDate() - 30));
                            this.filters.start_date = formatDate(thirtyDaysAgo);
                            this.filters.end_date = formatDate(new Date());
                            break;
                        case 'high_amount':
                            this.filters.min_amount = '500';
                            this.filters.max_amount = '';
                            break;
                        case 'low_amount':
                            this.filters.min_amount = '';
                            this.filters.max_amount = '50';
                            break;
                    }
                    this.searchTransactions();
                },

                formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString();
                },

                formatCurrency(amount, type) {
                    const formatted = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(Math.abs(amount));
                    return type === 'expense' ? `-${formatted}` : `+${formatted}`;
                },

                async deleteTransaction(id) {
                    if (!confirm('Are you sure you want to delete this transaction?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/transactions/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            this.transactions = this.transactions.filter(t => t.id !== id);
                        } else {
                            alert('Failed to delete transaction.');
                        }
                    } catch (error) {
                        console.error('Delete failed:', error);
                        alert('Failed to delete transaction.');
                    }
                }
            }
        }
    </script>
</x-app-layout>
