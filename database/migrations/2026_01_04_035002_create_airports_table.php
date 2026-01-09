<?php
// database/migrations/2026_01_04_035351_create_airports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->string('city');
            $table->string('country')->default('Indonesia');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};