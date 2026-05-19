<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertRule extends Model
{
    protected $fillable = [
        'server_id',
        'name',
        'metric_type',
        'operator',
        'threshold',
        'duration_minutes',
        'severity',
        'is_enabled',
        'notify_email',
        'notify_webhook_url',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'threshold' => 'float',
        'duration_minutes' => 'integer',
    ];

    const OPERATOR_GREATER_THAN = '>';
    const OPERATOR_LESS_THAN = '<';
    const OPERATOR_EQUALS = '=';
    const OPERATOR_NOT_EQUALS = '!=';

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
