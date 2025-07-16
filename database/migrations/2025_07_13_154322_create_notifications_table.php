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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'leave_request', 'permission_request'
            $table->string('title');
            $table->text('message');
            $table->unsignedBigInteger('user_id'); // Admin yang akan menerima notifikasi
            $table->unsignedBigInteger('related_id'); // ID dari leave atau permission
            $table->string('related_type'); // 'App\Models\Leave' atau 'App\Models\Permission'
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
