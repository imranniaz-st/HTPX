<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Services\SSHService;
use Illuminate\Http\Request;

class ServerUserController extends Controller
{
    public function __construct(private SSHService $sshService)
    {
    }

    public function index(Server $server)
    {
        try {
            $users = $this->sshService->getServerUsers($server);

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function changePassword(Request $request, Server $server, $username)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        try {
            $this->sshService->changeUserPassword(
                $server,
                $username,
                $validated['password']
            );

            return response()->json([
                'message' => 'Password changed successfully',
                'server' => $server->name,
                'username' => $username,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
