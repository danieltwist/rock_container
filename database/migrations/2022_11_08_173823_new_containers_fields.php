<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewContainersFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('containers', function (Blueprint $table) {

            $table->bigInteger('project_id')->nullable()->unsigned();
            $table->text('name')->nullable();
            $table->text('status')->nullable();
            $table->text('type')->nullable();
            $table->bigInteger('owner_id')->nullable()->unsigned();
            $table->string('owner_name', 255)->nullable();
            $table->string('size', 255)->nullable();
            $table->string('svv', 255)->nullable();
            $table->text('additional_info')->nullable();
            $table->date('own_date_buy')->nullable();
            $table->date('own_date_sell')->nullable();
            $table->string('own_sale_price', 255)->nullable();
            $table->string('own_buyer', 255)->nullable();

            $table->bigInteger('supplier_application_id')->nullable()->unsigned();
            $table->text('supplier_application_name')->nullable();
            $table->string('supplier_price_amount', 255)->nullable();
            $table->string('supplier_price_currency', 255)->nullable();
            $table->string('supplier_price_in_rubles', 255)->nullable();
            $table->string('supplier_grace_period', 255)->nullable();
            $table->text('supplier_snp_range')->nullable();
            $table->string('supplier_snp_after_range', 255)->nullable();
            $table->string('supplier_snp_currency', 255)->nullable();
            $table->string('supplier_country', 255)->nullable();
            $table->text('supplier_city')->nullable();
            $table->string('supplier_terminal', 255)->nullable();
            $table->date('supplier_date_get')->nullable();
            $table->date('supplier_date_start_using')->nullable();
            $table->string('supplier_days_using', 255)->nullable();
            $table->string('supplier_snp_total', 255)->nullable();
            $table->string('supplier_place_of_delivery_country', 255)->nullable();
            $table->text('supplier_place_of_delivery_city')->nullable();
            $table->string('supplier_terminal_storage_amount', 255)->nullable();
            $table->string('supplier_terminal_storage_currency', 255)->nullable();
            $table->string('supplier_terminal_storage_in_rubles', 255)->nullable();
            $table->string('supplier_payer_tx', 255)->nullable();
            $table->string('supplier_renewal_reexport_costs_amount', 255)->nullable();
            $table->string('supplier_renewal_reexport_costs_currency', 255)->nullable();
            $table->string('supplier_repair_amount', 255)->nullable();
            $table->string('supplier_repair_currency', 255)->nullable();
            $table->string('supplier_repair_in_rubles', 255)->nullable();
            $table->string('supplier_repair_status', 255)->nullable();
            $table->string('supplier_repair_confirmation', 255)->nullable();

            $table->bigInteger('relocation_counterparty_id')->nullable()->unsigned();
            $table->text('relocation_counterparty_name')->nullable();
            $table->text('relocation_counterparty_type')->nullable();
            $table->bigInteger('relocation_application_id')->nullable()->unsigned();
            $table->text('relocation_application_name')->nullable();
            $table->string('relocation_price_amount', 255)->nullable();
            $table->string('relocation_price_currency', 255)->nullable();
            $table->string('relocation_price_in_rubles', 255)->nullable();
            $table->date('relocation_date_send')->nullable();
            $table->date('relocation_date_arrival_to_terminal')->nullable();
            $table->text('relocation_place_of_delivery_city')->nullable();
            $table->string('relocation_place_of_delivery_terminal', 255)->nullable();
            $table->string('relocation_delivery_time_days', 255)->nullable();
            $table->text('relocation_snp_range')->nullable();
            $table->string('relocation_snp_after_range', 255)->nullable();
            $table->string('relocation_snp_currency', 255)->nullable();
            $table->string('relocation_snp_total', 255)->nullable();
            $table->string('relocation_repair_amount', 255)->nullable();
            $table->string('relocation_repair_currency', 255)->nullable();
            $table->string('relocation_repair_in_rubles', 255)->nullable();
            $table->string('relocation_repair_status', 255)->nullable();
            $table->string('relocation_repair_confirmation', 255)->nullable();

            $table->bigInteger('client_counterparty_id')->nullable()->unsigned();
            $table->text('client_counterparty_name')->nullable();
            $table->bigInteger('client_application_id')->nullable()->unsigned();
            $table->text('client_application_name')->nullable();
            $table->string('client_price_amount', 255)->nullable();
            $table->string('client_price_currency', 255)->nullable();
            $table->string('client_price_in_rubles', 255)->nullable();
            $table->string('client_grace_period', 255)->nullable();
            $table->text('client_snp_range')->nullable();
            $table->string('client_snp_after_range', 255)->nullable();
            $table->string('client_snp_currency', 255)->nullable();
            $table->string('client_snp_in_rubles', 255)->nullable();
            $table->date('client_date_get')->nullable();
            $table->date('client_date_return')->nullable();
            $table->string('client_place_of_delivery_country', 255)->nullable();
            $table->text('client_place_of_delivery_city')->nullable();
            $table->string('client_days_using', 255)->nullable();
            $table->string('client_snp_total', 255)->nullable();
            $table->string('client_repair_amount', 255)->nullable();
            $table->string('client_repair_currency', 255)->nullable();
            $table->string('client_repair_in_rubles', 255)->nullable();
            $table->string('client_repair_status', 255)->nullable();
            $table->string('client_repair_confirmation', 255)->nullable();
            $table->string('client_smgs', 255)->nullable();
            $table->string('client_manual', 255)->nullable();
            $table->string('client_location_request', 255)->nullable();
            $table->date('client_date_manual_request')->nullable();
            $table->string('client_return_act', 255)->nullable();
            $table->string('archive', 255)->nullable();
            $table->string('removed', 255)->nullable();
            $table->string('processing', 255)->nullable();

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
