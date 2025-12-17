<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryReference extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'supplier_id',
        'asset_id',
        'reference_code',
        'manufacturer_part_number',
        'supplier_part_number',
        'minimum_order_quantity',
        'supplier_price',
        'supplier_currency',
        'delivery_lead_time',
        'delivery_terms',
        'compatibility_info',
        'substitute_items',
        'notes',
        'is_preferred',
        'is_active'
    ];

    protected $casts = [
        'compatibility_info' => 'array',
        'substitute_items' => 'array',
        'supplier_price' => 'decimal:2',
        'is_preferred' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'supplier_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'reference_id')
                    ->where('reference_type', 'inventory_reference');
    }

    public function getStockStatusAttribute(): string
    {
        if (!$this->item) {
            return 'unknown';
        }

        $currentStock = $this->item->current_stock ?? 0;
        $reorderPoint = $this->item->reorder_point ?? 0;
        
        if ($currentStock <= 0) return 'out_of_stock';
        if ($currentStock <= $reorderPoint) return 'low_stock';
        if ($currentStock >= ($this->item->maximum_stock ?? $currentStock * 1.5)) return 'over_stock';
        
        return 'adequate';
    }
}