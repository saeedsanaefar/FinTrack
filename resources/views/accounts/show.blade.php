<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $account->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('accounts.edit', $account) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Account
                </a>
                <a href="{{ route('accounts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Accounts
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Account Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Account Type</h3>
                            <p class="mt-2 text-lg font-semibold text-gray-900">{{ $account->type_display }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Current Balance</h3>
                            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $account->formatted_balance }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Currency</h3>
                            <p class="mt-2 text-lg font-semibold text-gray-900">{{ $account->currency }}</p>
                        </div>
                    </div>
                    
                    @if($account->description)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Description</h3>
                            <p class="mt-2 text-gray-900">{{ $account->description }}</p>
                        </div>
                    @endif
                    
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Account Status</h3>
                        <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                        <a href="#" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View All Transactions
                        </a>
                    </div>
                    
                    @if($account->transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($account->transactions->take(10) as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->date ? $transaction->date->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->description ?? 'No description' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->category ?? 'Uncategorized' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium
                                                {{ ($transaction->amount ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ ($transaction->amount ?? 0) >= 0 ? '+' : '' }}{{ number_format($transaction->amount ?? 0, 2) }} {{ $account->currency }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto h-12 w-12 text-gray-400">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions</h3>
                            <p class="mt-1 text-sm text-gray-500">This account doesn't have any transactions yet.</p>
                            <div class="mt-6">
                                <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Add Transaction
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>