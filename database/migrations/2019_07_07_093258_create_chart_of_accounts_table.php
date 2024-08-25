<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->integer('parent_id')->nullable();
            $table->text('name');
            $table->integer('gl_code')->nullable();
            $table->string('external_id')->nullable();
            $table->string('account_type')->default('asset');
            $table->tinyInteger('enable_reconciliation')->default(0);
            $table->tinyInteger('allow_manual')->default(0);
            $table->tinyInteger('active')->default(1);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('chart_of_accounts');
    }
}
