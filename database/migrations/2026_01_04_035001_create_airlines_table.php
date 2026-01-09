<?php
// database/migrations/2026_01_04_035335_create_airlines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->string('country')->default('Indonesia');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('airlines');
    }
};