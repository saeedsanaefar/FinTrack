<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categories') }}
            </h2>
            <a href="{{ route('categories.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Category
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="{{ route('categories.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Search categories..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <!-- Type Filter -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" id="type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="both" {{ request('type') === 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>
                        
                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="" {{ request('status') === '' ? 'selected' : '' }}>All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        
                        <!-- Sort -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select name="sort" id="sort" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="sort_order" {{ request('sort') === 'sort_order' ? 'selected' : '' }}>Default Order</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                                <option value="transactions_count" {{ request('sort') === 'transactions_count' ? 'selected' : '' }}>Transaction Count</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('categories.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Clear
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            @if($categories->count() > 0)
                <!-- Categories Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                        <x-category-card :category="$category" />
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $categories->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
                    <p class="text-gray-500 mb-6">
                        @if(request()->hasAny(['search', 'type', 'status']))
                            No categories match your current filters. Try adjusting your search criteria.
                        @else
                            You haven't created any categories yet. Categories help organize your transactions.
                        @endif
                    </p>
                    <div class="space-x-3">
                        @if(request()->hasAny(['search', 'type', 'status']))
                            <a href="{{ route('categories.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>Clear Filters
                            </a>
                        @endif
                        <a href="{{ route('categories.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create Your First Category
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>