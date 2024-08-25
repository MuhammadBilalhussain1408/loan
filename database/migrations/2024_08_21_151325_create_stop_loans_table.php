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
        Schema::create('stop_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->text('account_holder')->nullable();
            $table->text('account_number')->nullable();
            $table->text('branch_code')->nullable();
            $table->text('monthly_installment')->nullable();
            $table->text('stop_order_date')->nullable();
            $table->text('member_account_holder')->nullable();
            $table->text('member_account_number')->nullable();
            $table->text('member_branch_code')->nullable();
            $table->text('reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stop_loans');
    }
};
