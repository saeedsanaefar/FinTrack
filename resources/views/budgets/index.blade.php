<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Budgets') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('budgets.alerts') }}"
                   class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Budget Alerts
                </a>
                <a href="{{ route('budgets.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add New Budget
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6 mb-6">
                <form method="GET" action="{{ route('budgets.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-300 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Search budgets..."
                                   class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-300 mb-1">Category</label>
                            <select name="category_id" id="category_id"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Period Type Filter -->
                        <div>
                            <label for="period_type" class="block text-sm font-medium text-gray-300 mb-1">Period</label>
                            <select name="period_type" id="period_type"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Periods</option>
                                <option value="monthly" {{ request('period_type') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ request('period_type') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-300 mb-1">Year</label>
                            <select name="year" id="year"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Years</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('budgets.index') }}"
                           class="px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-gray-800 transition-colors">
                            Clear
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            @if($budgets->count() > 0)
                <!-- Budgets Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($budgets as $budget)
                        <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6">
                            <!-- Budget Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-100">
                                        {{ $budget->category->name }}
                                    </h3>
                                    <p class="text-sm text-gray-400">
                                        {{ ucfirst($budget->period_type) }} - {{ $budget->period_label }}
                                    </p>
                                </div>
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

                                    <!-- Status Badge -->
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

                            <!-- Budget Progress -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-300 mb-2">
                                    <span>Spent: ${{ number_format($budget->spent_amount, 2) }}</span>
                                    <span>Budget: ${{ number_format($budget->amount, 2) }}</span>
                                </div>

                                <!-- Progress Bar -->
                                <div class="w-full bg-gray-700 rounded-full h-3">
                                    @php
                                        $progressColors = [
                                            'good' => 'bg-green-500',
                                            'caution' => 'bg-yellow-500',
                                            'warning' => 'bg-orange-500',
                                            'over' => 'bg-red-500'
                                        ];
                                    @endphp
                                    <div class="{{ $progressColors[$budget->status] ?? 'bg-gray-500' }} h-3 rounded-full transition-all duration-300"
                                         style="width: {{ min(100, $budget->progress_percentage) }}%"></div>
                                </div>

                                <div class="flex justify-between text-xs text-gray-400 mt-1">
                                    <span>{{ $budget->progress_percentage }}% used</span>
                                    @if($budget->remaining_amount > 0)
                                        <span class="text-green-400">${{ number_format($budget->remaining_amount, 2) }} remaining</span>
                                    @else
                                        <span class="text-red-400">${{ number_format($budget->over_budget_amount, 2) }} over budget</span>
                                    @endif
                                </div>
                            </div>

                            @if($budget->notes)
                                <p class="text-sm text-gray-400 mb-4">{{ $budget->notes }}</p>
                            @endif

                            <!-- Actions -->
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('budgets.show', $budget) }}"
                                   class="text-blue-400 hover:text-blue-300 text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="{{ route('budgets.edit', $budget) }}"
                                   class="text-yellow-400 hover:text-yellow-300 text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('budgets.destroy', $budget) }}"
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this budget?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-sm">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $budgets->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-chart-pie text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-100 mb-2">No budgets found</h3>
                    <p class="text-gray-400 mb-6">
                        @if(request()->hasAny(['search', 'category_id', 'period_type', 'year', 'status']))
                            No budgets match your current filters. Try adjusting your search criteria.
                        @else
                            You haven't created any budgets yet. Budgets help you track and control your spending.
                        @endif
                    </p>
                    <div class="space-x-3">
                        @if(request()->hasAny(['search', 'category_id', 'period_type', 'year', 'status']))
                            <a href="{{ route('budgets.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-gray-800 transition-colors">
                                <i class="fas fa-times mr-2"></i>Clear Filters
                            </a>
                        @endif
                        <a href="{{ route('budgets.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create Your First Budget
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
