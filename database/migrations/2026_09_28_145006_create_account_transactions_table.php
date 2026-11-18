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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->enum('type', ['income', 'expense', 'purchase', 'sales_collection']); // Main transaction types
            $table->string('category')->nullable(); // e.g., Rent, Utility, Salary, Marketing, Packaging
            $table->string('title');
            $table->decimal('amount', 12, 2); // Paid / Received amount
            $table->decimal('due_amount', 12, 2)->default(0); // Any remaining due
            $table->enum('party_type', ['customer', 'supplier', 'other'])->default('other');
            $table->string('party_name')->nullable(); // Customer or Supplier Name
            $table->string('payment_method')->default('cash'); // cash, bank, bkash, nagad
            $table->string('reference_no')->nullable(); // Voucher / Cheque / Order #
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Entry creator
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
