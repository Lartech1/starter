<?php

namespace App\Http\Controllers\Api;

use App\Models\ProjectUpdate;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectUpdateController extends \App\Http\Controllers\Controller
{
    public function submitUpdate(Request $request, $projectId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        if ($request->user()->role->slug !== 'field_agent' && $request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'completion_percentage' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
            'expenses' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $update = ProjectUpdate::create([
            'project_id' => $projectId,
            'field_agent_id' => $request->user()->id,
            'completion_percentage' => $validated['completion_percentage'],
            'description' => $validated['description'] ?? null,
            'expenses' => $validated['expenses'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update project completion percentage
        $project->update(['completion_percentage' => $validated['completion_percentage']]);

        return response()->json(['message' => 'Project update submitted', 'update' => $update], 201);
    }

    public function getProjectUpdates(Request $request, $projectId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $updates = ProjectUpdate::where('project_id', $projectId)
            ->with('fieldAgent')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['updates' => $updates]);
    }
}
