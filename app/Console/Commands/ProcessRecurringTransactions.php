<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use Illuminate\Console\Command;

class ProcessRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring:process {--dry-run : Show what would be processed without creating transactions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due recurring transactions and create new transaction records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing recurring transactions...');

        $dueRecurringTransactions = RecurringTransaction::active()
            ->due()
            ->with(['account', 'category', 'toAccount'])
            ->get();

        if ($dueRecurringTransactions->isEmpty()) {
            $this->info('No recurring transactions are due for processing.');
            return 0;
        }

        $this->info("Found {$dueRecurringTransactions->count()} due recurring transactions.");

        $processed = 0;
        $errors = 0;

        foreach ($dueRecurringTransactions as $recurring) {
            try {
                if ($this->option('dry-run')) {
                    $this->line("[DRY RUN] Would process: {$recurring->description} - {$recurring->amount} ({$recurring->frequency})");
                } else {
                    $transaction = $recurring->generateTransaction();
                    $this->line("✓ Created transaction: {$transaction->description} - {$transaction->amount}");
                }
                $processed++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to process recurring transaction ID {$recurring->id}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->info("\nProcessing complete!");
        $this->info("Processed: {$processed}");
        if ($errors > 0) {
            $this->warn("Errors: {$errors}");
        }

        return $errors > 0 ? 1 : 0;
    }
}
