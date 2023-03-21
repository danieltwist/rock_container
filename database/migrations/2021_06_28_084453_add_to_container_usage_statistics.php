<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToContainerUsageStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('container_usage_statistics', function (Blueprint $table) {
            $table->dropColumn('act_file');
            $table->dropColumn('snp_days');
            $table->string('snp_total_amount_for_client')->nullable()->after('svv');
            $table->string('snp_total_amount_for_us')->nullable()->after('snp_total_amount_for_client');
            $table->string('snp_days_for_client')->nullable()->after('return_date');
            $table->string('snp_days_for_us')->nullable()->after('snp_days_for_client');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('container_usage_statistics', function (Blueprint $table) {
            //
        });
    }
}
