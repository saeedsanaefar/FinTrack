@props(['category', 'showActions' => true])

<div class="bg-gray-900 border border-gray-700 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden">
    <!-- Category Header -->
    <div class="p-4 border-b border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Category Icon -->
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" 
                         style="background-color: {{ $category->color }}20; border: 2px solid {{ $category->color }}">
                        @if($category->icon)
                            <i class="fas fa-{{ $category->icon }} text-lg" style="color: {{ $category->color }}"></i>
                        @else
                            <i class="fas fa-folder text-lg" style="color: {{ $category->color }}"></i>
                        @endif
                    </div>
                </div>
                
                <!-- Category Info -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-gray-100 truncate">{{ $category->name }}</h3>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($category->type === 'income') bg-green-100 text-green-800
                            @elseif($category->type === 'expense') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($category->type) }}
                        </span>
                        @if(!$category->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($showActions)
            <!-- Actions Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-md shadow-lg z-10 border border-gray-600">
                    <div class="py-1">
                        <a href="{{ route('categories.show', $category) }}" 
                           class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>
                        <a href="{{ route('categories.edit', $category) }}" 
                           class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to delete this category?')"
                                    class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-900/20">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Category Details -->
    <div class="p-4">
        @if($category->description)
            <p class="text-sm text-gray-600 mb-3">{{ $category->description }}</p>
        @endif
        
        <!-- Statistics -->
        <div class="grid grid-cols-2 gap-4 text-center">
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $category->transactions_count ?? 0 }}</p>
                <p class="text-xs text-gray-500">Transactions</p>
            </div>
            <div>
                @php
                    $totalAmount = 0;
                    if($category->relationLoaded('transactions')) {
                        $totalAmount = $category->transactions->sum(function($transaction) {
                            return $transaction->type === 'income' ? $transaction->amount : -$transaction->amount;
                        });
                    }
                @endphp
                <p class="text-2xl font-bold {{ $totalAmount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ auth()->user()->currency }} {{ number_format(abs($totalAmount), 2) }}
                </p>
                <p class="text-xs text-gray-500">Total Amount</p>
            </div>
        </div>
    </div>
    
    <!-- Color Bar -->
    <div class="h-1" style="background-color: {{ $category->color }}"></div>
</div>