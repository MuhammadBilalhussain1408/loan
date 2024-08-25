<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('file_type_id')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('category')->nullable();
            $table->string('disk')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('link')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('files');
    }
};
