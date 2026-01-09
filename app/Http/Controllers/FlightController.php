<?php
// app/Http/Controllers/FlightController.php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FlightController extends Controller
{
    /**
     * Display a listing of flights
     */
    public function index(Request $request): JsonResponse
    {
        $query = Flight::with(['airline', 'originAirport', 'destinationAirport']);

        // Filter by type (departure/arrival)
        if ($request->has('type')) {
            $airport = $request->get('airport', 'CGK');
            
            if ($request->type === 'departure') {
                $query->departures($airport);
            } elseif ($request->type === 'arrival') {
                $query->arrivals($airport);
            }
        }

        // Filter by date
        if ($request->has('date')) {
            $query->byDate($request->date);
        }
        // Jika tidak ada filter date, jangan filter by today
        // agar menampilkan semua data

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by airline
        if ($request->has('airline')) {
            $query->byAirline($request->airline);
        }

        // Filter by destination
        if ($request->has('destination')) {
            $query->whereHas('destinationAirport', function ($q) use ($request) {
                $q->where('code', $request->destination);
            });
        }

        // Search by flight number
        if ($request->has('search')) {
            $query->where('flight_number', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'scheduled_departure');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Ambil SEMUA data tanpa batasan
        $flights = $query->get();

        return response()->json([
            'success' => true,
            'data' => $flights,
            'total' => $flights->count()
        ]);
    }

    /**
     * Store a newly created flight
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'flight_number' => 'required|string|max:10',
            'origin_airport_id' => 'required|exists:airports,id',
            'destination_airport_id' => 'required|exists:airports,id|different:origin_airport_id',
            'scheduled_departure' => 'required|date|after:now',
            'scheduled_arrival' => 'required|date|after:scheduled_departure',
            'gate' => 'nullable|string|max:10',
            'terminal' => 'nullable|string|max:10',
            'status' => 'nullable|in:SCHEDULED,BOARDING,DEPARTED,DELAYED,CANCELLED,ARRIVED',
            'remarks' => 'nullable|string|max:500',
        ]);

        $flight = Flight::create($validated);
        $flight->load(['airline', 'originAirport', 'destinationAirport']);

        return response()->json([
            'success' => true,
            'message' => 'Flight created successfully',
            'data' => $flight
        ], 201);
    }

    /**
     * Display the specified flight
     */
    public function show(Flight $flight): JsonResponse
    {
        $flight->load(['airline', 'originAirport', 'destinationAirport']);

        return response()->json([
            'success' => true,
            'data' => $flight
        ]);
    }

    /**
     * Update the specified flight
     */
    public function update(Request $request, Flight $flight): JsonResponse
    {
        $validated = $request->validate([
            'airline_id' => 'exists:airlines,id',
            'flight_number' => 'string|max:10',
            'origin_airport_id' => 'exists:airports,id',
            'destination_airport_id' => 'exists:airports,id|different:origin_airport_id',
            'scheduled_departure' => 'date',
            'scheduled_arrival' => 'date',
            'actual_departure' => 'nullable|date',
            'actual_arrival' => 'nullable|date',
            'gate' => 'nullable|string|max:10',
            'terminal' => 'nullable|string|max:10',
            'status' => 'in:SCHEDULED,BOARDING,DEPARTED,DELAYED,CANCELLED,ARRIVED',
            'remarks' => 'nullable|string|max:500',
            'delay_minutes' => 'nullable|integer|min:0',
        ]);

        $flight->update($validated);
        $flight->load(['airline', 'originAirport', 'destinationAirport']);

        return response()->json([
            'success' => true,
            'message' => 'Flight updated successfully',
            'data' => $flight
        ]);
    }

    /**
     * Remove the specified flight
     */
    public function destroy(Flight $flight): JsonResponse
    {
        $flight->delete();

        return response()->json([
            'success' => true,
            'message' => 'Flight deleted successfully'
        ]);
    }

    /**
     * Update flight status
     */
    public function updateStatus(Request $request, Flight $flight): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:SCHEDULED,BOARDING,DEPARTED,DELAYED,CANCELLED,ARRIVED',
            'remarks' => 'nullable|string|max:500',
        ]);

        $flight->updateStatus(
            $validated['status'],
            $validated['remarks'] ?? null
        );

        $flight->load(['airline', 'originAirport', 'destinationAirport']);

        return response()->json([
            'success' => true,
            'message' => 'Flight status updated successfully',
            'data' => $flight
        ]);
    }

    /**
     * Get departure board
     */
    public function departures(Request $request): JsonResponse
    {
        $airport = $request->get('airport', 'CGK');
        $date = $request->get('date');

        $query = Flight::with(['airline', 'destinationAirport'])
            ->departures($airport)
            ->orderBy('scheduled_departure', 'asc');

        // Jika ada filter date, gunakan, jika tidak tampilkan semua
        if ($date) {
            $query->byDate($date);
        }

        $flights = $query->get();

        return response()->json([
            'success' => true,
            'airport' => $airport,
            'date' => $date ?? 'All dates',
            'type' => 'DEPARTURES',
            'data' => $flights,
            'total' => $flights->count()
        ]);
    }

    /**
     * Get arrival board
     */
    public function arrivals(Request $request): JsonResponse
    {
        $airport = $request->get('airport', 'CGK');
        $date = $request->get('date');

        $query = Flight::with(['airline', 'originAirport'])
            ->arrivals($airport)
            ->orderBy('scheduled_arrival', 'asc');

        // Jika ada filter date, gunakan, jika tidak tampilkan semua
        if ($date) {
            $query->byDate($date);
        }

        $flights = $query->get();

        return response()->json([
            'success' => true,
            'airport' => $airport,
            'date' => $date ?? 'All dates',
            'type' => 'ARRIVALS',
            'data' => $flights,
            'total' => $flights->count()
        ]);
    }
}