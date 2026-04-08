<?php

namespace App\Http\Controllers\Api;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveRequestController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::query();

        if ($request->user()->role->slug === 'hr' || $request->user()->role->slug === 'manager' || $request->user()->role->slug === 'admin') {
            // HR and managers see all leave requests
            $query->with(['user', 'manager']);
        } else {
            // Employees see only their own
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'leave_requests' => $query->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:annual,sick,personal,maternity,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $durationDays = $endDate->diffInDays($startDate) + 1;

        $leaveRequest = LeaveRequest::create([
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => $durationDays,
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Leave request created', 'leave_request' => $leaveRequest], 201);
    }

    public function approve(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::find($id);

        if (!$leaveRequest) {
            return response()->json(['message' => 'Leave request not found'], 404);
        }

        if ($request->user()->role->slug !== 'manager' && $request->user()->role->slug !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'manager_notes' => 'nullable|string',
        ]);

        $leaveRequest->update([
            'status' => $validated['status'],
            'manager_id' => $request->user()->id,
            'manager_notes' => $validated['manager_notes'] ?? null,
        ]);

        return response()->json(['message' => 'Leave request ' . $validated['status'], 'leave_request' => $leaveRequest]);
    }
}
