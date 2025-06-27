<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Http\Requests\CreateTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // Authentication is handled via route middleware in web.php

    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request): View
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with(['account', 'category', 'transferAccount'])
            ->orderBy('date', 'desc');
        
        // Apply filters
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }
        
        $transactions = $query->paginate(20)->withQueryString();
        
        // Get filter options
        $accounts = Account::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $categories = Category::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(): View
    {
        $accounts = Account::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $categories = Category::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('transactions.create', compact('accounts', 'categories'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(CreateTransactionRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();
            
            // Create the transaction
            $transaction = Transaction::create($validated);
            
            // Update account balance
            $account = Account::findOrFail($validated['account_id']);
            
            if ($validated['type'] === 'income') {
                $account->increment('balance', $validated['amount']);
            } elseif ($validated['type'] === 'expense') {
                $account->decrement('balance', $validated['amount']);
            } elseif ($validated['type'] === 'transfer' && isset($validated['transfer_account_id'])) {
                // Handle transfer between accounts
                $transferAccount = Account::findOrFail($validated['transfer_account_id']);
                
                $account->decrement('balance', $validated['amount']);
                $transferAccount->increment('balance', $validated['amount']);
                
                // Create corresponding transfer transaction
                $transferTransaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'account_id' => $validated['transfer_account_id'],
                    'category_id' => $validated['category_id'],
                    'description' => 'Transfer from ' . $account->name,
                    'amount' => $validated['amount'],
                    'type' => 'transfer',
                    'date' => $validated['date'],
                    'transfer_account_id' => $validated['account_id'],
                    'transfer_transaction_id' => $transaction->id,
                ]);
                
                $transaction->update(['transfer_transaction_id' => $transferTransaction->id]);
            }
            
            DB::commit();
            
            return redirect()->route('transactions.index')
                ->with('success', 'Transaction created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction): View
    {
        $this->authorize('view', $transaction);
        
        $transaction->load(['account', 'category', 'transferAccount', 'transferTransaction']);
        
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction): View
    {
        $this->authorize('update', $transaction);
        
        $accounts = Account::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $categories = Category::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
        
        return view('transactions.edit');
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(CreateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);
        
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            $oldTransaction = $transaction->replicate();
            
            // Reverse the old transaction's effect on account balance
            $oldAccount = Account::findOrFail($transaction->account_id);
            
            if ($transaction->type === 'income') {
                $oldAccount->decrement('balance', $transaction->amount);
            } elseif ($transaction->type === 'expense') {
                $oldAccount->increment('balance', $transaction->amount);
            } elseif ($transaction->type === 'transfer' && $transaction->transfer_account_id) {
                $oldTransferAccount = Account::findOrFail($transaction->transfer_account_id);
                $oldAccount->increment('balance', $transaction->amount);
                $oldTransferAccount->decrement('balance', $transaction->amount);
                
                // Delete the corresponding transfer transaction
                if ($transaction->transfer_transaction_id) {
                    Transaction::where('id', $transaction->transfer_transaction_id)->delete();
                }
            }
            
            // Update the transaction
            $transaction->update($validated);
            
            // Apply the new transaction's effect on account balance
            $newAccount = Account::findOrFail($validated['account_id']);
            
            if ($validated['type'] === 'income') {
                $newAccount->increment('balance', $validated['amount']);
            } elseif ($validated['type'] === 'expense') {
                $newAccount->decrement('balance', $validated['amount']);
            } elseif ($validated['type'] === 'transfer' && isset($validated['transfer_account_id'])) {
                $newTransferAccount = Account::findOrFail($validated['transfer_account_id']);
                $newAccount->decrement('balance', $validated['amount']);
                $newTransferAccount->increment('balance', $validated['amount']);
                
                // Create new corresponding transfer transaction
                $transferTransaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'account_id' => $validated['transfer_account_id'],
                    'category_id' => $validated['category_id'],
                    'description' => 'Transfer from ' . $newAccount->name,
                    'amount' => $validated['amount'],
                    'type' => 'transfer',
                    'date' => $validated['date'],
                    'transfer_account_id' => $validated['account_id'],
                    'transfer_transaction_id' => $transaction->id,
                ]);
                
                $transaction->update(['transfer_transaction_id' => $transferTransaction->id]);
            }
            
            DB::commit();
            
            return redirect()->route('transactions.index')
                ->with('success', 'Transaction updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update transaction: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);
        
        try {
            DB::beginTransaction();
            
            // Reverse the transaction's effect on account balance
            $account = Account::findOrFail($transaction->account_id);
            
            if ($transaction->type === 'income') {
                $account->decrement('balance', $transaction->amount);
            } elseif ($transaction->type === 'expense') {
                $account->increment('balance', $transaction->amount);
            } elseif ($transaction->type === 'transfer' && $transaction->transfer_account_id) {
                $transferAccount = Account::findOrFail($transaction->transfer_account_id);
                $account->increment('balance', $transaction->amount);
                $transferAccount->decrement('balance', $transaction->amount);
                
                // Delete the corresponding transfer transaction
                if ($transaction->transfer_transaction_id) {
                    Transaction::where('id', $transaction->transfer_transaction_id)->delete();
                }
            }
            
            // Delete the transaction
            $transaction->delete();
            
            DB::commit();
            
            return redirect()->route('transactions.index')
                ->with('success', 'Transaction deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }
}