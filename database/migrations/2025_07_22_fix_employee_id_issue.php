<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu jika ada
            if (Schema::hasColumn('attendances', 'employee_id')) {
                // Cek apakah foreign key exists
                $foreignKeys = $this->getForeignKeys('attendances');
                
                foreach ($foreignKeys as $fk) {
                    if (str_contains($fk, 'employee_id')) {
                        try {
                            $table->dropForeign([$fk]);
                        } catch (Exception $e) {
                            // Jika gagal, coba dengan nama lengkap
                            try {
                                $table->dropForeign($fk);
                            } catch (Exception $e2) {
                                // Skip jika tidak bisa
                            }
                        }
                    }
                }
                
                // Hapus kolom employee_id
                $table->dropColumn('employee_id');
            }
            
            // Pastikan user_id ada
            if (!Schema::hasColumn('attendances', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }
        });

        // Tambahkan foreign key constraint untuk user_id secara terpisah
        $this->addUserIdForeignKey();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Hapus foreign key user_id terlebih dahulu
            try {
                $table->dropForeign(['user_id']);
            } catch (Exception $e) {
                // Skip jika tidak ada
            }
            
            // Tambah kembali kolom employee_id jika diperlukan rollback
            if (!Schema::hasColumn('attendances', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->after('id');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            }
        });
    }

    private function getForeignKeys($table)
    {
        $foreignKeys = [];
        try {
            $results = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$table]);
            
            foreach ($results as $result) {
                $foreignKeys[] = $result->CONSTRAINT_NAME;
            }
        } catch (Exception $e) {
            // Skip jika tidak bisa mendapatkan foreign keys
        }
        
        return $foreignKeys;
    }

    private function addUserIdForeignKey()
    {
        // Cek apakah foreign key user_id sudah ada
        $userIdForeignKeyExists = false;
        try {
            $results = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'attendances' 
                AND COLUMN_NAME = 'user_id' 
                AND REFERENCED_TABLE_NAME = 'users'
            ");
            
            $userIdForeignKeyExists = !empty($results);
        } catch (Exception $e) {
            // Skip
        }

        // Tambahkan foreign key jika belum ada
        if (!$userIdForeignKeyExists) {
            try {
                Schema::table('attendances', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                });
            } catch (Exception $e) {
                // Skip jika gagal
            }
        }
    }
}; 