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
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:60,1')->only(['store', 'update', 'destroy']);
        $this->middleware('throttle:100,1')->only(['index', 'show']);
    }

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

        // Date filters - support both old and new parameter names
        if ($request->filled('date_from') || $request->filled('start_date')) {
            $startDate = $request->date_from ?: $request->start_date;
            $query->whereDate('date', '>=', $startDate);
        }

        if ($request->filled('date_to') || $request->filled('end_date')) {
            $endDate = $request->date_to ?: $request->end_date;
            $query->whereDate('date', '<=', $endDate);
        }

        // Amount filters
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
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
        Log::info('Creating transaction with data:', $request->validated());

        try {
            DB::beginTransaction();

            $validated = $request->validated();
            Log::info('Validated data:', $validated);
            $validated['user_id'] = auth()->id();

            $account = Account::findOrFail($validated['account_id']);
            Log::info('Found account:', ['id' => $account->id, 'balance' => $account->balance]);

            $category = Category::findOrFail($validated['category_id']);
            Log::info('Found category:', ['id' => $category->id, 'name' => $category->name]);

            // Create the transaction
            $transaction = Transaction::create($validated);

            Log::info('Transaction created', ['transaction' => $transaction->toArray()]);

            // For transfer transactions, update both account balances directly
            if ($validated['type'] === 'transfer' && isset($validated['transfer_account_id'])) {
                $transferAccount = Account::findOrFail($validated['transfer_account_id']);
                Log::info('Found transfer account:', ['id' => $transferAccount->id, 'balance' => $transferAccount->balance]);

                // Update source account (subtract amount)
                $account->decrement('balance', $validated['amount']);

                // Update destination account (add amount)
                $transferAccount->increment('balance', $validated['amount']);

                Log::info('Updated balances - From account:', ['id' => $account->id, 'new_balance' => $account->fresh()->balance]);
                Log::info('Updated balances - To account:', ['id' => $transferAccount->id, 'new_balance' => $transferAccount->fresh()->balance]);
            } else {
                // For non-transfer transactions, update the account balance
                if ($validated['type'] === 'income') {
                    $account->increment('balance', $validated['amount']);
                } elseif ($validated['type'] === 'expense') {
                    $account->decrement('balance', $validated['amount']);
                }
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
        $transaction->load(['account', 'category', 'transferAccount', 'transferTransaction']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction): View
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

        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(CreateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
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
