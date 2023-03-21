<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->bigInteger('client_id')->unsigned();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('freight_info')->nullable();
            $table->string('freight_amount')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('manager_id')->unsigned()->nullable();
            $table->bigInteger('logist_id')->unsigned()->nullable();
            $table->string('currency')->nullable();
            $table->string('price_1pc')->nullable();
            $table->string('cb_rate')->nullable();
            $table->bigInteger('price_in_currency')->nullable();
            $table->bigInteger('price')->default('0')->nullable();;
            $table->string('status');
            $table->string('additional_info')->nullable();
            $table->string('active')->default('1');
            $table->bigInteger('active_block_id')->unsigned()->nullable();
            $table->dateTime('finished_at')->nullable();
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
