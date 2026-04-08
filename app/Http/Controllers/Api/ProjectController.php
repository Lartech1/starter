<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'projects' => $query->with(['manager', 'updates'])->paginate(15),
        ]);
    }

    public function show($id)
    {
        $project = Project::find($id)->load(['manager', 'updates.fieldAgent']);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        return response()->json(['project' => $project]);
    }

    public function store(Request $request)
    {
        if ($request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:residential,commercial,school,industrial,other',
            'location' => 'nullable|string',
            'status' => 'sometimes|in:planning,ongoing,completed,suspended',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'budget' => 'nullable|numeric',
        ]);

        $validated['manager_id'] = $request->user()->id;
        $validated['completion_percentage'] = 0;

        $project = Project::create($validated);

        return response()->json(['message' => 'Project created', 'project' => $project], 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        if ($request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:residential,commercial,school,industrial,other',
            'location' => 'nullable|string',
            'status' => 'sometimes|in:planning,ongoing,completed,suspended',
            'completion_percentage' => 'sometimes|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'spent' => 'nullable|numeric',
        ]);

        $project->update($validated);

        return response()->json(['message' => 'Project updated', 'project' => $project]);
    }
}
