<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Budget;
use App\Observers\TransactionObserver;
use App\Observers\AccountObserver;
use App\Observers\BudgetObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for cache invalidation
        Transaction::observe(TransactionObserver::class);
        Account::observe(AccountObserver::class);
        Budget::observe(BudgetObserver::class);
    }
}
