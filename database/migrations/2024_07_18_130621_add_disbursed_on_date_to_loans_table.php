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
        Schema::table('loans', function (Blueprint $table) {
            $table->date('disbursed_on_date')->nullable();
            $table->unsignedBigInteger('loan_purpose_id')->nullable()->change();
            $table->unsignedBigInteger('fund_id')->nullable()->change();
            $table->unsignedBigInteger('loan_officer_id')->nullable()->change();
            $table->unsignedBigInteger('created_by_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'disbursed_on_date'
            ]);
        });
    }
};
