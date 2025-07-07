<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupUserData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'backup:user-data {user : The user ID to backup}';

    /**
     * The console command description.
     */
    protected $description = 'Create a backup of user financial data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $this->info("Creating backup for user: {$user->name}");

        // Prepare backup data
        $backup = [
            'user' => $user->only(['name', 'email', 'currency', 'created_at']),
            'accounts' => $user->accounts()->get()->toArray(),
            'categories' => $user->categories()->get()->toArray(),
            'transactions' => $user->transactions()
                ->with(['account:id,name', 'category:id,name'])
                ->get()
                ->toArray(),
            'budgets' => $user->budgets()
                ->with(['category:id,name'])
                ->get()
                ->toArray(),
            'backup_metadata' => [
                'created_at' => now()->toISOString(),
                'version' => '1.0',
                'total_transactions' => $user->transactions()->count(),
                'total_accounts' => $user->accounts()->count(),
            ]
        ];

        // Create backup file
        $filename = "backups/user_{$user->id}_" . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::put(
            $filename,
            json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $this->info("Backup created successfully: {$filename}");
        $this->info("Backup size: " . Storage::size($filename) . " bytes");

        return 0;
    }
}
