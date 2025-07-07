<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Services\DashboardService;
use Illuminate\Support\Facades\App;

class TransactionObserver
{
    public function created(Transaction $transaction)
    {
        $this->clearUserCache($transaction->user_id);
    }

    public function updated(Transaction $transaction)
    {
        $this->clearUserCache($transaction->user_id);
    }

    public function deleted(Transaction $transaction)
    {
        $this->clearUserCache($transaction->user_id);
    }

    private function clearUserCache($userId)
    {
        $dashboardService = App::make(DashboardService::class);
        $dashboardService->clearUserCache($userId);
    }
}
