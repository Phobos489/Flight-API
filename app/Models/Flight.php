<?php
// app/Models/Flight.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'airline_id',
        'flight_number',
        'origin_airport_id',
        'destination_airport_id',
        'scheduled_departure',
        'scheduled_arrival',
        'actual_departure',
        'actual_arrival',
        'gate',
        'terminal',
        'status',
        'remarks',
        'delay_minutes',
    ];

    protected $casts = [
        'scheduled_departure' => 'datetime',
        'scheduled_arrival' => 'datetime',
        'actual_departure' => 'datetime',
        'actual_arrival' => 'datetime',
        'delay_minutes' => 'integer',
    ];

    /**
     * Relationships
     */
    public function airline(): BelongsTo
    {
        return $this->belongsTo(Airline::class);
    }

    public function originAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'origin_airport_id');
    }

    public function destinationAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'destination_airport_id');
    }

    /**
     * Scopes
     */
    public function scopeDepartures($query, $airportCode = null)
    {
        if ($airportCode) {
            $query->whereHas('originAirport', function ($q) use ($airportCode) {
                $q->where('code', $airportCode);
            });
        }
        return $query;
    }

    public function scopeArrivals($query, $airportCode = null)
    {
        if ($airportCode) {
            $query->whereHas('destinationAirport', function ($q) use ($airportCode) {
                $q->where('code', $airportCode);
            });
        }
        return $query;
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_departure', today());
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('scheduled_departure', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', strtoupper($status));
    }

    public function scopeByAirline($query, $airlineCode)
    {
        return $query->whereHas('airline', function ($q) use ($airlineCode) {
            $q->where('code', $airlineCode);
        });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_departure', '>', now())
                     ->whereIn('status', ['SCHEDULED', 'BOARDING', 'DELAYED']);
    }

    /**
     * Accessors & Mutators
     */
    public function getIsDelayedAttribute(): bool
    {
        return $this->delay_minutes > 0 || $this->status === 'DELAYED';
    }

    public function getFormattedDepartureTimeAttribute(): string
    {
        return $this->scheduled_departure->format('H:i');
    }

    public function getFormattedArrivalTimeAttribute(): string
    {
        return $this->scheduled_arrival->format('H:i');
    }

    public function getFlightDurationAttribute(): int
    {
        return $this->scheduled_departure->diffInMinutes($this->scheduled_arrival);
    }

    /**
     * Methods
     */
    public function updateStatus(string $status, ?string $remarks = null): bool
    {
        $this->status = strtoupper($status);
        
        if ($remarks) {
            $this->remarks = $remarks;
        }

        // Auto update actual times based on status
        if ($status === 'DEPARTED' && !$this->actual_departure) {
            $this->actual_departure = now();
        }

        if ($status === 'ARRIVED' && !$this->actual_arrival) {
            $this->actual_arrival = now();
        }

        return $this->save();
    }

    public function calculateDelay(): int
    {
        if ($this->actual_departure && $this->scheduled_departure) {
            $this->delay_minutes = $this->scheduled_departure
                ->diffInMinutes($this->actual_departure, false);
            $this->save();
        }

        return $this->delay_minutes;
    }
}