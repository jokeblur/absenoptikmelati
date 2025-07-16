<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->time('custom_clock_in_time')->nullable();
        $table->time('custom_clock_out_time')->nullable();
    });
}
public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['custom_clock_in_time', 'custom_clock_out_time']);
    });
}
};
