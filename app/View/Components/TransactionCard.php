<?php

namespace App\View\Components;

use App\Models\Transaction;
use Illuminate\View\Component;
use Illuminate\View\View;

class TransactionCard extends Component
{
    public function __construct(
        public Transaction $transaction,
        public bool $showAccount = true,
        public bool $compact = false,
        public bool $showCategory = true,
        public bool $interactive = true
    ) {}

    public function render(): View
    {
        return view('components.transaction-card');
    }

    public function getAmountColorClass(): string
    {
        return $this->transaction->type === 'income'
            ? 'text-green-600'
            : 'text-red-600';
    }

    public function getFormattedAmount(): string
    {
        $prefix = $this->transaction->type === 'income' ? '+' : '-';
        return $prefix . '$' . number_format($this->transaction->amount, 2);
    }
}
