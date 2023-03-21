<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ContainerProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_projects', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('container_id')->unsigned();
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->string('start_place')->nullable();
            $table->string('contract_with_terminal')->nullable();
            $table->string('rate_for_client_usd')->nullable();
            $table->string('rate_for_client_bank')->nullable();
            $table->string('rate_for_client_rub')->nullable();
            $table->string('grace_period')->nullable();
            $table->string('snp_amount_usd')->nullable();
            $table->string('snp_bank')->nullable();
            $table->string('snp_rub')->nullable();
            $table->date('svv')->nullable();
            $table->string('application_from_client')->nullable();
            $table->string('place_of_arrival')->nullable();
            $table->string('inspection_report')->nullable();
            $table->date('date_departure')->nullable();
            $table->string('contract_with_arrival_terminal')->nullable();
            $table->date('date_of_arrival')->nullable();
            $table->string('photos')->nullable();
            $table->string('need_repair')->nullable();
            $table->string('repair_usd')->nullable();
            $table->string('repair_bank')->nullable();
            $table->string('repair_rub')->nullable();
            $table->string('paid_usd')->nullable();
            $table->string('paid_bank')->nullable();
            $table->string('paid_rub')->nullable();
            $table->string('moving')->nullable();
            $table->string('drop_off_location')->nullable();
            $table->string('auto_add')->nullable();
            $table->string('status')->nullable();
            $table->text('additional_info')->nullable();
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
