<?php

namespace App\Observers;

use App\Models\Account;
use App\Services\DashboardService;
use Illuminate\Support\Facades\App;

class AccountObserver
{
    public function created(Account $account)
    {
        $this->clearUserCache($account->user_id);
    }

    public function updated(Account $account)
    {
        $this->clearUserCache($account->user_id);
    }

    public function deleted(Account $account)
    {
        $this->clearUserCache($account->user_id);
    }

    private function clearUserCache($userId)
    {
        $dashboardService = App::make(DashboardService::class);
        $dashboardService->clearUserCache($userId);
    }
}
