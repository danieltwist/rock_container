<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableProjectExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->primary('project_id');
            $table->string('container_currency')->nullable();
            $table->string('container_rate')->nullable();
            $table->bigInteger('container_amount')->unsigned()->nullable();
            $table->string('container_price_1pc')->nullable();
            $table->bigInteger('container_total_price_in_rub')->unsigned()->nullable();
            $table->string('platform_currency')->nullable();
            $table->string('platform_rate')->nullable();
            $table->bigInteger('platform_amount')->unsigned()->nullable();
            $table->string('platform_price_1pc')->nullable();
            $table->bigInteger('platform_total_price_in_rub')->unsigned()->nullable();
            $table->string('auto_currency')->nullable();
            $table->string('auto_rate')->nullable();
            $table->bigInteger('auto_amount')->unsigned()->nullable();
            $table->string('auto_price_1pc')->nullable();
            $table->bigInteger('auto_total_price_in_rub')->unsigned()->nullable();
            $table->string('other_currency')->nullable();
            $table->string('other_rate')->nullable();
            $table->bigInteger('other_amount')->unsigned()->nullable();
            $table->string('other_price_1pc')->nullable();
            $table->bigInteger('other_total_price_in_rub')->unsigned()->nullable();
            $table->bigInteger('amount')->unsigned()->nullable();
            $table->string('currency')->nullable();
            $table->string('cb_rate')->nullable();
            $table->string('price_1pc')->nullable();
            $table->bigInteger('price_in_currency')->unsigned()->nullable();
            $table->bigInteger('price_in_rub')->unsigned()->nullable();
            $table->bigInteger('planned_costs')->unsigned()->nullable();
            $table->bigInteger('planned_profit')->unsigned()->nullable();
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
