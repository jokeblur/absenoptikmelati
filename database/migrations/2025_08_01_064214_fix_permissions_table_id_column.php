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
        Schema::table('permissions', function (Blueprint $table) {
            // Drop the existing id column
            $table->dropColumn('id');
        });

        Schema::table('permissions', function (Blueprint $table) {
            // Recreate the id column with proper auto-increment and primary key
            $table->id()->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // Drop the auto-increment id column
            $table->dropColumn('id');
        });

        Schema::table('permissions', function (Blueprint $table) {
            // Recreate the original id column without auto-increment
            $table->bigInteger('id')->unsigned()->first();
        });
    }
};
