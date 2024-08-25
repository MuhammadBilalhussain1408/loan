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
        Schema::create('loan_application_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('loan_application_id')->unsigned();
            $table->bigInteger('created_by_id')->unsigned();
            $table->text('action')->nullable();
            $table->text('user')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_application_histories');
    }
};
