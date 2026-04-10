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
        // 1. Update tabel transactions: Tambah kolom transaction_type
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('transaction_type', ['payment', 'refund'])->default('payment')->after('status_pembayaran');
        });

        // 2. Update tabel enrollments: Tambah deleted_at & modify status_pembelajaran
        Schema::table('enrollments', function (Blueprint $table) {
            $table->softDeletes();
            // Menggunakan DB::statement untuk mengubah enum agar lebih aman
            DB::statement("ALTER TABLE enrollments MODIFY COLUMN status_pembelajaran ENUM('active', 'graduated', 'inactive', 'Keluar', 'Lulus') DEFAULT 'active'");
        });

        // 3. Update Foreign Key pada subjects: Ubah menjadi RESTRICT
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('restrict');
        });

        // 4. Update Foreign Key pada bundling_details: Ubah menjadi RESTRICT
        Schema::table('bundling_details', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['bundling_id']);
            $table->dropForeign(['subject_id']);

            // Re-add with RESTRICT
            $table->foreign('bundling_id')
                  ->references('id')
                  ->on('bundlings')
                  ->onDelete('restrict');

            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropSoftDeletes();
            DB::statement("ALTER TABLE enrollments MODIFY COLUMN status_pembelajaran ENUM('active', 'graduated', 'inactive') DEFAULT 'active'");
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });

        Schema::table('bundling_details', function (Blueprint $table) {
            $table->dropForeign(['bundling_id']);
            $table->dropForeign(['subject_id']);

            $table->foreign('bundling_id')
                  ->references('id')
                  ->on('bundlings')
                  ->onDelete('cascade');

            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('cascade');
        });
    }
};

