<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryOptimizationService
{
    public static function enableQueryLogging()
    {
        if (config('app.debug')) {
            DB::listen(function ($query) {
                if ($query->time > 100) { // Log slow queries (>100ms)
                    Log::warning('Slow Query Detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms'
                    ]);
                }
            });
        }
    }

    public static function optimizeTransactionQueries()
    {
        // Suggest indexes based on common query patterns
        return [
            'user_id + date' => 'Most common dashboard queries',
            'user_id + type + date' => 'Income/expense filtering',
            'account_id + date' => 'Account transaction history',
            'category_id + date' => 'Category analysis'
        ];
    }
}
