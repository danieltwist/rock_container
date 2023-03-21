<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableContainerProblems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_problems', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('container_id')->unsigned();
            $table->text('problem');
            $table->date('problem_date')->nullable();
            $table->text('problem_photos_folder')->nullable();
            $table->text('who_fault')->nullable();
            $table->text('problem_photos_solved_folder')->nullable();
            $table->date('problem_solved_date')->nullable();
            $table->text('amount')->nullable();
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
        Schema::dropIfExists('table_container_problems');
    }
}
