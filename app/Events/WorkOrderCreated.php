<?php

namespace App\Events;

use App\Models\WorkOrder;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkOrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $workOrder;

    /**
     * Create a new event instance.
     */
    public function __construct(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder;
    }
}