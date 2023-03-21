<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColsToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('amount_in_currency_actual')->nullable();
            $table->string('currency')->nullable();
            $table->string('amount_in_currency')->nullable();
            $table->string('amount_income_date')->nullable();
            $table->string('amount_in_currency_income_date')->nullable();
            $table->string('rate_out_date')->nullable();
            $table->string('rate_income_date')->nullable();
            $table->string('rate_sale_date')->nullable();
            $table->string('amount_sale_date')->nullable();
            $table->string('edited')->nullable();
            $table->longText('invoice_array')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
