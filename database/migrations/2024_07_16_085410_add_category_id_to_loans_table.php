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
            $table->unsignedBigInteger('member_category_id')->nullable();
            $table->unsignedBigInteger('member_designation_id')->nullable();
        });
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('member_category_id')->nullable();
            $table->unsignedBigInteger('member_designation_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'member_category_id',
                'member_designation_id'
            ]);
        });
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn([
                'member_category_id',
                'member_designation_id'
            ]);
        });
    }
};
