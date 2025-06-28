<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Transaction Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('transactions.edit', $transaction) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Transaction
                </a>
                <a href="{{ route('transactions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Transactions
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Transaction Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Transaction Information</h3>

                            <!-- Type -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' :
                                       ($transaction->type === 'expense' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </div>

                            <!-- Amount -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                <div class="text-2xl font-bold
                                    {{ $transaction->type === 'income' ? 'text-green-600' :
                                       ($transaction->type === 'expense' ? 'text-red-600' : 'text-blue-600') }}">
                                    {{ $transaction->type === 'income' ? '+' : ($transaction->type === 'expense' ? '-' : '') }}
                                    {{ number_format($transaction->amount, 2) }} {{ $transaction->account->currency }}
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <p class="text-gray-900">{{ $transaction->description }}</p>
                            </div>

                            <!-- Date -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Date</label>
                                <p class="text-gray-900">{{ $transaction->date ? $transaction->date->format('F j, Y') : 'N/A' }}</p>
                            </div>

                            @if($transaction->reference)
                                <!-- Reference -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                                    <p class="text-gray-900">{{ $transaction->reference }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Right Column -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account & Category</h3>

                            <!-- Account -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $transaction->type === 'transfer' ? 'From Account' : 'Account' }}</label>
                                <div class="flex items-center">
                                    <div class="bg-gray-100 p-3 rounded-lg flex-1">
                                        <p class="font-medium text-gray-900">{{ $transaction->account->name }}</p>
                                        <p class="text-sm text-gray-600">{{ ucfirst($transaction->account->type) }}</p>
                                        <p class="text-sm text-gray-600">Balance: {{ number_format($transaction->account->balance, 2) }} {{ $transaction->account->currency }}</p>
                                    </div>
                                    <a href="{{ route('accounts.show', $transaction->account) }}" class="ml-3 text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        View Account
                                    </a>
                                </div>
                            </div>

                            @if($transaction->type === 'transfer' && $transaction->toAccount)
                                <!-- To Account -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Account</label>
                                    <div class="flex items-center">
                                        <div class="bg-gray-100 p-3 rounded-lg flex-1">
                                            <p class="font-medium text-gray-900">{{ $transaction->toAccount->name }}</p>
                                            <p class="text-sm text-gray-600">{{ ucfirst($transaction->toAccount->type) }}</p>
                                            <p class="text-sm text-gray-600">Balance: {{ number_format($transaction->toAccount->balance, 2) }} {{ $transaction->toAccount->currency }}</p>
                                        </div>
                                        <a href="{{ route('accounts.show', $transaction->toAccount) }}" class="ml-3 text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View Account
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($transaction->category && $transaction->type !== 'transfer')
                                <!-- Category -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                              style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}">
                                            @if($transaction->category->icon)
                                                <span class="mr-1">{{ $transaction->category->icon }}</span>
                                            @endif
                                            {{ $transaction->category->name }}
                                        </span>
                                        <a href="{{ route('categories.show', $transaction->category) }}" class="ml-3 text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View Category
                                        </a>
                                    </div>
                                    @if($transaction->category->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $transaction->category->description }}</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Recurring Information -->
                            @if($transaction->is_recurring)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Recurring</label>
                                    <div class="bg-blue-50 p-3 rounded-lg">
                                        <p class="text-sm text-blue-800">
                                            <span class="font-medium">Frequency:</span> {{ ucfirst($transaction->recurring_frequency) }}
                                        </p>
                                        @if($transaction->recurring_end_date)
                                            <p class="text-sm text-blue-800">
                                                <span class="font-medium">Ends:</span> {{ $transaction->recurring_end_date->format('F j, Y') }}
                                            </p>
                                        @else
                                            <p class="text-sm text-blue-800">
                                                <span class="font-medium">Duration:</span> Indefinite
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($transaction->notes)
                        <!-- Notes -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $transaction->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-sm font-medium text-gray-700">Attachments</label>
                            <button onclick="document.getElementById('file-upload-form').style.display = document.getElementById('file-upload-form').style.display === 'none' ? 'block' : 'none'" 
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                + Add File
                            </button>
                        </div>

                        <!-- File Upload Form -->
                        <div id="file-upload-form" style="display: none;" class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <form action="{{ route('attachments.store', $transaction) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="flex items-center space-x-4">
                                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.gif" 
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                                           required>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        Upload
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Supported formats: PDF, JPG, PNG, GIF (Max: 10MB)</p>
                            </form>
                        </div>

                        <!-- Attachments List -->
                        @if($transaction->attachments->count() > 0)
                            <div class="space-y-2">
                                @foreach($transaction->attachments as $attachment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                @if(str_contains($attachment->file_type, 'image'))
                                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $attachment->original_filename }}</p>
                                                <p class="text-xs text-gray-500">{{ $attachment->file_size_formatted }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('attachments.download', $attachment) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Download
                                            </a>
                                            <form method="POST" action="{{ route('attachments.destroy', $attachment) }}" class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this file?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No attachments uploaded.</p>
                        @endif
                    </div>

                    <!-- Metadata -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">Created:</span> {{ $transaction->created_at ? $transaction->created_at->format('F j, Y g:i A') : 'N/A' }}
                            </div>
                            <div>
                                <span class="font-medium">Last Updated:</span> {{ $transaction->updated_at ? $transaction->updated_at->format('F j, Y g:i A') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('transactions.edit', $transaction) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Transaction
                        </a>

                        @if($transaction->type !== 'transfer')
                            <a href="{{ route('transactions.create') }}?duplicate={{ $transaction->id }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Duplicate Transaction
                            </a>
                        @endif

                        <a href="{{ route('accounts.show', $transaction->account) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            View Account
                        </a>

                        @if($transaction->category)
                            <a href="{{ route('categories.show', $transaction->category) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                View Category
                            </a>
                        @endif
                    </div>

                    <!-- Delete Section -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Delete Transaction</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>This action cannot be undone. The account balance will be automatically updated.</p>
                                    </div>
                                    <div class="mt-4">
                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this transaction? This action cannot be undone and will update your account balance.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                Delete Transaction
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
