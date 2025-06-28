<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Budget Alerts') }}
            </h2>
            <a href="{{ route('budgets.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Budgets
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($alerts->count() > 0)
                <!-- Alert Summary -->
                <div class="bg-orange-900 border border-orange-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-orange-400 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-lg font-medium text-orange-100">
                                {{ $alerts->count() }} Budget Alert{{ $alerts->count() > 1 ? 's' : '' }}
                            </h3>
                            <p class="text-orange-200">
                                You have budgets that need attention. Review them below to stay on track.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Alert Cards -->
                <div class="space-y-4">
                    @foreach($alerts as $budget)
                        <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Budget Header -->
                                    <div class="flex items-center space-x-3 mb-3">
                                        <h3 class="text-lg font-semibold text-gray-100">
                                            {{ $budget->category->name }}
                                        </h3>

                                        @php
                                            $alertColors = [
                                                'warning' => 'bg-orange-100 text-orange-800',
                                                'over' => 'bg-red-100 text-red-800'
                                            ];
                                            $alertIcons = [
                                                'warning' => 'fas fa-exclamation-triangle',
                                                'over' => 'fas fa-times-circle'
                                            ];
                                        @endphp

                                        <span class="px-3 py-1 {{ $alertColors[$budget->status] ?? 'bg-gray-100 text-gray-800' }} text-sm rounded-full flex items-center">
                                            <i class="{{ $alertIcons[$budget->status] ?? 'fas fa-info-circle' }} mr-1"></i>
                                            @if($budget->status === 'over')
                                                Over Budget
                                            @elseif($budget->status === 'warning')
                                                Near Limit
                                            @else
                                                {{ ucfirst($budget->status) }}
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Budget Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-400">Period</p>
                                            <p class="text-gray-100 font-medium">{{ $budget->period_label }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Budget Amount</p>
                                            <p class="text-gray-100 font-medium">${{ number_format($budget->amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Spent Amount</p>
                                            <p class="text-gray-100 font-medium">${{ number_format($budget->spent_amount, 2) }}</p>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm text-gray-300 mb-2">
                                            <span>Progress: {{ $budget->progress_percentage }}%</span>
                                            @if($budget->remaining_amount > 0)
                                                <span class="text-green-400">${{ number_format($budget->remaining_amount, 2) }} remaining</span>
                                            @else
                                                <span class="text-red-400">${{ number_format($budget->over_budget_amount, 2) }} over budget</span>
                                            @endif
                                        </div>

                                        <div class="w-full bg-gray-700 rounded-full h-3">
                                            @php
                                                $progressColors = [
                                                    'warning' => 'bg-orange-500',
                                                    'over' => 'bg-red-500'
                                                ];
                                            @endphp
                                            <div class="{{ $progressColors[$budget->status] ?? 'bg-gray-500' }} h-3 rounded-full transition-all duration-300"
                                                 style="width: {{ min(100, $budget->progress_percentage) }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Alert Message -->
                                    <div class="p-3 rounded-lg {{ $budget->status === 'over' ? 'bg-red-900 border border-red-700' : 'bg-orange-900 border border-orange-700' }}">
                                        @if($budget->status === 'over')
                                            <p class="text-red-100 text-sm">
                                                <i class="fas fa-exclamation-circle mr-2"></i>
                                                <strong>Budget Exceeded:</strong> You've spent ${{ number_format($budget->over_budget_amount, 2) }} more than your budget limit. Consider reviewing your expenses or adjusting your budget.
                                            </p>
                                        @else
                                            <p class="text-orange-100 text-sm">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                <strong>Approaching Limit:</strong> You've used {{ $budget->progress_percentage }}% of your budget. You have ${{ number_format($budget->remaining_amount, 2) }} remaining.
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col space-y-2 ml-4">
                                    <a href="{{ route('budgets.show', $budget) }}"
                                       class="text-blue-400 hover:text-blue-300 text-sm text-center">
                                        <i class="fas fa-eye mr-1"></i>View Details
                                    </a>
                                    <a href="{{ route('budgets.edit', $budget) }}"
                                       class="text-yellow-400 hover:text-yellow-300 text-sm text-center">
                                        <i class="fas fa-edit mr-1"></i>Edit Budget
                                    </a>
                                    <a href="{{ route('transactions.index', ['category_id' => $budget->category_id]) }}"
                                       class="text-green-400 hover:text-green-300 text-sm text-center">
                                        <i class="fas fa-list mr-1"></i>View Transactions
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 text-center">
                    <div class="space-x-4">
                        <a href="{{ route('budgets.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create New Budget
                        </a>
                        <a href="{{ route('transactions.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Transaction
                        </a>
                    </div>
                </div>
            @else
                <!-- No Alerts State -->
                <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-check-circle text-3xl text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-100 mb-2">All Good!</h3>
                    <p class="text-gray-400 mb-6">
                        You don't have any budget alerts at the moment. All your budgets are within healthy limits.
                    </p>
                    <div class="space-x-3">
                        <a href="{{ route('budgets.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-gray-800 transition-colors">
                            <i class="fas fa-chart-pie mr-2"></i>View All Budgets
                        </a>
                        <a href="{{ route('budgets.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create New Budget
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
