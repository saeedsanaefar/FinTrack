<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecurringTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recurringTransactions = Auth::user()->recurringTransactions()
            ->with(['account', 'category', 'toAccount'])
            ->orderBy('next_due_date')
            ->paginate(15);

        return view('recurring-transactions.index', compact('recurringTransactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = Auth::user()->accounts;
        $categories = Auth::user()->categories;
        
        return view('recurring-transactions.create', compact('accounts', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'to_account_id' => 'nullable|exists:accounts,id|different:account_id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['next_due_date'] = $validated['start_date'];

        RecurringTransaction::create($validated);

        return redirect()->route('recurring-transactions.index')
            ->with('success', 'Recurring transaction created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RecurringTransaction $recurringTransaction)
    {
        $this->authorize('view', $recurringTransaction);
        
        $recurringTransaction->load(['account', 'category', 'toAccount']);
        
        return view('recurring-transactions.show', compact('recurringTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RecurringTransaction $recurringTransaction)
    {
        $this->authorize('update', $recurringTransaction);
        
        $accounts = Auth::user()->accounts;
        $categories = Auth::user()->categories;
        
        return view('recurring-transactions.edit', compact('recurringTransaction', 'accounts', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RecurringTransaction $recurringTransaction)
    {
        $this->authorize('update', $recurringTransaction);
        
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'to_account_id' => 'nullable|exists:accounts,id|different:account_id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $recurringTransaction->update($validated);

        return redirect()->route('recurring-transactions.index')
            ->with('success', 'Recurring transaction updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecurringTransaction $recurringTransaction)
    {
        $this->authorize('delete', $recurringTransaction);
        
        $recurringTransaction->delete();

        return redirect()->route('recurring-transactions.index')
            ->with('success', 'Recurring transaction deleted successfully!');
    }

    /**
     * Toggle the active status of a recurring transaction.
     */
    public function toggle(RecurringTransaction $recurringTransaction)
    {
        $this->authorize('update', $recurringTransaction);
        
        $recurringTransaction->update([
            'is_active' => !$recurringTransaction->is_active
        ]);

        $status = $recurringTransaction->is_active ? 'activated' : 'paused';
        
        return back()->with('success', "Recurring transaction {$status} successfully!");
    }

    /**
     * Manually generate a transaction from a recurring transaction.
     */
    public function generate(RecurringTransaction $recurringTransaction)
    {
        $this->authorize('update', $recurringTransaction);
        
        if (!$recurringTransaction->is_active) {
            return back()->with('error', 'Cannot generate transaction from inactive recurring transaction.');
        }

        try {
            $transaction = $recurringTransaction->generateTransaction();
            
            return back()->with('success', 'Transaction generated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate transaction: ' . $e->getMessage());
        }
    }
}
