<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerMetric extends Model
{
    protected $fillable = [
        'server_id',
        'cpu_usage',
        'memory_usage',
        'memory_total',
        'disk_usage',
        'disk_total',
        'disk_free',
        'network_in',
        'network_out',
        'cpu_count',
        'load_average',
        'process_count',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cpu_usage' => 'float',
        'memory_usage' => 'float',
        'disk_usage' => 'float',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function getDiskPercentageAttribute(): float
    {
        if ($this->disk_total == 0) return 0;
        return round(($this->disk_usage / $this->disk_total) * 100, 2);
    }

    public function getMemoryPercentageAttribute(): float
    {
        if ($this->memory_total == 0) return 0;
        return round(($this->memory_usage / $this->memory_total) * 100, 2);
    }
}
