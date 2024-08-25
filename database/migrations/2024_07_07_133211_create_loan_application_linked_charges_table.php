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
        Schema::create('loan_application_linked_charges', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('loan_application_id')->unsigned();
            $table->bigInteger('loan_charge_id')->unsigned();
            $table->bigInteger('loan_charge_type_id')->unsigned()->nullable();
            $table->bigInteger('loan_charge_option_id')->unsigned()->nullable();
            $table->text('name')->nullable();
            $table->decimal('amount', 65, 6);
            $table->tinyInteger('is_penalty')->default(0);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_application_linked_charges');
    }
};
