<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('My Accounts') }}
            </h2>
            <a href="{{ route('accounts.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Account
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($accounts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($accounts as $account)
                                <div class="bg-gray-300 border border-gray-200 rounded-lg shadow-md p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $account->name }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($account->type === 'checking') bg-blue-100 text-blue-800
                                            @elseif($account->type === 'savings') bg-green-100 text-green-800
                                            @elseif($account->type === 'credit_card') bg-red-100 text-red-800
                                            @elseif($account->type === 'cash') bg-yellow-100 text-yellow-800
                                            @else bg-purple-100 text-purple-800
                                            @endif">
                                            {{ $account->type_display }}
                                        </span>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-2xl font-bold text-gray-900">{{ $account->formatted_balance }}</p>
                                        <p class="text-sm text-gray-500">{{ $account->currency }}</p>
                                    </div>

                                    @if($account->description)
                                        <p class="text-sm text-gray-600 mb-4">{{ $account->description }}</p>
                                    @endif

                                    <div class="flex justify-between items-center">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('accounts.show', $account) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                View
                                            </a>
                                            <a href="{{ route('accounts.edit', $account) }}" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                                Edit
                                            </a>
                                        </div>

                                        <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto h-12 w-12 text-gray-400">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No accounts</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first account.</p>
                            <div class="mt-6">
                                <a href="{{ route('accounts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Add Account
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
