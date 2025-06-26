<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Account') }}
            </h2>
            <a href="{{ route('accounts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Accounts
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('accounts.store') }}">
                        @csrf

                        <!-- Account Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Account Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Account Type -->
                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Account Type')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Account Type</option>
                                <option value="checking" {{ old('type') == 'checking' ? 'selected' : '' }}>Checking</option>
                                <option value="savings" {{ old('type') == 'savings' ? 'selected' : '' }}>Savings</option>
                                <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="investment" {{ old('type') == 'investment' ? 'selected' : '' }}>Investment</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Initial Balance -->
                        <div class="mb-4">
                            <x-input-label for="balance" :value="__('Initial Balance')" />
                            <x-text-input id="balance" class="block mt-1 w-full" type="number" name="balance" :value="old('balance', '0.00')" step="0.01" min="0" required />
                            <x-input-error :messages="$errors->get('balance')" class="mt-2" />
                        </div>

                        <!-- Currency -->
                        <div class="mb-4">
                            <x-input-label for="currency" :value="__('Currency')" />
                            <x-text-input id="currency" class="block mt-1 w-full" type="text" name="currency" :value="old('currency', auth()->user()->currency ?? 'USD')" maxlength="3" placeholder="USD" />
                            <p class="text-sm text-gray-600 mt-1">3-letter currency code (e.g., USD, EUR, GBP)</p>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Add a description for this account...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('accounts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Create Account') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>