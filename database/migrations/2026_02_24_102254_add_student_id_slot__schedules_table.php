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
            Schema::table('schedules', function (Blueprint $table) {
                // Cek dulu apakah kolom sudah ada sebelum menambahkannya
                if (!Schema::hasColumn('schedules', 'capacity')) {
                    $table->integer('capacity')->default(1)->after('ruangan');
                }
                
                if (!Schema::hasColumn('schedules', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('capacity');
                }

                // JANGAN tambah student_id di sini agar bisa menampung banyak siswa via enrollment
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
           $table->dropColumn(['capacity', 'is_active']);
        });
    }
};
