<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PrivacyController extends Controller
{
    /**
     * Show privacy settings page
     */
    public function index()
    {
        return view('privacy.index');
    }

    /**
     * Download user data (GDPR compliance)
     */
    public function downloadData()
    {
        $user = auth()->user();

        // Generate comprehensive data export
        $data = [
            'personal_info' => $user->only(['name', 'email', 'currency', 'created_at', 'updated_at']),
            'financial_data' => [
                'accounts' => $user->accounts()->get(),
                'transactions' => $user->transactions()
                    ->with(['account:id,name,type', 'category:id,name,type'])
                    ->get(),
                'categories' => $user->categories()->get(),
                'budgets' => $user->budgets()
                    ->with(['category:id,name'])
                    ->get(),
            ],
            'statistics' => [
                'total_transactions' => $user->transactions()->count(),
                'total_accounts' => $user->accounts()->count(),
                'total_categories' => $user->categories()->count(),
                'total_budgets' => $user->budgets()->count(),
                'account_created' => $user->created_at->toDateString(),
            ],
            'export_metadata' => [
                'export_date' => now()->toISOString(),
                'format_version' => '1.0',
                'exported_by' => 'FinTrack Data Export Tool'
            ]
        ];

        $filename = 'fintrack-data-export-' . now()->format('Y-m-d') . '.json';

        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Show account deletion confirmation page
     */
    public function showDeleteAccount()
    {
        return view('privacy.delete-account');
    }

    /**
     * Delete user account and all associated data
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            'confirmation' => 'required|in:DELETE MY ACCOUNT'
        ], [
            'confirmation.in' => 'You must type "DELETE MY ACCOUNT" exactly to confirm.'
        ]);

        $user = auth()->user();

        // Log the deletion for audit purposes
        \Log::info('User account deletion initiated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'deleted_at' => now()
        ]);

        // Delete all related data (cascade delete)
        $user->transactions()->delete();
        $user->accounts()->delete();
        $user->categories()->delete();
        $user->budgets()->delete();

        // Delete user account
        $user->delete();

        // Logout user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Your account and all associated data have been permanently deleted.');
    }
}
