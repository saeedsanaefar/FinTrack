@push('styles')
    @vite(['resources/css/dashboard.css'])
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Financial Dashboard') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Welcome back, <span class="font-medium text-blue-600 dark:text-blue-400">{{ Auth::user()->name }}</span>! Here's your financial overview.
                </p>
            </div>
            <div class="hidden md:flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Last updated: {{ now()->format('M d, Y \a\t g:i A') }}</span>
            </div>
        </div>
    </x-slot>

    @push('header-actions')
        <div class="flex space-x-3">
            <button class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Transaction
            </button>
            <button class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                </svg>
                Add Income
            </button>
        </div>
    @endpush

    <div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-8 animate-slide-in-left">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Good {{ now()->format('A') === 'AM' ? 'Morning' : 'Evening' }}, {{ Auth::user()->name }}! 👋</h2>
                            <p class="text-blue-100 text-lg">Ready to take control of your finances today?</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 animate-fade-in">
                <!-- Total Balance -->
                <div class="bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-xl rounded-2xl border border-blue-200 dark:border-blue-800 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full opacity-10 transform translate-x-8 -translate-y-8"></div>
                        <div class="flex items-center relative z-10">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Balance</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">$0.00</p>
                                <p class="text-xs text-green-600 dark:text-green-400 font-medium mt-1">+0.00% from last month</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Income -->
                <div class="bg-gradient-to-br from-white to-green-50 dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-xl rounded-2xl border border-green-200 dark:border-green-800 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full opacity-10 transform translate-x-8 -translate-y-8"></div>
                        <div class="flex items-center relative z-10">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">This Month Income</p>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">$0.00</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">0 transactions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Expenses -->
                <div class="bg-gradient-to-br from-white to-red-50 dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-xl rounded-2xl border border-red-200 dark:border-red-800 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-400 to-red-600 rounded-full opacity-10 transform translate-x-8 -translate-y-8"></div>
                        <div class="flex items-center relative z-10">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">This Month Expenses</p>
                                <p class="text-3xl font-bold text-red-600 dark:text-red-400 tracking-tight">$0.00</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">0 transactions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Savings Goal -->
                <div class="bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-xl rounded-2xl border border-purple-200 dark:border-purple-800 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full opacity-10 transform translate-x-8 -translate-y-8"></div>
                        <div class="flex items-center relative z-10">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Savings Goal</p>
                                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 tracking-tight">0%</p>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mt-2">
                                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Transactions -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Recent Transactions</h3>
                                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium transition-colors">
                                    View All
                                </button>
                            </div>
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No transactions yet</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Start tracking your finances by adding your first transaction</p>
                                <button class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
                                    Add Transaction
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Quick Actions</h3>
                        <div class="space-y-4">
                            <button class="w-full text-left p-4 rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800/30 dark:hover:to-blue-700/30 transition-all duration-200 transform hover:scale-105 group">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Add Expense</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Record a new expense</p>
                                    </div>
                                </div>
                            </button>

                            <button class="w-full text-left p-4 rounded-xl bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700 hover:from-green-100 hover:to-green-200 dark:hover:from-green-800/30 dark:hover:to-green-700/30 transition-all duration-200 transform hover:scale-105 group">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Add Income</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Record new income</p>
                                    </div>
                                </div>
                            </button>

                            <button class="w-full text-left p-4 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 hover:from-purple-100 hover:to-purple-200 dark:hover:from-purple-800/30 dark:hover:to-purple-700/30 transition-all duration-200 transform hover:scale-105 group">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Set Budget</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Create monthly budget</p>
                                    </div>
                                </div>
                            </button>

                            <button class="w-full text-left p-4 rounded-xl bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border border-orange-200 dark:border-orange-700 hover:from-orange-100 hover:to-orange-200 dark:hover:from-orange-800/30 dark:hover:to-orange-700/30 transition-all duration-200 transform hover:scale-105 group">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">View Reports</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Analyze your finances</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
