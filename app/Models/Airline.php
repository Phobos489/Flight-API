<?php
// app/Models/Airline.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Airline extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'logo_url',
        'country',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all flights for this airline
     */
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    /**
     * Scope untuk filter airline aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full airline identifier (code - name)
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }
}