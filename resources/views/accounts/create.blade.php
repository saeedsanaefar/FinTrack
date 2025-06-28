<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Create New Account') }}
                    </h2>
                    <p class="text-sm text-gray-600">Set up a new financial account to track your money</p>
                </div>
            </div>
            <x-secondary-button onclick="window.location.href='{{ route('accounts.index') }}'">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Accounts
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 overflow-hidden shadow-lg sm:rounded-xl border border-gray-700">
                <div class="p-8 text-gray-100">
                    <form method="POST" action="{{ route('accounts.store') }}" class="space-y-6">
                        @csrf

                        <!-- Account Name -->
                        <div class="space-y-2">
                            <x-input-label for="name" :value="__('Account Name')" :required="true" />
                            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="Enter account name" icon="user" />
                            <p class="text-xs text-gray-500">Choose a descriptive name for your account</p>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Account Type -->
                        <div class="space-y-2">
                            <x-input-label for="type" :value="__('Account Type')" :required="true" />
                            <select id="type" name="type" class="block w-full bg-gray-800 border-gray-600 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500" required>
                                <option value="">💳 Select Account Type</option>
                                <option value="checking" {{ old('type') == 'checking' ? 'selected' : '' }}>🏦 Checking Account</option>
                                <option value="savings" {{ old('type') == 'savings' ? 'selected' : '' }}>💰 Savings Account</option>
                                <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>💳 Credit Card</option>
                                <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>💵 Cash</option>
                                <option value="investment" {{ old('type') == 'investment' ? 'selected' : '' }}>📈 Investment Account</option>
                            </select>
                            <p class="text-xs text-gray-500">Select the type that best describes this account</p>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Initial Balance -->
                        <div class="space-y-2">
                            <x-input-label for="balance" :value="__('Initial Balance')" :required="true" />
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold text-lg">$</span>
                                <x-text-input id="balance" class="block w-full pl-8 text-lg font-semibold" type="number" name="balance" :value="old('balance', '0.00')" step="0.01" min="0" required placeholder="0.00" />
                            </div>
                            <p class="text-xs text-gray-500">Enter the current balance of this account</p>
                            <x-input-error :messages="$errors->get('balance')" class="mt-2" />
                        </div>

                        <!-- Currency -->
                        <div class="space-y-2">
                            <x-input-label for="currency" :value="__('Currency')" />
                            <x-text-input id="currency" class="block w-full uppercase" type="text" name="currency" :value="old('currency', auth()->user()->currency ?? 'USD')" maxlength="3" placeholder="USD" />
                            <p class="text-xs text-gray-500">3-letter currency code (e.g., USD, EUR, GBP)</p>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="block w-full bg-gray-800 border-gray-600 text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all duration-200 hover:border-gray-500 resize-none" placeholder="Add a description for this account...">{{ old('description') }}</textarea>
                            <p class="text-xs text-gray-500">Optional notes about this account</p>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <x-secondary-button onclick="window.location.href='{{ route('accounts.index') }}'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Cancel
                            </x-secondary-button>
                            <x-primary-button class="ml-4">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('Create Account') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
