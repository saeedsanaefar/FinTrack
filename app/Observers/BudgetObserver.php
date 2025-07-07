<?php

namespace App\Observers;

use App\Models\Budget;
use App\Services\DashboardService;
use Illuminate\Support\Facades\App;

class BudgetObserver
{
    public function created(Budget $budget)
    {
        $this->clearUserCache($budget->user_id);
    }

    public function updated(Budget $budget)
    {
        $this->clearUserCache($budget->user_id);
    }

    public function deleted(Budget $budget)
    {
        $this->clearUserCache($budget->user_id);
    }

    private function clearUserCache($userId)
    {
        $dashboardService = App::make(DashboardService::class);
        $dashboardService->clearUserCache($userId);
    }
}
