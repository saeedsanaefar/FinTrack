<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the dashboard.
     */
    public function __invoke(Request $request): View
    {
        $user = auth()->user();
        $dashboardData = $this->dashboardService->getDashboardData($user->id);

        return view('dashboard', $dashboardData);
    }
}
