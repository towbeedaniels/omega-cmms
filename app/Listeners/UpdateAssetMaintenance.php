<?php

namespace App\Listeners;

use App\Events\WorkOrderCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateAssetMaintenance implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(WorkOrderCompleted $event): void
    {
        $workOrder = $event->workOrder;
        
        if ($workOrder->asset_id) {
            $asset = $workOrder->asset;
            
            // Update last maintenance date
            $asset->update([
                'last_maintenance_date' => now(),
                'next_maintenance_date' => $this->calculateNextMaintenanceDate($asset),
                'status' => 'operational'
            ]);
        }
    }

    /**
     * Calculate next maintenance date based on maintenance interval.
     */
    private function calculateNextMaintenanceDate($asset): ?\Carbon\Carbon
    {
        if (!$asset->model || !$asset->model->maintenance_interval_days) {
            return null;
        }

        return now()->addDays($asset->model->maintenance_interval_days);
    }
}