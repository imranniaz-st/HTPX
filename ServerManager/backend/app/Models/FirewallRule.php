<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FirewallRule extends Model
{
    protected $fillable = [
        'server_id',
        'name',
        'direction',
        'action',
        'protocol',
        'port',
        'source_ip',
        'destination_ip',
        'description',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const DIRECTION_INBOUND = 'inbound';
    const DIRECTION_OUTBOUND = 'outbound';

    const ACTION_ALLOW = 'allow';
    const ACTION_DENY = 'deny';

    const PROTOCOL_TCP = 'tcp';
    const PROTOCOL_UDP = 'udp';
    const PROTOCOL_ICMP = 'icmp';
    const PROTOCOL_ALL = 'all';

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function toggle(): void
    {
        $this->update(['is_enabled' => !$this->is_enabled]);
    }
}
