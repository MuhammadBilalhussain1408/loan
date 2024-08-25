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
            $table->unsignedBigInteger('disbursed_by_id')->nullable();
            $table->text('disbursement_notes')->nullable();
            $table->date('disbursed_on_date')->nullable();
            $table->unsignedBigInteger('payment_type_id')->nullable();
            $table->date('first_payment_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn([
                'disbursed_by_id',
                'disbursement_notes',
                'disbursed_on_date',
                'payment_type_id',
                'first_payment_date',
            ]);
        });
    }
};
