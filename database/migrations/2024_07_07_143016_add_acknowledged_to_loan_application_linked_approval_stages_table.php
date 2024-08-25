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
        Schema::table('loan_application_linked_approval_stages', function (Blueprint $table) {
            $table->tinyInteger('acknowledged')->default(0);
            $table->timestamp('acknowledged_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_application_linked_approval_stages', function (Blueprint $table) {
            $table->dropColumn([
                'acknowledged',
                'acknowledged_at'
            ]);
        });
    }
};
