<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ServerLog;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    protected function ensureAdmin()
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }
    }

    public function users(Request $request)
    {
        $this->ensureAdmin();

        $perPage = (int) $request->get('per_page', 50);
        $users = User::orderBy('id', 'desc')->paginate($perPage);

        return response()->json($users);
    }

    public function logs(Request $request)
    {
        $this->ensureAdmin();

        $perPage = (int) $request->get('per_page', 50);
        $serverId = $request->get('server_id');

        $query = ServerLog::query()->orderBy('created_at', 'desc');
        if ($serverId) {
            $query->where('server_id', $serverId);
        }

        $logs = $query->paginate($perPage);

        return response()->json($logs);
    }
}
