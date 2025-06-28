<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Budget Details') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('budgets.edit', $budget) }}"
                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Budget
                </a>
                <a href="{{ route('budgets.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Budgets
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Budget Overview -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Budget Info -->
                    <div>
                        <h3 class="text-xl font-semibold text-gray-100 mb-4">{{ $budget->category->name }}</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Period:</span>
                                <span class="text-gray-100">{{ ucfirst($budget->period_type) }} - {{ $budget->period_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Budget Amount:</span>
                                <span class="text-gray-100 font-semibold">${{ number_format($budget->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Spent Amount:</span>
                                <span class="text-gray-100 font-semibold">${{ number_format($budget->spent_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Status:</span>
                                <div class="flex items-center space-x-2">
                                    @if($budget->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                            Inactive
                                        </span>
                                    @endif

                                    @php
                                        $statusColors = [
                                            'good' => 'bg-green-100 text-green-800',
                                            'caution' => 'bg-yellow-100 text-yellow-800',
                                            'warning' => 'bg-orange-100 text-orange-800',
                                            'over' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 {{ $statusColors[$budget->status] ?? 'bg-gray-100 text-gray-800' }} text-xs rounded-full">
                                        {{ ucfirst($budget->status) }}
                                    </span>
                                </div>
                            </div>
                            @if($budget->remaining_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Remaining:</span>
                                    <span class="text-green-400 font-semibold">${{ number_format($budget->remaining_amount, 2) }}</span>
                                </div>
                            @else
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Over Budget:</span>
                                    <span class="text-red-400 font-semibold">${{ number_format($budget->over_budget_amount, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Visualization -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-100 mb-4">Budget Progress</h4>

                        <!-- Circular Progress -->
                        <div class="flex items-center justify-center mb-4">
                            <div class="relative w-32 h-32">
                                <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                                    <!-- Background circle -->
                                    <path class="text-gray-700" stroke="currentColor" stroke-width="3" fill="none"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <!-- Progress circle -->
                                    @php
                                        $progressColors = [
                                            'good' => 'text-green-500',
                                            'caution' => 'text-yellow-500',
                                            'warning' => 'text-orange-500',
                                            'over' => 'text-red-500'
                                        ];
                                        $strokeDasharray = min(100, $budget->progress_percentage);
                                    @endphp
                                    <path class="{{ $progressColors[$budget->status] ?? 'text-gray-500' }}" stroke="currentColor"
                                          stroke-width="3" fill="none" stroke-linecap="round"
                                          stroke-dasharray="{{ $strokeDasharray }}, 100"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xl font-bold text-gray-100">{{ $budget->progress_percentage }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Linear Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-300 mb-2">
                                <span>Progress</span>
                                <span>{{ $budget->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-4">
                                @php
                                    $progressBarColors = [
                                        'good' => 'bg-green-500',
                                        'caution' => 'bg-yellow-500',
                                        'warning' => 'bg-orange-500',
                                        'over' => 'bg-red-500'
                                    ];
                                @endphp
                                <div class="{{ $progressBarColors[$budget->status] ?? 'bg-gray-500' }} h-4 rounded-full transition-all duration-300"
                                     style="width: {{ min(100, $budget->progress_percentage) }}%"></div>
                            </div>
                        </div>

                        @if($budget->notes)
                            <div class="mt-4">
                                <h5 class="text-sm font-medium text-gray-300 mb-2">Notes:</h5>
                                <p class="text-sm text-gray-400">{{ $budget->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-100">Recent Transactions</h3>
                    <a href="{{ route('transactions.index', ['category_id' => $budget->category_id]) }}"
                       class="text-blue-400 hover:text-blue-300 text-sm">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                @if($recentTransactions->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTransactions as $transaction)
                            <div class="flex justify-between items-center p-3 bg-gray-800 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-minus text-red-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-100 truncate">
                                                {{ $transaction->description }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $transaction->account->name }} • {{ $transaction->date->format('M j, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-red-400">
                                        -${{ number_format($transaction->amount, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="mx-auto w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-receipt text-2xl text-gray-400"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-100 mb-2">No transactions yet</h4>
                        <p class="text-gray-400 mb-4">
                            No expenses have been recorded for this budget period.
                        </p>
                        <a href="{{ route('transactions.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Transaction
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
