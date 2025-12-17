<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'facility_id',
        'zone_id',
        'model_id',
        'parent_id',
        'asset_tag',
        'serial_number',
        'name',
        'status',
        'acquisition_date',
        'acquisition_cost',
        'warranty_expiry',
        'installation_date',
        'last_maintenance_date',
        'next_maintenance_date',
        'specifications',
        'location_details',
        'attachments',
        'useful_life_years',
        'current_value',
        'depreciation_rate',
        'is_critical',
        'requires_calibration',
        'last_calibration_date',
        'next_calibration_date',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'specifications' => 'array',
        'location_details' => 'array',
        'attachments' => 'array',
        'acquisition_date' => 'date',
        'warranty_expiry' => 'date',
        'installation_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'last_calibration_date' => 'date',
        'next_calibration_date' => 'date',
        'acquisition_cost' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'is_critical' => 'boolean',
        'requires_calibration' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Model::class, 'model_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Asset::class, 'parent_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function inventoryReferences(): HasMany
    {
        return $this->hasMany(InventoryReference::class);
    }

    public function calculateDepreciation(): float
    {
        if (!$this->acquisition_cost || !$this->acquisition_date || !$this->depreciation_rate) {
            return 0;
        }

        $monthsOwned = now()->diffInMonths($this->acquisition_date);
        $depreciationPerMonth = ($this->acquisition_cost * $this->depreciation_rate) / (12 * 100);
        
        return min($this->acquisition_cost, $depreciationPerMonth * $monthsOwned);
    }

    public function getCurrentValueAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        $depreciation = $this->calculateDepreciation();
        return max(0, ($this->acquisition_cost ?? 0) - $depreciation);
    }

    public function isMaintenanceOverdue(): bool
    {
        if (!$this->next_maintenance_date) {
            return false;
        }
        
        return $this->next_maintenance_date < now();
    }

    public function isCalibrationOverdue(): bool
    {
        if (!$this->requires_calibration || !$this->next_calibration_date) {
            return false;
        }
        
        return $this->next_calibration_date < now();
    }
}