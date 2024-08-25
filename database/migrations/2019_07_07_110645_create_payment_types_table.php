<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('chart_of_account_credit_id')->nullable();
            $table->unsignedBigInteger('chart_of_account_debit_id')->nullable();
            $table->string('name');
            $table->string('system_name')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_cash')->default(0);
            $table->tinyInteger('is_online')->default(0);
            $table->tinyInteger('is_system')->default(0);
            $table->tinyInteger('active')->default(1);
            $table->integer('position')->nullable();
            $table->text('options')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('report_color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_types');
    }
}
