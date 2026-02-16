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
       Schema::create('bundlings', function (Blueprint $table) {
            $table->id();
            $table->string('bundling_name');
            $table->integer('bundling_price');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('bundling_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundling_id')->constrained('bundlings')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundlings');
    }
};
