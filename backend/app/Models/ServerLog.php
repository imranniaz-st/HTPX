<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerLog extends Model
{
    protected $fillable = [
        'server_id',
        'type',
        'level',
        'title',
        'message',
        'command',
        'output',
        'error',
        'status_code',
        'user_id',
        'source',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';

    const TYPE_SYSTEM = 'system';
    const TYPE_SSH = 'ssh';
    const TYPE_FIREWALL = 'firewall';
    const TYPE_ALERT = 'alert';
    const TYPE_PASSWORD_CHANGE = 'password_change';
    const TYPE_REBOOT = 'reboot';
    const TYPE_PACKAGE = 'package';
    const TYPE_CUSTOM = 'custom';

    const SOURCE_SYSTEM = 'system';
    const SOURCE_AGENT = 'agent';
    const SOURCE_USER = 'user';
    const SOURCE_REMOTE = 'remote';

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->nullable();
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('timestamp', '>=', now()->subDays($days));
    }

    public function scopeErrors($query)
    {
        return $query->where('level', self::LEVEL_ERROR);
    }
}
