<?php

namespace App\Http\Controllers\Api;

use App\Models\PropertyAssignment;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyAssignmentController extends \App\Http\Controllers\Controller
{
    public function assignToRealtor(Request $request, $propertyId)
    {
        $property = Property::find($propertyId);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        if ($request->user()->role->slug !== 'estate_manager' && $request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'realtor_id' => 'required|exists:users,id',
        ]);

        // Check if already assigned
        $existing = PropertyAssignment::where('property_id', $propertyId)
            ->where('realtor_id', $validated['realtor_id'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Property already assigned to this realtor'], 400);
        }

        $assignment = PropertyAssignment::create([
            'property_id' => $propertyId,
            'realtor_id' => $validated['realtor_id'],
            'status' => 'assigned',
        ]);

        return response()->json(['message' => 'Property assigned', 'assignment' => $assignment], 201);
    }

    public function getMyAssignments(Request $request)
    {
        if ($request->user()->role->slug !== 'realtor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assignments = PropertyAssignment::where('realtor_id', $request->user()->id)
            ->with('property')
            ->paginate(15);

        return response()->json(['assignments' => $assignments]);
    }
}
