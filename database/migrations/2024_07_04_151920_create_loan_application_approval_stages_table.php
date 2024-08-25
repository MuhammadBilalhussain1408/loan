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
        Schema::create('loan_application_approval_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_to_id')->nullable();
            $table->string('name');
            $table->tinyInteger('is_last')->default(0);
            $table->integer('order')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_application_approval_stages');
    }
};
