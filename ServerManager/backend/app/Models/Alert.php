<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'server_id',
        'type',
        'severity',
        'title',
        'message',
        'metric_type',
        'metric_value',
        'threshold',
        'is_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'metric_value' => 'float',
        'threshold' => 'float',
    ];

    const SEVERITY_CRITICAL = 'critical';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_INFO = 'info';

    const TYPE_DISK_FULL = 'disk_full';
    const TYPE_HIGH_CPU = 'high_cpu';
    const TYPE_HIGH_MEMORY = 'high_memory';
    const TYPE_SERVER_OFFLINE = 'server_offline';
    const TYPE_CUSTOM = 'custom';

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function resolve(): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);
    }
}
