<?php
// database/migrations/2026_01_04_035258_create_flights_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->string('flight_number');
            $table->foreignId('origin_airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignId('destination_airport_id')->constrained('airports')->onDelete('cascade');
            $table->dateTime('scheduled_departure');
            $table->dateTime('scheduled_arrival');
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('actual_arrival')->nullable();
            $table->string('gate', 10)->nullable();
            $table->string('terminal', 10)->nullable();
            $table->enum('status', [
                'SCHEDULED',
                'BOARDING',
                'DEPARTED',
                'DELAYED',
                'CANCELLED',
                'ARRIVED'
            ])->default('SCHEDULED');
            $table->text('remarks')->nullable();
            $table->integer('delay_minutes')->default(0);
            $table->timestamps();
            
            $table->index(['scheduled_departure', 'status']);
            $table->index(['origin_airport_id', 'scheduled_departure']);
            $table->index(['destination_airport_id', 'scheduled_arrival']);
            $table->index('flight_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};