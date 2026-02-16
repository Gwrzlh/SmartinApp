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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            
            // Opsional: Hubungkan ke detail transaksi sebagai bukti bayar
            $table->foreignId('transaction_detail_id')->constrained('transaction_details')->cascadeOnDelete();
            
            $table->string('item_type'); 
            $table->unsignedBigInteger('item_id');
            
            $table->date('tgl_daftar');
            $table->enum('status_pembelajaran', ['active', 'graduated'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
