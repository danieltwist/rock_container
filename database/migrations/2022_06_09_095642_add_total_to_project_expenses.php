<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalToProjectExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->string('total_income')->nullable()->default('0');
            $table->string('total_outcome')->nullable()->default('0');
            $table->string('total_profit')->nullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            //
        });
    }
}
