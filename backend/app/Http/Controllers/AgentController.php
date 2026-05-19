<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\ServerMetric;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function heartbeat(Request $request)
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'api_key' => 'required|string',
        ]);

        $server = Server::findOrFail($validated['server_id']);
        // Verify API key here
        $server->update(['last_heartbeat' => now()]);

        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    }

    public function submitMetrics(Request $request)
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'api_key' => 'required|string',
            'cpu_usage' => 'numeric',
            'memory_usage' => 'numeric',
            'memory_total' => 'numeric',
            'disk_usage' => 'numeric',
            'disk_total' => 'numeric',
            'disk_free' => 'numeric',
            'network_in' => 'numeric',
            'network_out' => 'numeric',
            'load_average' => 'numeric',
        ]);

        $server = Server::findOrFail($validated['server_id']);

        $metric = $server->metrics()->create([
            'cpu_usage' => $validated['cpu_usage'] ?? null,
            'memory_usage' => $validated['memory_usage'] ?? null,
            'memory_total' => $validated['memory_total'] ?? null,
            'disk_usage' => $validated['disk_usage'] ?? null,
            'disk_total' => $validated['disk_total'] ?? null,
            'disk_free' => $validated['disk_free'] ?? null,
            'network_in' => $validated['network_in'] ?? null,
            'network_out' => $validated['network_out'] ?? null,
            'load_average' => $validated['load_average'] ?? null,
            'recorded_at' => now(),
        ]);

        return response()->json(['status' => 'stored', 'metric_id' => $metric->id]);
    }

    public function getTasks(Request $request)
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'api_key' => 'required|string',
        ]);

        // TODO: Implement task queue system
        return response()->json(['tasks' => []]);
    }

    public function submitTaskResult(Request $request)
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'task_id' => 'required|string',
            'status' => 'required|in:success,failed',
            'result' => 'nullable|array',
        ]);

        // TODO: Process task result
        return response()->json(['status' => 'received']);
    }
}
