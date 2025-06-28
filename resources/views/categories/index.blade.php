<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categories') }}
            </h2>
            <button onclick="openCategoryModal()"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Category
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-6 mb-6">
                <form method="GET" action="{{ route('categories.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-300 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Search categories..."
                                   class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-300 mb-1">Type</label>
                            <select name="type" id="type"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Types</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="both" {{ request('type') === 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="" {{ request('status') === '' ? 'selected' : '' }}>All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-300 mb-1">Sort By</label>
                            <select name="sort" id="sort"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="sort_order" {{ request('sort') === 'sort_order' ? 'selected' : '' }}>Default Order</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                                <option value="transactions_count" {{ request('sort') === 'transactions_count' ? 'selected' : '' }}>Transaction Count</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('categories.index') }}"
                           class="px-4 py-2 bg-gray-800 border border-gray-500 rounded-md text-gray-100 hover:bg-gray-500 transition-colors">
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
                <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 p-12 text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-100 mb-2">No categories found</h3>
                    <p class="text-gray-400 mb-6">
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
                        <button onclick="openCategoryModal()"
                                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Create Your First Category
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Category Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-gray-900">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-100">Add New Category</h3>
                    <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="categoryForm" method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                    @csrf
                    
                    <!-- Name -->
                    <div>
                        <label for="modal_name" class="block text-sm font-medium text-gray-300 mb-1">
                            Category Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" id="modal_name" required
                               placeholder="Enter category name"
                               class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Type -->
                    <div>
                        <label for="modal_type" class="block text-sm font-medium text-gray-300 mb-1">
                            Category Type <span class="text-red-400">*</span>
                        </label>
                        <select name="type" id="modal_type" required
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Category Type</option>
                            <option value="income">💰 Income</option>
                            <option value="expense">💸 Expense</option>
                        </select>
                    </div>
                    
                    <!-- Icon Selector -->
                    <div>
                        <x-icon-selector name="icon" :selected="'tag'" />
                        <p class="text-xs text-gray-400 mt-1">Choose an icon to represent this category</p>
                    </div>
                    
                    <!-- Color Picker -->
                    <div>
                        <x-color-picker name="color" :selected="'#3B82F6'" />
                        <p class="text-xs text-gray-400 mt-1">Pick a color to easily identify this category</p>
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label for="modal_description" class="block text-sm font-medium text-gray-300 mb-1">
                            Description
                        </label>
                        <textarea name="description" id="modal_description" rows="2"
                                  placeholder="Add a description for this category..."
                                  class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                    </div>
                    
                    <!-- Sort Order -->
                    <div>
                        <label for="modal_sort_order" class="block text-sm font-medium text-gray-300 mb-1">
                            Sort Order
                        </label>
                        <input type="number" name="sort_order" id="modal_sort_order" value="0" min="0"
                               placeholder="0"
                               class="w-full px-3 py-2 bg-gray-800 border border-gray-600 text-gray-100 placeholder-gray-400 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-400 mt-1">Lower numbers appear first in lists</p>
                    </div>
                    
                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="modal_is_active" value="1" checked
                               class="h-4 w-4 text-blue-600 bg-gray-800 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="modal_is_active" class="ml-2 text-sm text-gray-300">
                            ✅ Active Category
                        </label>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeCategoryModal()"
                                class="px-4 py-2 bg-gray-600 text-gray-100 rounded-md hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                            Add Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCategoryModal() {
            document.getElementById('categoryModal').classList.remove('hidden');
            document.getElementById('modal_name').focus();
        }
        
        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
            document.getElementById('categoryForm').reset();
        }
        
        // Close modal when clicking outside
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCategoryModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCategoryModal();
            }
        });
    </script>
    
    @push('scripts')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @endpush
</x-app-layout>
