<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OwnContainers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('own_containers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('container_id')->unsigned();
            $table->string('prime_cost')->nullable();
            $table->date('date_of_purchase')->nullable();
            $table->string('last_place')->nullable();
            $table->string('place_of_purchase')->nullable();
            $table->string('status')->nullable();
            $table->string('additional_info')->nullable();
            $table->string('expense')->nullable();
            $table->string('income')->nullable();
            $table->string('profit')->nullable();
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
