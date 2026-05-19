<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\FirewallRule;
use Illuminate\Http\Request;

class FirewallRuleController extends Controller
{
    public function index(Server $server)
    {
        $rules = $server->firewall_rules()->paginate(20);

        return response()->json($rules);
    }

    public function store(Request $request, Server $server)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'direction' => 'required|in:inbound,outbound',
            'action' => 'required|in:allow,deny',
            'protocol' => 'required|in:tcp,udp,icmp,all',
            'port' => 'nullable|integer|between:1,65535',
            'source_ip' => 'nullable|ip',
            'destination_ip' => 'nullable|ip',
            'description' => 'nullable|string',
        ]);

        $rule = $server->firewall_rules()->create($validated);

        return response()->json($rule, 201);
    }

    public function show(Server $server, FirewallRule $firewallRule)
    {
        return response()->json($firewallRule);
    }

    public function update(Request $request, Server $server, FirewallRule $firewallRule)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'direction' => 'in:inbound,outbound',
            'action' => 'in:allow,deny',
            'protocol' => 'in:tcp,udp,icmp,all',
            'port' => 'nullable|integer|between:1,65535',
            'is_enabled' => 'boolean',
        ]);

        $firewallRule->update($validated);

        return response()->json($firewallRule);
    }

    public function destroy(Server $server, FirewallRule $firewallRule)
    {
        $firewallRule->delete();

        return response()->json(null, 204);
    }
}
