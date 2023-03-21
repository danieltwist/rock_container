<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableContainerGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->bigInteger('project_id')->unsigned();
            $table->text('containers');
            $table->date('start_date')->nullable();
            $table->date('border_date')->nullable();
            $table->date('return_date')->nullable();
            $table->text('additional_info')->nullable();
            $table->longText('usage_stat')->nullable();
            $table->timestamps();
        });

        Schema::create('container_group_locations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('container_group_id')->unsigned();
            $table->date('date');
            $table->text('country');
            $table->text('city');
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
