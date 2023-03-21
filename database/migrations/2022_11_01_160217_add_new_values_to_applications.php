<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewValuesToApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('name', 255);
            $table->string('status', 255)->nullable();
            $table->text('contract_info')->nullable();
            $table->string('counterparty_type', 255)->nullable();
            $table->string('client_name', 255)->nullable();
            $table->string('supplier_name', 255)->nullable();
            $table->bigInteger('containers_amount')->unsigned();
            $table->text('containers')->nullable();
            $table->text('containers_removed')->nullable();
            $table->string('removed_by', 255)->nullable();
            $table->string('price_currency', 10)->nullable();
            $table->string('price_amount', 255)->nullable();
            $table->string('currency_rate', 255)->nullable();
            $table->string('send_from_country', 255)->nullable();
            $table->text('send_from_city')->nullable();
            $table->string('send_to_country', 255)->nullable();
            $table->text('send_to_city')->nullable();
            $table->string('place_of_delivery_country', 255)->nullable();
            $table->text('place_of_delivery_city')->nullable();
            $table->string('grace_period', 255)->nullable();
            $table->string('snp_currency', 255)->nullable();
            $table->text('snp_range')->nullable();
            $table->string('snp_after_range', 255)->nullable();
            $table->text('invoices_generate')->nullable();
            $table->string('containers_type', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            //
        });
    }
}
