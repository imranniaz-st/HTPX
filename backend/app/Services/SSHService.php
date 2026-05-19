<?php

namespace App\Services;

use App\Models\Server;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\RSA;
use Exception;

class SSHService
{
    /**
     * Connect to a server via SSH
     */
    public function connect(Server $server): SSH2
    {
        $ssh = new SSH2($server->ip_address, $server->ssh_port ?? 22);

        if ($server->ssh_auth_type === 'key') {
            $key = RSA::load(file_get_contents($server->ssh_key_path));
            if (!$ssh->login($server->ssh_username, $key)) {
                throw new Exception('SSH authentication failed');
            }
        } else {
            // Password auth - get from secure storage
            throw new Exception('Password auth not implemented yet');
        }

        return $ssh;
    }

    /**
     * Get list of users on a server
     */
    public function getServerUsers(Server $server): array
    {
        try {
            $ssh = $this->connect($server);
            $output = $ssh->exec('cut -d: -f1 /etc/passwd');
            
            $users = array_filter(
                array_map('trim', explode("\n", $output)),
                fn($user) => !empty($user)
            );

            return [
                'users' => $users,
                'count' => count($users),
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to fetch users: ' . $e->getMessage());
        }
    }

    /**
     * Change user password on server
     */
    public function changeUserPassword(Server $server, string $username, string $newPassword): bool
    {
        try {
            $ssh = $this->connect($server);

            // Use chpasswd for non-interactive password change
            $command = "echo '{$username}:{$newPassword}' | chpasswd";
            $output = $ssh->exec($command);

            if ($ssh->getExitStatus() === 0) {
                return true;
            }

            throw new Exception('Password change failed: ' . $output);
        } catch (Exception $e) {
            throw new Exception('Failed to change password: ' . $e->getMessage());
        }
    }

    /**
     * Apply firewall rule
     */
    public function applyFirewallRule(Server $server, $rule): bool
    {
        try {
            $ssh = $this->connect($server);

            $command = $this->buildFirewallCommand($rule);
            $output = $ssh->exec($command);

            return $ssh->getExitStatus() === 0;
        } catch (Exception $e) {
            throw new Exception('Failed to apply firewall rule: ' . $e->getMessage());
        }
    }

    /**
     * Build firewall command (UFW or iptables)
     */
    private function buildFirewallCommand($rule): string
    {
        // Check which firewall is available
        $direction = strtoupper($rule->direction);
        $action = strtoupper($rule->action);
        $port = $rule->port ? " {$rule->port}" : '';

        return "sudo ufw {$action} {$direction}{$port}";
    }

    /**
     * Execute custom command on server
     */
    public function execute(Server $server, string $command): string
    {
        try {
            $ssh = $this->connect($server);
            return $ssh->exec($command);
        } catch (Exception $e) {
            throw new Exception('Command execution failed: ' . $e->getMessage());
        }
    }

    /**
     * Test SSH connection
     */
    public function testConnection(Server $server): bool
    {
        try {
            $ssh = $this->connect($server);
            return $ssh->exec('echo "Connection successful"') !== false;
        } catch (Exception $e) {
            throw new Exception('SSH connection test failed: ' . $e->getMessage());
        }
    }
}
