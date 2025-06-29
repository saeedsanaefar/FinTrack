<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Information</h3>
                    
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.update-profile') }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', auth()->user()->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', auth()->user()->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <!-- Currency -->
                            <div>
                                <x-input-label for="currency" :value="__('Currency')" />
                                <select id="currency" name="currency" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="USD" {{ old('currency', auth()->user()->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ old('currency', auth()->user()->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ old('currency', auth()->user()->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    <option value="JPY" {{ old('currency', auth()->user()->currency) == 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                                    <option value="CAD" {{ old('currency', auth()->user()->currency) == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                    <option value="AUD" {{ old('currency', auth()->user()->currency) == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                    <option value="CHF" {{ old('currency', auth()->user()->currency) == 'CHF' ? 'selected' : '' }}>CHF - Swiss Franc</option>
                                    <option value="CNY" {{ old('currency', auth()->user()->currency) == 'CNY' ? 'selected' : '' }}>CNY - Chinese Yuan</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>{{ __('Save Profile') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Update Password</h3>

                    <form method="POST" action="{{ route('settings.update-password') }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Current Password -->
                            <div>
                                <x-input-label for="current_password" :value="__('Current Password')" />
                                <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                                <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
                            </div>

                            <!-- New Password -->
                            <div>
                                <x-input-label for="password" :value="__('New Password')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="md:col-span-2">
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>{{ __('Update Password') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Export -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Data Export</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Export all your financial data including accounts, transactions, budgets, and recurring transactions in JSON format.
                    </p>
                    
                    <a href="{{ route('settings.export-data') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Data
                    </a>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-red-600 mb-4">Delete Account</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
                    </p>
                    
                    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">{{ __('Delete Account') }}</x-danger-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('settings.delete-account') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>