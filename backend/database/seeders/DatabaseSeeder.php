<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Server;
use App\Models\Tag;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_VIEWER] as $roleName) {
            Role::findOrCreate($roleName, 'sanctum');
        }

        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@servermanager.local',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@servermanager.local',
                'password' => Hash::make('manager123'),
                'role' => User::ROLE_MANAGER,
            ],
            [
                'name' => 'Viewer',
                'email' => 'viewer@servermanager.local',
                'password' => Hash::make('viewer123'),
                'role' => User::ROLE_VIEWER,
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData + ['is_active' => true]
            );

            $user->syncRoles([$roleName]);
        }

        // Create tags
        $tags = [
            ['name' => 'Production', 'color' => '#ef4444'],
            ['name' => 'Staging', 'color' => '#f59e0b'],
            ['name' => 'Development', 'color' => '#3b82f6'],
            ['name' => 'Database', 'color' => '#8b5cf6'],
            ['name' => 'Web Server', 'color' => '#10b981'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }

        // Create sample servers
        $servers = [
            [
                'name' => 'Web Server 1',
                'ip_address' => '192.168.1.10',
                'hostname' => 'web-01.local',
                'os_type' => 'linux',
                'ssh_port' => 22,
                'ssh_username' => 'ubuntu',
                'description' => 'Primary web server',
            ],
            [
                'name' => 'Database Server',
                'ip_address' => '192.168.1.20',
                'hostname' => 'db-01.local',
                'os_type' => 'linux',
                'ssh_port' => 22,
                'ssh_username' => 'ubuntu',
                'description' => 'MySQL database server',
            ],
            [
                'name' => 'API Server',
                'ip_address' => '192.168.1.30',
                'hostname' => 'api-01.local',
                'os_type' => 'linux',
                'ssh_port' => 22,
                'ssh_username' => 'ubuntu',
                'description' => 'REST API server',
            ],
        ];

        foreach ($servers as $serverData) {
            Server::create($serverData);
        }
    }
}
