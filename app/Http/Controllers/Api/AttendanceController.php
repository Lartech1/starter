<?php

namespace App\Http\Controllers\Api;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends \App\Http\Controllers\Controller
{
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', $request->user()->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id' => $request->user()->id,
                'date' => $today,
                'check_in_time' => now()->toTimeString(),
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'present',
            ]);
        }

        return response()->json(['message' => 'Checked in successfully', 'attendance' => $attendance], 201);
    }

    public function checkOut(Request $request)
    {
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', $request->user()->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'No check-in found for today'], 404);
        }

        $attendance->update([
            'check_out_time' => now()->toTimeString(),
        ]);

        return response()->json(['message' => 'Checked out successfully', 'attendance' => $attendance]);
    }

    public function getAttendanceRecords(Request $request)
    {
        if ($request->user()->role->slug !== 'hr' && $request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Attendance::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        return response()->json([
            'attendance_records' => $query->with('user')->paginate(50),
        ]);
    }
}
