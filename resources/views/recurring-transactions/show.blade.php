@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-xl font-semibold text-gray-900">Recurring Transaction Details</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $recurringTransaction->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $recurringTransaction->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('recurring-transactions.edit', $recurringTransaction) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Edit
                        </a>
                        <a href="{{ route('recurring-transactions.index') }}" class="text-gray-600 hover:text-gray-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Basic Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $recurringTransaction->description }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($recurringTransaction->type === 'income') bg-green-100 text-green-800
                                @elseif($recurringTransaction->type === 'expense') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($recurringTransaction->type) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount</label>
                            <p class="mt-1 text-lg font-semibold
                                @if($recurringTransaction->type === 'income') text-green-600
                                @elseif($recurringTransaction->type === 'expense') text-red-600
                                @else text-blue-600 @endif">
                                ${{ number_format($recurringTransaction->amount, 2) }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">From Account</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $recurringTransaction->account->name }} ({{ $recurringTransaction->account->type }})</p>
                        </div>

                        @if($recurringTransaction->to_account_id)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">To Account</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $recurringTransaction->toAccount->name }} ({{ $recurringTransaction->toAccount->type }})</p>
                            </div>
                        @endif

                        @if($recurringTransaction->category)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $recurringTransaction->category->name }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Schedule Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Schedule Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Frequency</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($recurringTransaction->frequency) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $recurringTransaction->start_date->format('M d, Y') }}</p>
                        </div>

                        @if($recurringTransaction->end_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $recurringTransaction->end_date->format('M d, Y') }}</p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Next Due Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $recurringTransaction->next_due_date->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $recurringTransaction->is_active ? 'Active' : 'Inactive' }}</p>
                        </div>
                    </div>
                </div>

                @if($recurringTransaction->reference)
                    <!-- Reference -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
                        <p class="text-gray-900">{{ $recurringTransaction->reference }}</p>
                    </div>
                @endif

                @if($recurringTransaction->notes)
                    <!-- Notes -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $recurringTransaction->notes }}</p>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <form method="POST" action="{{ route('recurring-transactions.toggle', $recurringTransaction) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ $recurringTransaction->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            @if($recurringTransaction->is_active)
                                <form method="POST" action="{{ route('recurring-transactions.generate', $recurringTransaction) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Generate Transaction Now
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('recurring-transactions.edit', $recurringTransaction) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('recurring-transactions.destroy', $recurringTransaction) }}" class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this recurring transaction? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-500">
                        <div>
                            <span class="font-medium">Created:</span> {{ $recurringTransaction->created_at->format('M d, Y g:i A') }}
                        </div>
                        <div>
                            <span class="font-medium">Last Updated:</span> {{ $recurringTransaction->updated_at->format('M d, Y g:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection