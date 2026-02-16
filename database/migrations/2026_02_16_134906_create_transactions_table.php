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
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users'); // Admin/Staff yang melayani
                $table->dateTime('tgl_bayar');
                $table->integer('total_bayar');
                $table->integer('uang_diterima');
                $table->integer('uang_kembali');
                $table->enum('status_pembayaran', ['paid', 'unpaid'])->default('unpaid');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
