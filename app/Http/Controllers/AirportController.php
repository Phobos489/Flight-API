<?php
// app/Http/Controllers/AirportController.php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AirportController extends Controller
{
    /**
     * Display a listing of airports
     */
    public function index(Request $request): JsonResponse
    {
        $query = Airport::query();

        // Filter active airports
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter by country
        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'city');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $airports = $query->paginate($perPage);

        return response()->json($airports);
    }

    /**
     * Store a newly created airport
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:airports,code',
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $airport = Airport::create($validated);

        return response()->json([
            'message' => 'Airport created successfully',
            'data' => $airport
        ], 201);
    }

    /**
     * Display the specified airport
     */
    public function show(Airport $airport): JsonResponse
    {
        $airport->loadCount(['departingFlights', 'arrivingFlights']);

        return response()->json($airport);
    }

    /**
     * Update the specified airport
     */
    public function update(Request $request, Airport $airport): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'string|size:3|unique:airports,code,' . $airport->id,
            'name' => 'string|max:255',
            'city' => 'string|max:100',
            'country' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ]);

        $airport->update($validated);

        return response()->json([
            'message' => 'Airport updated successfully',
            'data' => $airport
        ]);
    }

    /**
     * Remove the specified airport
     */
    public function destroy(Airport $airport): JsonResponse
    {
        $airport->delete();

        return response()->json([
            'message' => 'Airport deleted successfully'
        ]);
    }

    /**
     * Show the form for creating a new resource (not used in API)
     */
    public function create()
    {
        // Not used in API
    }

    /**
     * Show the form for editing the specified resource (not used in API)
     */
    public function edit(Airport $airport)
    {
        // Not used in API
    }
}