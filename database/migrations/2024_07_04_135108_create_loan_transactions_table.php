<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('loan_id')->unsigned();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned()->nullable();
            $table->bigInteger('payment_type_id')->unsigned()->nullable();
            $table->bigInteger('payment_detail_id')->unsigned()->nullable();
            $table->text('name')->nullable();
            $table->decimal('amount', 65, 6);
            $table->decimal('xrate', 65, 6)->nullable()->default(1.00);
            $table->decimal('credit', 65, 6)->nullable();
            $table->decimal('debit', 65, 6)->nullable();
            $table->decimal('principal_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('interest_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('fees_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('penalties_repaid_derived', 65, 6)->default(0.00);
            $table->bigInteger('loan_transaction_type_id')->unsigned();
            $table->tinyInteger('reversed')->default(0);
            $table->tinyInteger('reversible')->default(0);
            $table->date('submitted_on')->nullable();
            $table->date('due_date')->nullable();
            $table->date('created_on')->nullable();
            $table->enum('status', ['pending', 'approved', 'declined'])->nullable();
            $table->string('reference')->nullable();
            $table->string('gateway_id')->nullable();
            $table->text('description')->nullable();
            $table->text('payment_gateway_data')->nullable();
            $table->tinyInteger('online_transaction')->default(0);
            $table->timestamps();
            $table->index('loan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_transactions');
    }
};
