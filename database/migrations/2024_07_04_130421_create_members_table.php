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
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('loan_officer_id')->unsigned()->nullable();
            $table->string('reference')->nullable();
            $table->string('account_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->default('unspecified')->nullable();//can be 'male', 'female', 'other', 'unspecified'
            $table->enum('status', ['pending', 'active', 'inactive', 'deceased', 'unspecified', 'closed'])->default('pending');
            $table->enum('marital_status', ['married', 'single', 'divorced', 'widowed', 'unspecified', 'other'])->default('unspecified')->nullable();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->bigInteger('title_id')->unsigned()->nullable();
            $table->bigInteger('profession_id')->unsigned()->nullable();
            $table->string('graded_tax_number')->nullable();
            $table->string('identification_number')->nullable();
            $table->integer('number_of_spouses')->nullable();
            $table->integer('number_of_children')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('home_number')->nullable();
            $table->tinyInteger('english')->default(0);
            $table->tinyInteger('eswatini')->default(0);
            $table->string('other_language')->nullable();
            $table->string('email')->nullable();
            $table->string('external_id')->nullable();
            $table->decimal('monthly_or_annual_salary', 65)->nullable();
            $table->date('date_of_appointment')->nullable();
            $table->date('term_end_date')->nullable();
            $table->date('dob')->nullable();
            $table->text('postal_address')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('employer')->nullable();
            $table->string('photo')->nullable();
            $table->text('description')->nullable();
            $table->text('signature')->nullable();
            $table->date('created_date')->nullable();
            $table->date('joined_date')->nullable();
            $table->date('activation_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
