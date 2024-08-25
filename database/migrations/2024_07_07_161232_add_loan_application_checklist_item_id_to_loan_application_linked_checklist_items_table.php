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
        Schema::table('loan_application_linked_checklist_items', function (Blueprint $table) {
            $table->unsignedBigInteger('loan_application_checklist_item_id')->after('loan_application_id')->nullable();
            $table->unsignedBigInteger('completed_by_id')->after('loan_application_checklist_item_id')->nullable();
            $table->tinyInteger('completed')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_application_linked_checklist_items', function (Blueprint $table) {
            $table->dropColumn(['loan_application_checklist_item_id', 'completed_by_id']);
        });
    }
};
