<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cluster_id',
        'region_id',
        'name',
        'code',
        'type',
        'color_code',
        'address',
        'latitude',
        'longitude',
        'specifications',
        'is_active'
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function activeAssets(): HasMany
    {
        return $this->assets()->where('is_active', true);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->type) {
            'building' => $this->color_code ?? '#3B82F6',
            'outdoor' => $this->color_code ?? '#10B981',
            'infrastructure' => $this->color_code ?? '#F59E0B',
            'utility' => $this->color_code ?? '#8B5CF6',
            default => '#6B7280'
        };
    }
}