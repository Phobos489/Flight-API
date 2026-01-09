<?php
// app/Http/Controllers/AirlineController.php

namespace App\Http\Controllers;

use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AirlineController extends Controller
{
    /**
     * Display a listing of airlines
     */
    public function index(Request $request): JsonResponse
    {
        $query = Airline::query();

        // Filter active airlines
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Ubah dari paginate() ke get()
        $airlines = $query->get();

        return response()->json([
            'success' => true,
            'data' => $airlines,
            'total' => $airlines->count()
        ]);
    }

    /**
     * Store a newly created airline
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|unique:airlines,code',
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $airline = Airline::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Airline created successfully',
            'data' => $airline
        ], 201);
    }

    /**
     * Display the specified airline
     */
    public function show(Airline $airline): JsonResponse
    {
        $airline->load(['flights' => function ($query) {
            $query->with(['originAirport', 'destinationAirport'])
                  ->latest('scheduled_departure')
                  ->limit(10);
        }]);

        return response()->json([
            'success' => true,
            'data' => $airline
        ]);
    }

    /**
     * Update the specified airline
     */
    public function update(Request $request, Airline $airline): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'string|size:2|unique:airlines,code,' . $airline->id,
            'name' => 'string|max:255',
            'logo_url' => 'nullable|url',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $airline->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Airline updated successfully',
            'data' => $airline
        ]);
    }

    /**
     * Remove the specified airline
     */
    public function destroy(Airline $airline): JsonResponse
    {
        $airline->delete();

        return response()->json([
            'success' => true,
            'message' => 'Airline deleted successfully'
        ]);
    }
}