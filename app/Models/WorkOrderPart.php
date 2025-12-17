<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderPart extends Model
{
    protected $fillable = [
        'work_order_completion_id',
        'item_id',
        'quantity',
        'unit_cost',
        'total_cost'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2'
    ];

    public function completion(): BelongsTo
    {
        return $this->belongsTo(WorkOrderCompletion::class, 'work_order_completion_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}