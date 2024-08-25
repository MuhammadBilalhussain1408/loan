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
        Schema::create('loan_application_linked_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_application_checklist_id');
            $table->unsignedBigInteger('loan_application_id');
            $table->tinyInteger('completed')->default(1);
            $table->string('status')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_application_linked_checklist_items');
    }
};
