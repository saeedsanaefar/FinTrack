<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Edit Account: ') . $account->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('accounts.show', $account) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    View Account
                </a>
                <a href="{{ route('accounts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Accounts
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6 text-gray-100">
                    <form method="POST" action="{{ route('accounts.update', $account) }}">
                        @csrf
                        @method('PUT')

                        <!-- Account Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Account Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $account->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Account Type -->
                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Account Type')" />
                            <select id="type" name="type" class="block mt-1 w-full bg-gray-800 border-gray-600 text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Account Type</option>
                                <option value="checking" {{ old('type', $account->type) == 'checking' ? 'selected' : '' }}>Checking</option>
                                <option value="savings" {{ old('type', $account->type) == 'savings' ? 'selected' : '' }}>Savings</option>
                                <option value="credit_card" {{ old('type', $account->type) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="cash" {{ old('type', $account->type) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="investment" {{ old('type', $account->type) == 'investment' ? 'selected' : '' }}>Investment</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Current Balance -->
                        <div class="mb-4">
                            <x-input-label for="balance" :value="__('Current Balance')" />
                            <x-text-input id="balance" class="block mt-1 w-full" type="number" name="balance" :value="old('balance', $account->balance)" step="0.01" required />
                            <p class="text-sm text-gray-600 mt-1">Note: Changing the balance will not create a transaction record.</p>
                            <x-input-error :messages="$errors->get('balance')" class="mt-2" />
                        </div>

                        <!-- Currency -->
                        <div class="mb-4">
                            <x-input-label for="currency" :value="__('Currency')" />
                            <x-text-input id="currency" class="block mt-1 w-full" type="text" name="currency" :value="old('currency', $account->currency)" maxlength="3" placeholder="USD" />
                            <p class="text-sm text-gray-600 mt-1">3-letter currency code (e.g., USD, EUR, GBP)</p>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full bg-gray-800 border-gray-600 text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Add a description for this account...">{{ old('description', $account->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Account Status -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input id="is_active" name="is_active" type="checkbox" value="1"
                                       {{ old('is_active', $account->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Account is active
                                </label>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Inactive accounts will be hidden from most views but data will be preserved.</p>
                            <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('accounts.show', $account) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Account') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Delete Account Section -->
                    @if($account->transactions->count() === 0)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Delete Account</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>This account has no transactions and can be safely deleted. This action cannot be undone.</p>
                                        </div>
                                        <div class="mt-4">
                                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this account? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                    Delete Account
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Cannot Delete Account</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>This account has {{ $account->transactions->count() }} transaction(s) and cannot be deleted. You can deactivate it instead by unchecking "Account is active" above.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
