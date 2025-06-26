<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function __invoke(Request $request): View
    {
        // TODO: Add dashboard statistics and data
        // - Total balance across all accounts
        // - Recent transactions
        // - Monthly income/expenses
        // - Budget progress

        return view('dashboard');
    }
}
