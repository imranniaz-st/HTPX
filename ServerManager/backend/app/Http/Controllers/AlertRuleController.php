<?php

namespace App\Http\Controllers;

use App\Models\AlertRule;
use Illuminate\Http\Request;

class AlertRuleController extends Controller
{
    public function index()
    {
        $rules = AlertRule::paginate(20);

        return response()->json($rules);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'name' => 'required|string|max:255',
            'metric_type' => 'required|string',
            'operator' => 'required|in:>,<,=,!=',
            'threshold' => 'required|numeric',
            'duration_minutes' => 'integer|min:1',
            'severity' => 'required|in:critical,warning,info',
            'notify_email' => 'nullable|email',
            'notify_webhook_url' => 'nullable|url',
        ]);

        $rule = AlertRule::create($validated);

        return response()->json($rule, 201);
    }

    public function show(AlertRule $alertRule)
    {
        return response()->json($alertRule);
    }

    public function update(Request $request, AlertRule $alertRule)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'threshold' => 'numeric',
            'severity' => 'in:critical,warning,info',
            'is_enabled' => 'boolean',
        ]);

        $alertRule->update($validated);

        return response()->json($alertRule);
    }

    public function destroy(AlertRule $alertRule)
    {
        $alertRule->delete();

        return response()->json(null, 204);
    }
}
