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
        Schema::create('loan_linked_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('loan_id')->unsigned();
            $table->bigInteger('loan_charge_id')->unsigned();
            $table->bigInteger('loan_charge_type_id')->unsigned()->nullable();
            $table->bigInteger('loan_charge_option_id')->unsigned()->nullable();
            $table->bigInteger('loan_transaction_id')->unsigned()->nullable();
            $table->text('name')->nullable();
            $table->decimal('amount', 65, 6);
            $table->decimal('calculated_amount', 65, 6)->nullable();
            $table->decimal('amount_paid_derived', 65, 6)->nullable();
            $table->decimal('amount_waived_derived', 65, 6)->nullable();
            $table->decimal('amount_written_off_derived', 65, 6)->nullable();
            $table->decimal('amount_outstanding_derived', 65, 6)->nullable();
            $table->tinyInteger('is_penalty')->default(0);
            $table->tinyInteger('waived')->default(0);
            $table->tinyInteger('is_paid')->default(0);
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->index('loan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_linked_charges');
    }
};
