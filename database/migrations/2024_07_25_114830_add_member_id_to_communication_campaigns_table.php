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
        Schema::table('communication_campaigns', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable();
        });
        Schema::table('communication_campaign_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communication_campaigns', function (Blueprint $table) {
            $table->dropColumn(['member_id']);
        });
        Schema::table('communication_campaign_logs', function (Blueprint $table) {
            $table->dropColumn(['member_id']);
        });
    }
};
