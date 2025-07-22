<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'custom_clock_in_time')) {
                $table->dropColumn('custom_clock_in_time');
            }
            if (Schema::hasColumn('users', 'custom_clock_out_time')) {
                $table->dropColumn('custom_clock_out_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'custom_clock_in_time')) {
                $table->time('custom_clock_in_time')->nullable();
            }
            if (!Schema::hasColumn('users', 'custom_clock_out_time')) {
                $table->time('custom_clock_out_time')->nullable();
            }
        });
    }
};
