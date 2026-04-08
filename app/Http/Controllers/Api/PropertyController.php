<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Property::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%");
        }

        return response()->json([
            'properties' => $query->with('estateManager')->paginate(12),
        ]);
    }

    public function show($id)
    {
        $property = Property::find($id)->load(['estateManager', 'assignments', 'clientVisits']);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json(['property' => $property]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:land,duplex,flat,house,commercial,blocks',
            'price' => 'nullable|numeric',
            'rental_price' => 'nullable|numeric',
            'area_size' => 'nullable|numeric',
            'location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'has_bq' => 'nullable|boolean',
            'features' => 'nullable|string',
            'project_name' => 'nullable|string',
        ]);

        $validated['estate_manager_id'] = $request->user()->id;

        $property = Property::create($validated);

        return response()->json(['message' => 'Property created', 'property' => $property], 201);
    }

    public function update(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        // Check authorization
        if ($property->estate_manager_id !== $request->user()->id && $request->user()->role->slug !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:land,duplex,flat,house,commercial,blocks',
            'price' => 'nullable|numeric',
            'rental_price' => 'nullable|numeric',
            'area_size' => 'nullable|numeric',
            'location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'has_bq' => 'nullable|boolean',
            'features' => 'nullable|string',
            'project_name' => 'nullable|string',
            'status' => 'sometimes|in:available,sold,rented,on-hold,under-maintenance',
        ]);

        $property->update($validated);

        return response()->json(['message' => 'Property updated', 'property' => $property]);
    }

    public function destroy($id, Request $request)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        // Only admin can delete
        if ($request->user()->role->slug !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted']);
    }
}
