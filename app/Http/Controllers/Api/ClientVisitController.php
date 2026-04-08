<?php

namespace App\Http\Controllers\Api;

use App\Models\ClientVisit;
use Illuminate\Http\Request;

class ClientVisitController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = ClientVisit::query();

        if ($request->user()->role->slug === 'realtor') {
            $query->where('realtor_id', $request->user()->id);
        }

        if ($request->has('status')) {
            $query->where('offer_status', $request->status);
        }

        return response()->json([
            'client_visits' => $query->with(['realtor', 'property', 'manager'])->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->role->slug !== 'realtor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'client_name' => 'required|string',
            'client_phone' => 'nullable|string',
            'client_email' => 'nullable|email',
            'property_id' => 'required|exists:properties,id',
            'notes' => 'nullable|string',
            'outcome' => 'required|in:interested,not-interested,needs-followup,pending',
            'offered_price' => 'nullable|numeric',
        ]);

        $visit = ClientVisit::create([
            'realtor_id' => $request->user()->id,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'] ?? null,
            'client_email' => $validated['client_email'] ?? null,
            'property_id' => $validated['property_id'],
            'notes' => $validated['notes'] ?? null,
            'outcome' => $validated['outcome'],
            'offered_price' => $validated['offered_price'] ?? null,
            'offer_status' => 'pending',
            'visited_at' => now(),
        ]);

        return response()->json(['message' => 'Client visit logged', 'visit' => $visit], 201);
    }

    public function approve(Request $request, $id)
    {
        $visit = ClientVisit::find($id);

        if (!$visit) {
            return response()->json(['message' => 'Client visit not found'], 404);
        }

        if ($request->user()->role->slug !== 'manager' && $request->user()->role->slug !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'approved' => 'required|boolean',
            'offer_status' => 'required|in:pending,accepted,rejected,counter',
        ]);

        $visit->update([
            'approved' => $validated['approved'],
            'offer_status' => $validated['offer_status'],
            'manager_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Client visit approved/updated', 'visit' => $visit]);
    }
}
