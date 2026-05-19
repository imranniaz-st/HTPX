<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Server;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $query = Alert::query();

        if ($request->query('is_resolved') !== null) {
            $query->where('is_resolved', $request->boolean('is_resolved'));
        }

        if ($request->query('severity')) {
            $query->where('severity', $request->query('severity'));
        }

        $alerts = $query->latest()->paginate(20);

        return response()->json($alerts);
    }

    public function show(Alert $alert)
    {
        return response()->json($alert);
    }

    public function resolve(Alert $alert)
    {
        $alert->resolve();

        return response()->json($alert);
    }
}
