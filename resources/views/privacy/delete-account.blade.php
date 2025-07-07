<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Delete Account') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-red-600 dark:text-red-400 mb-4">
                            ⚠️ Warning: This action cannot be undone
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Deleting your account will permanently remove:
                        </p>
                        <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 mb-6 space-y-1">
                            <li>All your financial accounts and balances</li>
                            <li>All transaction history</li>
                            <li>All custom categories</li>
                            <li>All budget data</li>
                            <li>Your user profile and settings</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('privacy.delete-account') }}" class="space-y-6">
                        @csrf
                        @method('DELETE')

                        <!-- Password Confirmation -->
                        <div>
                            <x-input-label for="password" :value="__('Confirm Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirmation Text -->
                        <div>
                            <x-input-label for="confirmation" :value="__('Type \"DELETE MY ACCOUNT\" to confirm')" />
                            <x-text-input id="confirmation" name="confirmation" type="text" class="mt-1 block w-full" placeholder="DELETE MY ACCOUNT" required />
                            <x-input-error :messages="$errors->get('confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('privacy.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>

                            <x-danger-button>
                                {{ __('Delete Account Permanently') }}
                            </x-danger-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
