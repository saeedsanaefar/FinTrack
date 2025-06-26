<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    /**
     * Display a listing of the accounts.
     */
    public function index(): View
    {
        $accounts = auth()->user()->accounts()->with('transactions')->get();
        
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new account.
     */
    public function create(): View
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created account in storage.
     */
    public function store(CreateAccountRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['currency'] = $data['currency'] ?? auth()->user()->currency ?? 'USD';
        
        auth()->user()->accounts()->create($data);
        
        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully!');
    }

    /**
     * Display the specified account.
     */
    public function show(Account $account): View
    {
        $this->authorize('view', $account);
        
        $account->load('transactions');
        
        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(Account $account): View
    {
        $this->authorize('update', $account);
        
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified account in storage.
     */
    public function update(CreateAccountRequest $request, Account $account): RedirectResponse
    {
        $this->authorize('update', $account);
        
        $data = $request->validated();
        $data['currency'] = $data['currency'] ?? $account->currency;
        
        $account->update($data);
        
        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully!');
    }

    /**
     * Remove the specified account from storage.
     */
    public function destroy(Account $account): RedirectResponse
    {
        $this->authorize('delete', $account);
        
        // Check if account has transactions
        if ($account->transactions()->count() > 0) {
            return redirect()->route('accounts.index')
                ->with('error', 'Cannot delete account with existing transactions.');
        }
        
        $account->delete();
        
        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully!');
    }
}