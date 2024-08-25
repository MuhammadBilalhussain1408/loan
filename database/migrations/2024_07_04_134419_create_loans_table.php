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
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('loan_application_id')->unsigned()->nullable();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('member_id')->unsigned()->nullable();
            $table->unsignedBigInteger('loan_provisioning_id')->nullable();
            $table->unsignedBigInteger('payment_type_id')->nullable();
            $table->bigInteger('currency_id')->unsigned()->nullable();
            $table->bigInteger('loan_product_id')->unsigned();
            $table->bigInteger('loan_transaction_processing_strategy_id')->unsigned();
            $table->bigInteger('fund_id')->unsigned()->nullable();
            $table->bigInteger('loan_purpose_id')->unsigned();
            $table->bigInteger('loan_officer_id')->unsigned();
            $table->bigInteger('loan_disbursement_channel_id')->unsigned()->nullable();
            $table->date('approved_on_date')->nullable();
            $table->date('expected_first_payment_date')->nullable();
            $table->date('first_payment_date')->nullable();
            $table->date('expected_maturity_date')->nullable();
            $table->date('closed_on_date')->nullable();
            $table->bigInteger('closed_by_user_id')->unsigned()->nullable();
            $table->text('closed_notes')->nullable();
            $table->string('external_id')->unique()->nullable();
            $table->string('account_number')->unique()->nullable();
            $table->decimal('xrate', 65, 4)->nullable()->default(1.0);
            $table->decimal('principal', 65, 6);
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
            $table->tinyInteger('enable_balloon_payments')->default(0);
            $table->tinyInteger('allow_schedule_adjustments')->default(0);
            $table->integer('grace_on_principal_paid')->default(0);
            $table->integer('grace_on_interest_paid')->default(0);
            $table->integer('grace_on_interest_charged')->default(0);
            $table->tinyInteger('allow_custom_grace_period')->default(0);
            $table->tinyInteger('allow_topup')->default(0);
            $table->enum('interest_methodology', ['flat', 'declining_balance']);
            $table->tinyInteger('interest_recalculation')->default(0);
            $table->enum('amortization_method', ['equal_installments', 'equal_principal_payments'])->nullable();
            $table->enum('interest_calculation_period_type', ['daily', 'same'])->nullable();
            $table->enum('days_in_year', ['actual', '360', '365', '364'])->default('actual')->nullable();
            $table->enum('days_in_month', ['actual', '30', '31'])->default('actual')->nullable();
            $table->tinyInteger('include_in_loan_cycle')->default(0);
            $table->tinyInteger('lock_guarantee_funds')->default(0);
            $table->tinyInteger('auto_allocate_overpayments')->default(0);
            $table->tinyInteger('allow_additional_charges')->default(0);
            $table->tinyInteger('auto_disburse')->default(0);
            $table->string('status')->nullable()->default('active');
            $table->decimal('disbursement_charges', 65, 6)->nullable();
            $table->decimal('principal_disbursed_derived', 65, 6)->default(0.00);
            $table->decimal('principal_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('principal_written_off_derived', 65, 6)->default(0.00);
            $table->decimal('principal_outstanding_derived', 65, 6)->default(0.00);
            $table->decimal('interest_disbursed_derived', 65, 6)->default(0.00);
            $table->decimal('interest_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('interest_written_off_derived', 65, 6)->default(0.00);
            $table->decimal('interest_waived_derived', 65, 6)->default(0.00);
            $table->decimal('interest_outstanding_derived', 65, 6)->default(0.00);
            $table->decimal('fees_disbursed_derived', 65, 6)->default(0.00);
            $table->decimal('fees_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('fees_written_off_derived', 65, 6)->default(0.00);
            $table->decimal('fees_waived_derived', 65, 6)->default(0.00);
            $table->decimal('fees_outstanding_derived', 65, 6)->default(0.00);
            $table->decimal('penalties_disbursed_derived', 65, 6)->default(0.00);
            $table->decimal('penalties_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('penalties_written_off_derived', 65, 6)->default(0.00);
            $table->decimal('penalties_waived_derived', 65, 6)->default(0.00);
            $table->decimal('penalties_outstanding_derived', 65, 6)->default(0.00);
            $table->decimal('total_disbursed_derived', 65, 6)->default(0.00);
            $table->decimal('total_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('total_written_off_derived', 65, 6)->default(0.00);
            $table->decimal('total_waived_derived', 65, 6)->default(0.00);
            $table->decimal('total_outstanding_derived', 65, 6)->default(0.00);
            $table->string('classification')->nullable();
            $table->tinyInteger('deduct_interest_from_principal')->default(0);
            $table->timestamps();

            $table->index('member_id');
            $table->index('loan_officer_id');
            $table->index('loan_product_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
