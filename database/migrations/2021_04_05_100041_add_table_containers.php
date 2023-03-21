<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableContainers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('type', 255)->nullable();
            $table->string('size', 255)->nullable();
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->bigInteger('project_group_id')->unsigned()->nullable();
            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->string('seal', 255)->nullable();
            $table->date('start_date')->nullable();
            $table->string('grace_period', 255)->nullable();
            $table->text('country')->nullable();
            $table->text('city')->nullable();
            $table->date('border_date')->nullable();
            $table->date('svv_prolong_to')->nullable();
            $table->text('additional_info')->nullable();
            $table->bigInteger('problem_id')->unsigned()->nullable();
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
