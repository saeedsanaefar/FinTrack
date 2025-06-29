<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Financial Management Routes
    Route::resource('accounts', AccountController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('budgets', BudgetController::class);

    // Budget specific routes
    Route::get('/budgets-alerts', [BudgetController::class, 'alerts'])->name('budgets.alerts');
    Route::post('/budgets-update-spent', [BudgetController::class, 'updateSpentAmounts'])->name('budgets.update-spent');

    // Attachment routes
    Route::post('/transactions/{transaction}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Recurring Transaction routes
    Route::resource('recurring-transactions', RecurringTransactionController::class);
    Route::patch('/recurring-transactions/{recurringTransaction}/toggle', [RecurringTransactionController::class, 'toggle'])->name('recurring-transactions.toggle');
    Route::post('/recurring-transactions/{recurringTransaction}/generate', [RecurringTransactionController::class, 'generate'])->name('recurring-transactions.generate');

    // Report routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/monthly-data', [ReportController::class, 'monthlyData'])->name('reports.monthly-data');
    Route::get('/reports/category-data', [ReportController::class, 'categoryData'])->name('reports.category-data');
    Route::get('/reports/account-data', [ReportController::class, 'accountData'])->name('reports.account-data');
    Route::get('/reports/total-income', [ReportController::class, 'totalIncome'])->name('reports.total-income');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('/reports/chart-data', [ReportController::class, 'getChartData'])->name('reports.chart-data');

    // Search and Filtering
    Route::get('/search/transactions', [SearchController::class, 'transactions'])->name('search.transactions');
    Route::get('/api/search/transactions', [SearchController::class, 'api'])->name('api.search.transactions');

    // Settings
    Route::get('/settings', [SettingsController::class, 'show'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.update-profile');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.update-password');
    Route::get('/settings/export-data', [SettingsController::class, 'exportData'])->name('settings.export-data');
    Route::delete('/settings/delete-account', [SettingsController::class, 'deleteAccount'])->name('settings.delete-account');
});

require __DIR__.'/auth.php';
