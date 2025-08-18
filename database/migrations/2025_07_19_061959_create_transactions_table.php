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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid()->index();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId( 'account_id')->constrained('accounts');
            $table->enum('type',['income','expense']);
            $table->decimal('amount', 18, 2);
            $table->string('description');
            $table->string('notes')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->string('reference_number')->nullable();
            $table->integer('active');
            $table->timestamps();
            $table->softDeletes();

            // For better perforamnce
            $table->index(['user_id','type']);
            $table->index(['account_id', 'transaction_date']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
