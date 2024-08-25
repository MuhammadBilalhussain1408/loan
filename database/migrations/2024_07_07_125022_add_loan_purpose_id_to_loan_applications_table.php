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
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_purpose_id')->nullable();
            $table->unsignedBigInteger('loan_officer_id')->nullable();
            $table->decimal('interest_rate', 65, 6);
            $table->integer('decimals')->nullable();
            $table->integer('instalment_multiple_of')->default(1)->nullable();
            $table->integer('loan_term');
            $table->integer('repayment_frequency');
            $table->enum('repayment_frequency_type', array(
                'days',
                'weeks',
                'months',
                'years',
            ));
            $table->enum('interest_rate_type', array(
                'day', 'week', 'month', 'year', 'principal'
            ));
            $table->enum('interest_methodology', ['flat', 'declining_balance']);
            $table->tinyInteger('interest_recalculation')->default(0);
            $table->enum('amortization_method', ['equal_installments', 'equal_principal_payments'])->nullable();
            $table->enum('interest_calculation_period_type', ['daily', 'same'])->nullable();
            $table->enum('days_in_year', ['actual', '360', '365', '364'])->default('actual')->nullable();
            $table->enum('days_in_month', ['actual', '30', '31'])->default('actual')->nullable();
            $table->tinyInteger('enable_balloon_payments')->default(0);
            $table->tinyInteger('allow_schedule_adjustments')->default(0);
            $table->integer('grace_on_principal_paid')->default(0);
            $table->integer('grace_on_interest_paid')->default(0);
            $table->integer('grace_on_interest_charged')->default(0);
            $table->tinyInteger('allow_custom_grace_period')->default(0);
            $table->tinyInteger('allow_topup')->default(0);
            $table->tinyInteger('include_in_loan_cycle')->default(0);
            $table->tinyInteger('lock_guarantee_funds')->default(0);
            $table->tinyInteger('auto_allocate_overpayments')->default(0);
            $table->tinyInteger('allow_additional_charges')->default(0);
            $table->tinyInteger('auto_disburse')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn([
                'loan_purpose_id',
                'loan_officer_id',
                'interest_rate',
                'decimals',
                'instalment_multiple_of',
                'loan_term',
                'repayment_frequency',
                'repayment_frequency_type',
                'interest_rate_type',
                'interest_methodology',
                'interest_recalculation',
                'amortization_method',
                'interest_calculation_period_type',
                'days_in_year',
                'days_in_month',
                'enable_balloon_payments',
                'allow_schedule_adjustments',
                'grace_on_principal_paid',
                'grace_on_interest_paid',
                'grace_on_interest_charged',
                'allow_custom_grace_period',
                'allow_topup',
                'include_in_loan_cycle',
                'lock_guarantee_funds',
                'auto_allocate_overpayments',
                'allow_additional_charges',
                'auto_disburse',
            ]);
        });
    }
};
