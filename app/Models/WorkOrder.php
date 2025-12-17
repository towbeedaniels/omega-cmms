<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'work_order_number',
        'asset_id',
        'subsystem_id',
        'facility_id',
        'requested_by',
        'assigned_to',
        'title',
        'description',
        'priority',
        'status',
        'work_type',
        'scheduled_date',
        'due_date',
        'estimated_hours',
        'estimated_cost',
        'safety_instructions',
        'required_tools',
        'required_materials',
        'assigned_at',
        'started_at',
        'completed_at',
        'completion_notes',
        'actual_hours',
        'actual_cost',
        'attachments',
        'requires_approval',
        'is_approved',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'due_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'required_tools' => 'array',
        'required_materials' => 'array',
        'attachments' => 'array',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'requires_approval' => 'boolean',
        'is_approved' => 'boolean'
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function subsystem(): BelongsTo
    {
        return $this->belongsTo(Subsystem::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(WorkOrderCompletion::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['assigned', 'in_progress']);
    }

    public function isOverdue(): bool
    {
        if (!$this->due_date) {
            return false;
        }
        
        return $this->due_date < now() && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function generateWorkOrderNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $count = WorkOrder::whereYear('created_at', $year)
                         ->whereMonth('created_at', $month)
                         ->count() + 1;
        
        return "WO-{$year}{$month}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workOrder) {
            if (!$workOrder->work_order_number) {
                $workOrder->work_order_number = $workOrder->generateWorkOrderNumber();
            }
        });
    }
}