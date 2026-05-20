<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Server extends Model
{
    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'hostname',
        'os_type',
        'status',
        'description',
        'ssh_port',
        'ssh_username',
        'ssh_auth_type',
        'ssh_key_path',
        'last_heartbeat',
    ];

    protected $casts = [
        'last_heartbeat' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function metrics(): HasMany
    {
        return $this->hasMany(ServerMetric::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function firewall_rules(): HasMany
    {
        return $this->hasMany(FirewallRule::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'server_users')
                    ->withPivot('username', 'role');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ServerLog::class);
    }

    public function getStatusAttribute($value)
    {
        $lastHeartbeat = $this->last_heartbeat;
        if (!$lastHeartbeat) {
            return $value ?: 'unknown';
        }
        
        $diffMinutes = now()->diffInMinutes($lastHeartbeat);
        return $diffMinutes > 5 ? 'offline' : 'online';
    }
}
