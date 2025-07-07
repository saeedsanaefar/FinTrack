<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function show()
    {
        return view('settings.index');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'currency' => 'required|string|size:3',
        ]);

        auth()->user()->update($request->only(['name', 'email', 'currency']));

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function exportData()
    {
        $user = auth()->user();

        $data = [
            'user' => $user->only(['name', 'email', 'currency']),
            'accounts' => $user->accounts,
            'categories' => $user->categories,
            'transactions' => $user->transactions()->with(['account', 'category'])->get(),
            'budgets' => $user->budgets()->with('category')->get(),
            'recurring_transactions' => $user->recurringTransactions()->with(['account', 'category'])->get(),
        ];

        $filename = 'fintrack-export-' . now()->format('Y-m-d') . '.json';

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();

        // Log out the user
        auth()->logout();

        // Delete the user account (cascade will handle related data)
        $user->delete();

        return redirect('/')->with('success', 'Your account has been deleted successfully.');
    }
}
