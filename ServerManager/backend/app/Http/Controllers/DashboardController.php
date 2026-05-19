<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Alert;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalServers = Server::count();
        $onlineServers = Server::where('status', 'online')->count();
        $offlineServers = $totalServers - $onlineServers;

        $activeAlerts = Alert::where('is_resolved', false)->count();
        $criticalAlerts = Alert::where('is_resolved', false)
            ->where('severity', 'critical')
            ->count();

        $avgCpuUsage = \DB::table('server_metrics')
            ->whereRaw('recorded_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)')
            ->avg('cpu_usage') ?? 0;

        $avgMemoryUsage = \DB::table('server_metrics')
            ->whereRaw('recorded_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)')
            ->avg('memory_usage') ?? 0;

        return response()->json([
            'total_servers' => $totalServers,
            'online_servers' => $onlineServers,
            'offline_servers' => $offlineServers,
            'active_alerts' => $activeAlerts,
            'critical_alerts' => $criticalAlerts,
            'avg_cpu_usage' => round($avgCpuUsage, 2),
            'avg_memory_usage' => round($avgMemoryUsage, 2),
        ]);
    }

    public function alertsSummary()
    {
        $alerts = Alert::where('is_resolved', false)
            ->with('server')
            ->groupBy('severity')
            ->selectRaw('severity, count(*) as count')
            ->get();

        return response()->json($alerts);
    }
}
