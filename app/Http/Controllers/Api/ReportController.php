<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use App\Models\Project;
use App\Models\Lead;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends \App\Http\Controllers\Controller
{
    public function salesReport(Request $request)
    {
        if ($request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $propertiesSold = Property::where('status', 'sold')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $totalRevenue = Property::where('status', 'sold')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('price');

        $ordersDelivered = Order::where('status', 'delivered')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $ordersRevenue = Order::where('status', 'delivered')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('total_price');

        return response()->json([
            'report' => [
                'period' => [$startDate, $endDate],
                'properties_sold' => $propertiesSold,
                'properties_revenue' => $totalRevenue,
                'orders_delivered' => $ordersDelivered,
                'orders_revenue' => $ordersRevenue,
                'total_revenue' => $totalRevenue + $ordersRevenue,
            ]
        ]);
    }

    public function projectReport(Request $request)
    {
        if ($request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $projects = Project::with('updates')->get();

        $report = $projects->map(function ($project) {
            $latestUpdate = $project->updates()->latest()->first();
            return [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
                'completion_percentage' => $project->completion_percentage,
                'budget' => $project->budget,
                'spent' => $project->spent,
                'latest_update' => $latestUpdate ? $latestUpdate->created_at : null,
            ];
        });

        return response()->json(['projects' => $report]);
    }

    public function leadConversionReport(Request $request)
    {
        if ($request->user()->role->slug !== 'admin' && $request->user()->role->slug !== 'manager' && $request->user()->role->slug !== 'marketer') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();
        $convertedLeads = Lead::where('status', 'converted')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $conversionRate = $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;

        $leadsByStatus = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return response()->json([
            'report' => [
                'period' => [$startDate, $endDate],
                'total_leads' => $totalLeads,
                'converted_leads' => $convertedLeads,
                'conversion_rate' => round($conversionRate, 2),
                'leads_by_status' => $leadsByStatus,
            ]
        ]);
    }
}
