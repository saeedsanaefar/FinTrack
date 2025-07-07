<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Composite indexes for common queries
            $table->index(['user_id', 'type', 'date'], 'idx_user_type_date');
            $table->index(['user_id', 'created_at'], 'idx_user_created_at');
            $table->index(['account_id', 'type'], 'idx_account_type');
            $table->index(['category_id', 'date'], 'idx_category_date');
        });

        Schema::table('budgets', function (Blueprint $table) {
            // Composite indexes for budget queries
            $table->index(['user_id', 'is_active', 'period_type'], 'idx_user_active_period');
            $table->index(['category_id', 'year', 'month'], 'idx_category_year_month');
        });

        Schema::table('accounts', function (Blueprint $table) {
            // Index for account queries
            $table->index(['user_id', 'type'], 'idx_user_type');
            $table->index(['user_id', 'is_active'], 'idx_user_active');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Index for category queries
            $table->index(['user_id', 'type', 'is_active'], 'idx_user_type_active');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_user_type_date');
            $table->dropIndex('idx_user_created_at');
            $table->dropIndex('idx_account_type');
            $table->dropIndex('idx_category_date');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex('idx_user_active_period');
            $table->dropIndex('idx_category_year_month');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex('idx_user_type');
            $table->dropIndex('idx_user_active');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_user_type_active');
        });
    }
};
