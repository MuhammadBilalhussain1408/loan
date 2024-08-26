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
        Schema::create('member_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->text('member_category')->nullable();
            $table->text('Surname')->nullable();
            $table->text('name')->nullable();
            $table->text('gender')->nullable();
            $table->text('id_no')->nullable();
            $table->double('contri_15_per')->nullable();
            $table->double('contri_30_per')->nullable();
            $table->double('total_contribution')->nullable();
            $table->double('basic_salary')->nullable();
            $table->double('balance')->nullable();
            $table->enum('type',['credit','debit'])->default('credit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_contributions');
    }
};
