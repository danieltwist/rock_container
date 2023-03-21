<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InvoicesTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->text('direction');
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->bigInteger('block_id')->unsigned()->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->bigInteger('amount');
            $table->bigInteger('amount_actual')->nullable();
            $table->bigInteger('amount_paid')->nullable();
            $table->string('status')->nullable();
            $table->string('director_comment')->nullable();
            $table->string('manager_comment')->nullable();
            $table->string('accountant_comment')->nullable();
            $table->text('file')->nullable();
            $table->text('agree_1')->nullable();
            $table->text('agree_2')->nullable();
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
