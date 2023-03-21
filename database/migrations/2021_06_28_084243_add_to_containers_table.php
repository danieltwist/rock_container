<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('containers', function (Blueprint $table) {

            $table->dropColumn('grace_period');
            $table->string('grace_period_for_client')->nullable()->after('start_date');
            $table->string('grace_period_for_us')->nullable()->after('grace_period_for_client');
            $table->string('snp_amount_for_client')->nullable();
            $table->string('snp_amount_for_us')->nullable();
            $table->string('snp_currency')->nullable();
            $table->dropColumn('svv_prolong_to');
            $table->date('svv')->nullable()->after('snp_currency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('containers', function (Blueprint $table) {
            //
        });
    }
}
