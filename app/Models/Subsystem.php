<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subsystem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'building_id',
        'parent_id',
        'name',
        'code',
        'type',
        'description',
        'specifications',
        'installation_date',
        'warranty_expiry',
        'is_active'
    ];

    protected $casts = [
        'specifications' => 'array',
        'installation_date' => 'date',
        'warranty_expiry' => 'date',
        'is_active' => 'boolean'
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Subsystem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Subsystem::class, 'parent_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'subsystem_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'subsystem_id');
    }

    public function getHierarchyPathAttribute(): string
    {
        $path = [];
        $current = $this;
        
        while ($current) {
            $path[] = $current->name;
            $current = $current->parent;
        }
        
        return implode(' → ', array_reverse($path));
    }
}