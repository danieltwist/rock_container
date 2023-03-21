<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_updates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('update_id')->nullable();
            $table->bigInteger('chat_id')->nullable();
            $table->text('object')->nullable();
            $table->text('action')->nullable();
            $table->text('processed')->nullable();
            $table->text('info')->nullable();
            $table->text('text')->nullable();
            $table->bigInteger('message_id_remove')->nullable();
            $table->bigInteger('answer_id')->nullable();
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
        Schema::dropIfExists('telegram_updates');
    }
}
