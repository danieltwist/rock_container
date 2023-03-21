<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name')->nullable();
            $table->string('model')->nullable();
            $table->bigInteger('model_id')->unsigned()->nullable();
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->text('object')->nullable();
            $table->string('send_to')->nullable();
            $table->string('responsible_user')->nullable();
            $table->string('additional_users')->nullable();
            $table->bigInteger('from_user_id')->unsigned()->nullable();
            $table->string('to_users');
            $table->bigInteger('accepted_user_id')->unsigned()->nullable();
            $table->text('text')->nullable();
            $table->string('info')->nullable();
            $table->string('status');
            $table->longText('comment')->nullable();
            $table->longText('history')->nullable();
            $table->text('file')->nullable();
            $table->text('object_array')->nullable();
            $table->string('active');
            $table->string('can_change_deadline')->nullable();
            $table->string('check_work')->nullable();
            $table->dateTime('done')->nullable();
            $table->dateTime('deadline')->nullable();
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
