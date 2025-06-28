<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2); // Budget limit amount
            $table->enum('period_type', ['monthly', 'yearly'])->default('monthly');
            $table->integer('year');
            $table->integer('month')->nullable(); // null for yearly budgets
            $table->decimal('spent_amount', 15, 2)->default(0); // Cached spent amount
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'period_type', 'year', 'month']);
            $table->index(['category_id']);
            $table->index(['is_active']);
            
            // Unique constraint to prevent duplicate budgets
            $table->unique(['user_id', 'category_id', 'period_type', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};