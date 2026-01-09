<?php
// app/Models/Airport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'city',
        'country',
        'timezone',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Get departing flights from this airport
     */
    public function departingFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'origin_airport_id');
    }

    /**
     * Get arriving flights to this airport
     */
    public function arrivingFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'destination_airport_id');
    }

    /**
     * Scope untuk filter airport aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full airport identifier (code - name, city)
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->code} - {$this->name}, {$this->city}";
    }
}