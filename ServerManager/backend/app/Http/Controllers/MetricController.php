<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\ServerMetric;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    public function getLatest(Server $server)
    {
        $metric = $server->metrics()->latest()->first();

        return response()->json($metric);
    }

    public function getHistory(Request $request, Server $server)
    {
        $hours = $request->query('hours', 24);
        $limit = $request->query('limit', 100);

        $metrics = $server->metrics()
            ->where('recorded_at', '>=', now()->subHours($hours))
            ->orderBy('recorded_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($metrics);
    }
}
