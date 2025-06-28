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
                    Welcome back, <span
                        class="font-medium text-blue-600 dark:text-blue-400">{{ Auth::user()->name }}</span>! Here's your
                    financial overview.
                </p>
            </div>
            <div class="space-x-2 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex space-x-3">
                    <button
                        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                            </path>
                        </svg>
                        Add Transaction
                    </button>
                    <button
                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11l5-5m0 0l5 5m-5-5v12">
                            </path>
                        </svg>
                        Add Income
                    </button>
                </div>
                <div class="flex items-center hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Last updated: {{ now()->format('M d, Y \a\t g:i A') }}
                </div>
            </div>
        </div>
    </x-slot>

    {{-- @push('header-actions') --}}

    {{-- @endpush --}}

    <div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-8 animate-slide-in-left">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Good
                                {{ now()->format('A') === 'AM' ? 'Morning' : 'Evening' }}, {{ Auth::user()->name }}! 👋
                            </h2>
                            <p class="text-blue-100 text-lg">Ready to take control of your finances today?</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 animate-fade-in">
                <!-- Total Balance -->
                <div
                    class="bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-xl rounded-2xl border border-blue-200 dark:border-blue-800 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6 relative">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full opacity-10 transform translate-x-8 -translate-y-8">
                        </div>
                        <div class="flex items-center relative z-10">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Balance</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">
                                    ${{ number_format($totalBalance, 2) }}</p>
                                <p
                                    class="text-xs {{ $balanceChange >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium mt-1">
                                    {{ $balanceChange >= 0 ? '+' : '' }}{{ number_format($balanceChange, 2) }}% from
                                    last month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Income -->
                <div
                    class="bg-gradient-to-br from-white to-green-50 dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-xl rounded-2xl border border-green-200 dark:border-green-800 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6 relative">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full opacity-10 transform translate-x-8 -translate-y-8">
                        </div>
                        <div class="flex items-center relative z-10">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">This Month Income
                                </p>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">
                                    ${{ number_format($monthlyIncome, 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                                    {{ $incomeTransactionCount }}
                                    {{ Str::plural('transaction', $incomeTransactionCount) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Expenses -->
                <div
                    class="bg-gradient-to-br from-white to-red-50 dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-xl rounded-2xl border border-red-200 dark:border-red-800 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6 relative">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-400 to-red-600 rounded-full opacity-10 transform translate-x-8 -translate-y-8">
                        </div>
                        <div class="flex items-center relative z-10">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">This Month Expenses
                                </p>
                                <p class="text-3xl font-bold text-red-600 dark:text-red-400 tracking-tight">
                                    ${{ number_format($monthlyExpenses, 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                                    {{ $expenseTransactionCount }}
                                    {{ Str::plural('transaction', $expenseTransactionCount) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Savings Goal -->
                <div
                    class="bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-xl rounded-2xl border border-purple-200 dark:border-purple-800 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6 relative">
                        <div
                            class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full opacity-10 transform translate-x-8 -translate-y-8">
                        </div>
                        <div class="flex items-center relative z-10">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Savings Rate</p>
                                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 tracking-tight">
                                    {{ number_format(max(0, $savingsRate), 1) }}%</p>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mt-2">
                                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full"
                                        style="width: {{ min(100, max(0, $savingsRate)) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Alerts & Progress -->
            @if($activeBudgetsCount > 0)
                <div class="mb-8">
                    <!-- Budget Alerts -->
                    @if($budgetAlerts->count() > 0)
                        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-2xl p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-800 rounded-xl flex items-center justify-center mr-3">
                                        <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-orange-900 dark:text-orange-100">
                                            {{ $budgetAlerts->count() }} Budget Alert{{ $budgetAlerts->count() > 1 ? 's' : '' }}
                                        </h3>
                                        <p class="text-sm text-orange-700 dark:text-orange-300">
                                            Some budgets need your attention
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('budgets.alerts') }}" 
                                   class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-medium transition-colors">
                                    View All Alerts
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($budgetAlerts->take(2) as $budget)
                                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-orange-200 dark:border-orange-700">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $budget->category->name }}</h4>
                                            @php
                                                $alertColors = [
                                                    'warning' => 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200',
                                                    'over' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 {{ $alertColors[$budget->status] ?? 'bg-gray-100 text-gray-800' }} text-xs rounded-full">
                                                @if($budget->status === 'over')
                                                    Over Budget
                                                @else
                                                    {{ $budget->progress_percentage }}% Used
                                                @endif
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                                <span>${{ number_format($budget->spent_amount, 2) }}</span>
                                                <span>${{ number_format($budget->amount, 2) }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                @php
                                                    $progressColors = [
                                                        'warning' => 'bg-orange-500',
                                                        'over' => 'bg-red-500'
                                                    ];
                                                @endphp
                                                <div class="{{ $progressColors[$budget->status] ?? 'bg-gray-500' }} h-2 rounded-full transition-all duration-300" 
                                                     style="width: {{ min(100, $budget->progress_percentage) }}%"></div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $budget->period_label }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Current Month Budget Progress -->
                    @if($currentMonthBudgets->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">This Month's Budgets</h3>
                                <a href="{{ route('budgets.index') }}" 
                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium transition-colors">
                                    Manage Budgets
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($currentMonthBudgets->take(6) as $budget)
                                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $budget->category->name }}</h4>
                                            @php
                                                $statusColors = [
                                                    'good' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
                                                    'caution' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
                                                    'warning' => 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200',
                                                    'over' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 {{ $statusColors[$budget->status] ?? 'bg-gray-100 text-gray-800' }} text-xs rounded-full">
                                                {{ $budget->progress_percentage }}%
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                                <span>${{ number_format($budget->spent_amount, 2) }}</span>
                                                <span>${{ number_format($budget->amount, 2) }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                @php
                                                    $progressColors = [
                                                        'good' => 'bg-green-500',
                                                        'caution' => 'bg-yellow-500',
                                                        'warning' => 'bg-orange-500',
                                                        'over' => 'bg-red-500'
                                                    ];
                                                @endphp
                                                <div class="{{ $progressColors[$budget->status] ?? 'bg-gray-500' }} h-2 rounded-full transition-all duration-300" 
                                                     style="width: {{ min(100, $budget->progress_percentage) }}%"></div>
                                            </div>
                                        </div>
                                        @if($budget->remaining_amount > 0)
                                            <p class="text-xs text-green-600 dark:text-green-400">
                                                ${{ number_format($budget->remaining_amount, 2) }} remaining
                                            </p>
                                        @else
                                            <p class="text-xs text-red-600 dark:text-red-400">
                                                ${{ number_format($budget->over_budget_amount, 2) }} over
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Recent Transactions & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Transactions -->
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Recent Transactions</h3>
                                <a href="{{ route('transactions.index') }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium transition-colors">
                                    View All
                                </a>
                            </div>
                            @if ($recentTransactions->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($recentTransactions as $transaction)
                                        <div
                                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-12 h-12 rounded-xl flex items-center justify-center mr-4 {{ $transaction->type === 'income' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                                    @if ($transaction->category && $transaction->category->icon)
                                                        <i
                                                            class="fas fa-{{ $transaction->category->icon }} {{ $transaction->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"></i>
                                                    @else
                                                        <svg class="w-5 h-5 {{ $transaction->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            @if ($transaction->type === 'income')
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12">
                                                                </path>
                                                            @else
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6">
                                                                </path>
                                                            @endif
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $transaction->description }}</p>
                                                    <div
                                                        class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                                        <span>{{ $transaction->account->name }}</span>
                                                        <span>•</span>
                                                        <span>{{ $transaction->category->name ?? 'Uncategorized' }}</span>
                                                        <span>•</span>
                                                        <span>{{ $transaction->date->format('M d') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p
                                                    class="font-bold {{ $transaction->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $transaction->type === 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $transaction->date->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div
                                        class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No
                                        transactions yet</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Start tracking your
                                        finances by adding your first transaction</p>
                                    <a href="{{ route('transactions.create') }}"
                                        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 transform hover:scale-105 shadow-lg inline-block">
                                        Add Transaction
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Quick Actions</h3>
                        <div class="space-y-4">
                            <a href="{{ route('transactions.create', ['type' => 'expense']) }}"
                                class="block w-full text-left p-4 rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800/30 dark:hover:to-blue-700/30 transition-all duration-200 transform hover:scale-105 group">
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Add Expense</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Record a new expense</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('transactions.create', ['type' => 'income']) }}"
                                class="block w-full text-left p-4 rounded-xl bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700 hover:from-green-100 hover:to-green-200 dark:hover:from-green-800/30 dark:hover:to-green-700/30 transition-all duration-200 transform hover:scale-105 group">
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Add Income</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Record new income</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('accounts.index') }}"
                                class="block w-full text-left p-4 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 hover:from-purple-100 hover:to-purple-200 dark:hover:from-purple-800/30 dark:hover:to-purple-700/30 transition-all duration-200 transform hover:scale-105 group">
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Manage Accounts
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">View and edit accounts</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('transactions.index') }}"
                                class="block w-full text-left p-4 rounded-xl bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border border-orange-200 dark:border-orange-700 hover:from-orange-100 hover:to-orange-200 dark:hover:from-orange-800/30 dark:hover:to-orange-700/30 transition-all duration-200 transform hover:scale-105 group">
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">View
                                            Transactions</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Browse all transactions</p>
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('budgets.index') }}"
                                class="block w-full text-left p-4 rounded-xl bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 border border-indigo-200 dark:border-indigo-700 hover:from-indigo-100 hover:to-indigo-200 dark:hover:from-indigo-800/30 dark:hover:to-indigo-700/30 transition-all duration-200 transform hover:scale-105 group">
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-shadow">
                                        <i class="fas fa-chart-pie text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Manage Budgets</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Track spending limits</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Account Breakdown -->
                <div class="mt-8">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Account Breakdown</h3>
                            <a href="{{ route('accounts.index') }}"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm transition-colors">
                                Manage Accounts
                            </a>
                        </div>

                        @if ($accountBreakdown->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($accountBreakdown as $account)
                                    <div
                                        class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $account['name'] }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 capitalize">
                                                    {{ $account['type'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p
                                                    class="font-bold text-lg {{ $account['balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    ${{ number_format($account['balance'], 2) }}
                                                </p>
                                            </div>
                                        </div>
                                        @if ($account['description'])
                                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                                {{ $account['description'] }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No accounts yet
                                </h4>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Create your first account to start
                                    tracking your finances.</p>
                                <a href="{{ route('accounts.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Account
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
