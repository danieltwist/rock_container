<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableActionRecordings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_recordings', function (Blueprint $table) {
            $table->id();
            $table->string('model')->nullable();
            $table->bigInteger('model_id')->unsigned()->nullable();
            $table->text('object')->nullable();
            $table->text('text')->nullable();
            $table->longText('before_edit')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
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
        //
    }
}
