<div class="transaction-card {{ $compact ? 'py-2' : 'py-4' }} px-4 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $interactive ? 'cursor-pointer' : '' }}"
     @if($interactive) onclick="window.location.href='/transactions/{{ $transaction->id }}'" @endif>

    <div class="flex items-center justify-between">
        <!-- Left side: Icon, Description, Details -->
        <div class="flex items-center space-x-3 flex-1 min-w-0">
            <!-- Category Icon -->
            @if($showCategory && $transaction->category)
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                     style="background-color: {{ $transaction->category->color ?? '#6B7280' }}20">
                    <span class="text-lg">{{ $transaction->category->icon ?? '💰' }}</span>
                </div>
            @endif

            <!-- Transaction Details -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-2">
                    <h4 class="font-medium text-gray-900 truncate">{{ $transaction->description }}</h4>
                    @if($transaction->is_recurring)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            🔄 Recurring
                        </span>
                    @endif
                </div>

                <div class="flex items-center space-x-2 text-sm text-gray-500 mt-1">
                    @if($showAccount && $transaction->account)
                        <span class="truncate">{{ $transaction->account->name }}</span>
                        <span>•</span>
                    @endif
                    @if($showCategory && $transaction->category)
                        <span class="truncate">{{ $transaction->category->name }}</span>
                        <span>•</span>
                    @endif
                    <time datetime="{{ $transaction->date }}">{{ $transaction->date->format('M j') }}</time>
                </div>
            </div>
        </div>

        <!-- Right side: Amount -->
        <div class="flex-shrink-0 text-right">
            <div class="transaction-amount {{ $getAmountColorClass() }} font-semibold">
                {{ $getFormattedAmount() }}
            </div>
            @if(!$compact && $transaction->notes)
                <div class="text-xs text-gray-400 mt-1">📝 Has notes</div>
            @endif
        </div>
    </div>

    <!-- Mobile-specific quick actions -->
    @if($interactive)
        <div class="show-mobile mt-3 flex space-x-2">
            <button class="flex-1 bg-blue-50 text-blue-600 py-2 px-3 rounded text-sm font-medium hover:bg-blue-100 transition-colors"
                    onclick="event.stopPropagation(); editTransaction({{ $transaction->id }})">
                ✏️ Edit
            </button>
            <button class="flex-1 bg-gray-50 text-gray-600 py-2 px-3 rounded text-sm font-medium hover:bg-gray-100 transition-colors"
                    onclick="event.stopPropagation(); duplicateTransaction({{ $transaction->id }})">
                📋 Duplicate
            </button>
        </div>
    @endif
</div>
