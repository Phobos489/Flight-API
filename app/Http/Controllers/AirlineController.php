<?php
// app/Http/Controllers/AirlineController.php

namespace App\Http\Controllers;

use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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

        $airlines = $query->get();

        // Add full URL for logo
        $airlines->transform(function ($airline) {
            if ($airline->logo_url && !filter_var($airline->logo_url, FILTER_VALIDATE_URL)) {
                $airline->logo_url = asset('storage/' . $airline->logo_url);
            }
            return $airline;
        });

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_url' => 'nullable|url',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('airlines/logos', 'public');
            $validated['logo_url'] = $logoPath;
        }

        $airline = Airline::create($validated);

        // Add full URL for response
        if ($airline->logo_url && !filter_var($airline->logo_url, FILTER_VALIDATE_URL)) {
            $airline->logo_url = asset('storage/' . $airline->logo_url);
        }

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

        // Add full URL for logo
        if ($airline->logo_url && !filter_var($airline->logo_url, FILTER_VALIDATE_URL)) {
            $airline->logo_url = asset('storage/' . $airline->logo_url);
        }

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_url' => 'nullable|url',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($airline->logo_url && !filter_var($airline->logo_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($airline->logo_url);
            }
            
            $logoPath = $request->file('logo')->store('airlines/logos', 'public');
            $validated['logo_url'] = $logoPath;
        }

        $airline->update($validated);

        // Add full URL for response
        if ($airline->logo_url && !filter_var($airline->logo_url, FILTER_VALIDATE_URL)) {
            $airline->logo_url = asset('storage/' . $airline->logo_url);
        }

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
        // Delete logo if exists
        if ($airline->logo_url && !filter_var($airline->logo_url, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($airline->logo_url);
        }

        $airline->delete();

        return response()->json([
            'success' => true,
            'message' => 'Airline deleted successfully'
        ]);
    }
}