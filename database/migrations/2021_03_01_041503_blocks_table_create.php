<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BlocksTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->unsigned();
            $table->text('name');
            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->string('status')->nullable();
            $table->string('active')->default('waiting');
            $table->bigInteger('contract_id')->unsigned()->nullable();
            $table->text('files')->nullable();
            $table->text('index')->nullable();
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
