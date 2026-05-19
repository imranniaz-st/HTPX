<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::with(['metrics' => function($query) {
            $query->latest()->limit(1);
        }])->paginate(15);

        return response()->json($servers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:servers',
            'ip_address' => 'required|ip|unique:servers',
            'hostname' => 'required|string|max:255',
            'ssh_port' => 'integer|default:22',
            'ssh_username' => 'string|max:100',
            'os_type' => 'string|in:linux,windows,macos',
            'description' => 'nullable|string',
        ]);

        $server = Server::create($validated);

        return response()->json($server, 201);
    }

    public function show(Server $server)
    {
        $server->load(['metrics' => function($query) {
            $query->latest()->limit(20);
        }, 'alerts' => function($query) {
            $query->where('is_resolved', false)->latest();
        }]);

        return response()->json($server);
    }

    public function update(Request $request, Server $server)
    {
        $validated = $request->validate([
            'name' => 'string|max:255|unique:servers,name,' . $server->id,
            'ip_address' => 'ip|unique:servers,ip_address,' . $server->id,
            'hostname' => 'string|max:255',
            'ssh_port' => 'integer',
            'ssh_username' => 'string|max:100',
            'description' => 'nullable|string',
        ]);

        $server->update($validated);

        return response()->json($server);
    }

    public function destroy(Server $server)
    {
        $server->metrics()->delete();
        $server->alerts()->delete();
        $server->firewall_rules()->delete();
        $server->delete();

        return response()->json(null, 204);
    }
}
