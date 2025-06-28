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
                <button onclick="openBudgetModal()"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add New Budget
                </button>
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
                        <button onclick="openBudgetModal()"
                                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create Your First Budget
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Budget Modal -->
    <div id="budgetModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-gray-900">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-100">Add New Budget</h3>
                    <button onclick="closeBudgetModal()" class="text-gray-400 hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="budgetForm" method="POST" action="{{ route('budgets.store') }}" class="space-y-4">
                    @csrf
                    
                    <!-- Category -->
                    <div>
                        <label for="modal_category_id" class="block text-sm font-medium text-gray-300 mb-1">
                            Category <span class="text-red-400">*</span>
                        </label>
                        <select name="category_id" id="modal_category_id" required
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Budget Amount -->
                    <div>
                        <label for="modal_amount" class="block text-sm font-medium text-gray-300 mb-1">
                            Budget Amount <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-400">$</span>
                            <input type="number" name="amount" id="modal_amount" step="0.01" min="0.01" required
                                   placeholder="0.00"
                                   class="w-full pl-8 pr-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <!-- Period Type -->
                    <div>
                        <label for="modal_period_type" class="block text-sm font-medium text-gray-300 mb-1">
                            Budget Period <span class="text-red-400">*</span>
                        </label>
                        <select name="period_type" id="modal_period_type" required
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="monthly" selected>Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    
                    <!-- Year -->
                    <div>
                        <label for="modal_year" class="block text-sm font-medium text-gray-300 mb-1">
                            Year <span class="text-red-400">*</span>
                        </label>
                        <select name="year" id="modal_year" required
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Month (for monthly budgets) -->
                    <div id="modal-month-field">
                        <label for="modal_month" class="block text-sm font-medium text-gray-300 mb-1">
                            Month <span class="text-red-400">*</span>
                        </label>
                        <select name="month" id="modal_month" required
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>January</option>
                            <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>February</option>
                            <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>March</option>
                            <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                            <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>May</option>
                            <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>June</option>
                            <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>July</option>
                            <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>August</option>
                            <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                            <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>October</option>
                            <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                            <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>December</option>
                        </select>
                    </div>
                    
                    <!-- Notes -->
                    <div>
                        <label for="modal_notes" class="block text-sm font-medium text-gray-300 mb-1">
                            Notes
                        </label>
                        <textarea name="notes" id="modal_notes" rows="2"
                                  placeholder="Optional notes about this budget..."
                                  class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="modal_is_active" value="1" checked
                               class="h-4 w-4 text-blue-600 bg-gray-800 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="modal_is_active" class="ml-2 text-sm text-gray-300">
                            Active budget
                        </label>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeBudgetModal()"
                                class="px-4 py-2 bg-gray-600 text-gray-100 rounded-md hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-save mr-2"></i>Create Budget
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openBudgetModal() {
            document.getElementById('budgetModal').classList.remove('hidden');
            document.getElementById('modal_category_id').focus();
        }
        
        function closeBudgetModal() {
            document.getElementById('budgetModal').classList.add('hidden');
            document.getElementById('budgetForm').reset();
            // Reset to default values
            document.getElementById('modal_period_type').value = 'monthly';
            document.getElementById('modal_year').value = '{{ date('Y') }}';
            document.getElementById('modal_month').value = '{{ date('n') }}';
            document.getElementById('modal_is_active').checked = true;
            // Show month field
            document.getElementById('modal-month-field').style.display = 'block';
            document.getElementById('modal_month').required = true;
        }
        
        // Handle period type change
        document.getElementById('modal_period_type').addEventListener('change', function() {
            const monthField = document.getElementById('modal-month-field');
            const monthSelect = document.getElementById('modal_month');
            
            if (this.value === 'monthly') {
                monthField.style.display = 'block';
                monthSelect.required = true;
            } else {
                monthField.style.display = 'none';
                monthSelect.required = false;
                monthSelect.value = '';
            }
        });
        
        // Close modal when clicking outside
        document.getElementById('budgetModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBudgetModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBudgetModal();
            }
        });
    </script>
</x-app-layout>
