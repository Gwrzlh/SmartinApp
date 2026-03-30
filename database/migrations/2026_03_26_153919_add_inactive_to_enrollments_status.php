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
       Schema::table('enrollments', function (Blueprint $table) {
        // Kita ubah definition enum-nya
            DB::statement("ALTER TABLE enrollments MODIFY COLUMN status_pembelajaran ENUM('active', 'graduated', 'inactive') DEFAULT 'active'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments_status', function (Blueprint $table) {
            //
        });
    }
};
