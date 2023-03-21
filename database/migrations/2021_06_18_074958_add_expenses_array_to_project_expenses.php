<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpensesArrayToProjectExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn('container_currency');
            $table->dropColumn('container_rate');
            $table->dropColumn('container_amount');
            $table->dropColumn('container_price_1pc');
            $table->dropColumn('container_total_price_in_rub');
            $table->dropColumn('platform_currency');
            $table->dropColumn('platform_rate');
            $table->dropColumn('platform_amount');
            $table->dropColumn('platform_price_1pc');
            $table->dropColumn('platform_total_price_in_rub');
            $table->dropColumn('auto_currency');
            $table->dropColumn('auto_rate');
            $table->dropColumn('auto_amount');
            $table->dropColumn('auto_price_1pc');
            $table->dropColumn('auto_total_price_in_rub');
            $table->dropColumn('other_currency');
            $table->dropColumn('other_rate');
            $table->dropColumn('other_amount');
            $table->dropColumn('other_price_1pc');
            $table->dropColumn('other_total_price_in_rub');
            $table->longText('expenses_array')->nullable();
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
