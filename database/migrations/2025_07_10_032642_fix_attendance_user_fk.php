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
    Schema::table('attendances', function (Blueprint $table) {
        // Cek apakah foreign key 'attendances_employee_id_foreign' masih ada sebelum mencoba menghapusnya.
        // Ini adalah cara yang lebih aman untuk menghapus FK.
        // Nama foreign key yang digunakan di dropForeign harus persis sama dengan yang ada di database.
        // Jika Anda yakin FK ini sudah tidak ada, Anda bisa menghapus seluruh blok if ini.
        // if (Schema::getConnection()->getDoctrineSchemaManager()->tablesExist(['attendances'])) { // Cek tabel ada
        //      $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('attendances');
        //      foreach ($foreignKeys as $fk) {
        //          if ($fk->getName() === 'employee_id') { // Cek nama FK
        //              $table->dropForeign('employee_id');
        //              break;
        //          }
        //      }
        // }

        // Sekarang, tambahkan foreign key yang benar, merujuk ke tabel 'users'
        // Pastikan kolom 'user_id' sudah ada dan bertipe unsignedBigInteger.
        // Jika Anda perlu menambahkannya lagi, lakukan di migrasi terpisah atau di atasnya.
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // Alternatif Laravel 8+: $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
        });
    }
};
