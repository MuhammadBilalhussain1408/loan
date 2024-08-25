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
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->bigInteger('loan_application_checklist_id')->unsigned()->nullable();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('current_loan_application_linked_approval_stage_id')->unsigned()->nullable();
            $table->bigInteger('next_loan_application_linked_approval_stage_id')->unsigned()->nullable();
            $table->bigInteger('member_id')->unsigned();
            $table->bigInteger('currency_id')->unsigned()->nullable();;
            $table->bigInteger('loan_product_id')->unsigned();
            $table->decimal('applied_amount', 65)->default(0.00);
            $table->decimal('approved_amount', 65)->nullable();
            $table->decimal('xrate', 65)->default(1.00);
            $table->string('status')->nullable()->default('pending');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};
