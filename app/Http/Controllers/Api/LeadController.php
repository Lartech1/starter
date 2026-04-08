<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->user()->role->slug === 'realtor') {
            $query->where('assigned_to', $request->user()->id);
        } elseif ($request->user()->role->slug === 'marketer') {
            $query->where('marketer_id', $request->user()->id);
        } elseif ($request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'leads' => $query->with(['assignedTo', 'marketer'])->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'message' => 'nullable|string',
            'value' => 'nullable|numeric',
        ]);

        $validated['marketer_id'] = $request->user()->id;
        $validated['status'] = 'new';

        $lead = Lead::create($validated);

        return response()->json(['message' => 'Lead created', 'lead' => $lead], 201);
    }

    public function update(Request $request, $id)
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:new,contacted,qualified,lost,converted',
            'assigned_to' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date',
            'value' => 'nullable|numeric',
        ]);

        $lead->update($validated);

        return response()->json(['message' => 'Lead updated', 'lead' => $lead]);
    }

    public function assignLead(Request $request, $id)
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        if ($request->user()->role->slug !== 'marketer' && $request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $lead->update($validated);

        return response()->json(['message' => 'Lead assigned', 'lead' => $lead]);
    }
}
