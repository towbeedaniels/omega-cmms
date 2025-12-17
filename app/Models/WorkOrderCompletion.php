<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrderCompletion extends Model
{
    protected $fillable = [
        'work_order_id',
        'completed_by',
        'completed_at',
        'status',
        'actual_hours',
        'labor_cost',
        'material_cost',
        'notes',
        'parts_used',
        'signature_path',
        'attachments',
        'downtime_hours',
        'cost_savings',
        'customer_satisfaction',
        'customer_feedback'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'parts_used' => 'array',
        'attachments' => 'array',
        'actual_hours' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'material_cost' => 'decimal:2',
        'downtime_hours' => 'integer',
        'cost_savings' => 'decimal:2',
        'customer_satisfaction' => 'boolean'
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    public function getTotalCostAttribute(): float
    {
        return ($this->labor_cost ?? 0) + ($this->material_cost ?? 0);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasIssues(): bool
    {
        return $this->status === 'completed_with_issues';
    }
}