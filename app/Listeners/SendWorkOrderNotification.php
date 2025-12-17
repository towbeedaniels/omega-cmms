<?php

namespace App\Listeners;

use App\Events\WorkOrderCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewWorkOrderNotification;

class SendWorkOrderNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(WorkOrderCreated $event): void
    {
        $workOrder = $event->workOrder;
        
        // Get technicians who can be assigned
        $technicians = User::where('role_id', 3) // Technician role
            ->where('is_active', true)
            ->whereJsonContains('facility_scope', $workOrder->facility_id)
            ->get();

        // Send notification to technicians
        Notification::send($technicians, new NewWorkOrderNotification($workOrder));

        // Send notification to manager if work order is critical
        if ($workOrder->priority === 'critical') {
            $managers = User::where('role_id', 2)
                ->where('is_active', true)
                ->get();
            
            Notification::send($managers, new NewWorkOrderNotification($workOrder));
        }
    }
}