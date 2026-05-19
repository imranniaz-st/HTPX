<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\ServerLog;
use Illuminate\Http\Request;

class ServerLogController extends Controller
{
    public function index(Server $server, Request $request)
    {
        $query = $server->logs();

        if ($request->query('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->query('level')) {
            $query->where('level', $request->query('level'));
        }

        if ($request->query('days')) {
            $query->where('timestamp', '>=', now()->subDays($request->query('days')));
        }

        $logs = $query->latest('timestamp')->paginate(50);

        return response()->json($logs);
    }

    public function show(Server $server, ServerLog $log)
    {
        if ($log->server_id !== $server->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($log);
    }

    public function store(Request $request, Server $server)
    {
        $validated = $request->validate([
            'type' => 'required|in:system,ssh,firewall,alert,password_change,reboot,package,custom',
            'level' => 'required|in:debug,info,warning,error',
            'title' => 'string|max:255',
            'message' => 'string',
            'command' => 'string',
            'output' => 'string',
            'error' => 'string',
            'status_code' => 'integer',
            'source' => 'in:system,agent,user,remote',
        ]);

        $log = $server->logs()->create(array_merge($validated, [
            'user_id' => auth()->id(),
            'timestamp' => now(),
        ]));

        return response()->json($log, 201);
    }

    public function download(Server $server, Request $request)
    {
        $logs = $server->logs()
            ->latest('timestamp')
            ->limit(1000)
            ->get();

        $csv = "Timestamp,Type,Level,Title,Message,Command,Output,Error,User\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log->timestamp,
                $log->type,
                $log->level,
                $log->title,
                str_replace('"', '""', $log->message),
                str_replace('"', '""', $log->command),
                str_replace('"', '""', $log->output),
                str_replace('"', '""', $log->error),
                $log->user?->name ?? 'System'
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="server-' . $server->id . '-logs.csv"',
        ]);
    }

    public function clearOldLogs(Server $server, Request $request)
    {
        $days = $request->query('days', 30);

        $deleted = $server->logs()
            ->where('timestamp', '<', now()->subDays($days))
            ->delete();

        return response()->json([
            'message' => "Deleted $deleted logs older than $days days",
            'count' => $deleted,
        ]);
    }
}
