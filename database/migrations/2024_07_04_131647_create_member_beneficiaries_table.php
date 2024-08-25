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
        Schema::create('member_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by_id')->unsigned()->nullable();
            $table->unsignedBigInteger('member_relationship_id')->unsigned()->nullable();
            $table->unsignedBigInteger('member_id');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->default('unspecified')->nullable();//can be 'male', 'female', 'other', 'unspecified'
            $table->enum('status', ['pending', 'active', 'inactive', 'deceased', 'unspecified', 'closed'])->default('active');
            $table->enum('marital_status', ['married', 'single', 'divorced', 'widowed', 'unspecified', 'other'])->default('unspecified')->nullable();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->bigInteger('title_id')->unsigned()->nullable();
            $table->decimal('shares')->nullable();
            $table->string('identification_number')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('home_number')->nullable();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->text('postal_address')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('photo')->nullable();
            $table->text('description')->nullable();
            $table->text('signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_beneficiaries');
    }
};
