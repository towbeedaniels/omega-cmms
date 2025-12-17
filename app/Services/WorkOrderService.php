<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\WorkOrderCompletion;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkOrderService
{
    public function completeWorkOrder(WorkOrder $workOrder, array $data, int $userId): WorkOrderCompletion
    {
        try {
            $completionData = array_merge($data, [
                'completed_by' => $userId,
                'completed_at' => now(),
                'labor_cost' => $this->calculateLaborCost($workOrder, $data['actual_hours'] ?? 0),
                'material_cost' => $this->calculateMaterialCost($data['parts_used'] ?? [])
            ]);

            $completion = WorkOrderCompletion::create($completionData);

            // Update work order status
            $workOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
                'actual_hours' => $data['actual_hours'] ?? 0,
                'actual_cost' => $completion->total_cost
            ]);

            // Create inventory transactions for used parts
            if (isset($data['parts_used']) && is_array($data['parts_used'])) {
                $this->createInventoryTransactions($data['parts_used'], $workOrder, $completion);
            }

            // Log activity
            activity()
                ->causedBy($userId)
                ->performedOn($workOrder)
                ->withProperties([
                    'completion_id' => $completion->id,
                    'actual_hours' => $data['actual_hours'] ?? 0,
                    'total_cost' => $completion->total_cost
                ])
                ->log('Work order completed');

            return $completion;

        } catch (\Exception $e) {
            Log::error('WorkOrderService::completeWorkOrder Error:', [
                'message' => $e->getMessage(),
                'work_order_id' => $workOrder->id,
                'user_id' => $userId
            ]);
            throw $e;
        }
    }

    private function calculateLaborCost(WorkOrder $workOrder, float $actualHours): float
    {
        // Default labor rate (should be configurable)
        $hourlyRate = 50.00;
        return $actualHours * $hourlyRate;
    }

    private function calculateMaterialCost(array $partsUsed): float
    {
        return collect($partsUsed)->sum(function ($part) {
            return ($part['quantity'] ?? 0) * ($part['unit_cost'] ?? 0);
        });
    }

    private function createInventoryTransactions(array $partsUsed, WorkOrder $workOrder, WorkOrderCompletion $completion): void
    {
        foreach ($partsUsed as $part) {
            InventoryTransaction::create([
                'item_id' => $part['item_id'],
                'transaction_type' => 'consumption',
                'quantity' => -abs($part['quantity']), // Negative for consumption
                'unit_cost' => $part['unit_cost'],
                'total_cost' => -abs($part['quantity'] * $part['unit_cost']),
                'reference_type' => 'work_order_completion',
                'reference_id' => $completion->id,
                'created_by' => $completion->completed_by,
                'remarks' => "Used in work order: {$workOrder->work_order_number}",
                'transaction_date' => now()
            ]);
        }
    }

    public function assignWorkOrder(WorkOrder $workOrder, int $technicianId, int $assignerId): void
    {
        DB::transaction(function () use ($workOrder, $technicianId, $assignerId) {
            $workOrder->update([
                'assigned_to' => $technicianId,
                'assigned_at' => now(),
                'status' => 'assigned'
            ]);

            activity()
                ->causedBy($assignerId)
                ->performedOn($workOrder)
                ->withProperties(['technician_id' => $technicianId])
                ->log('Work order assigned');
        });
    }

    public function scheduleWorkOrder(WorkOrder $workOrder, string $scheduledDate, int $schedulerId): void
    {
        DB::transaction(function () use ($workOrder, $scheduledDate, $schedulerId) {
            $workOrder->update([
                'scheduled_date' => $scheduledDate,
                'status' => 'pending'
            ]);

            activity()
                ->causedBy($schedulerId)
                ->performedOn($workOrder)
                ->withProperties(['scheduled_date' => $scheduledDate])
                ->log('Work order scheduled');
        });
    }

    public function calculateKPIs(WorkOrder $workOrder): array
    {
        $scheduledDate = $workOrder->scheduled_date;
        $completedDate = $workOrder->completed_at;
        $estimatedHours = $workOrder->estimated_hours;
        $actualHours = $workOrder->actual_hours;

        $metrics = [
            'on_time_completion' => null,
            'time_variance' => null,
            'cost_variance' => null,
            'efficiency' => null
        ];

        if ($scheduledDate && $completedDate) {
            $scheduled = \Carbon\Carbon::parse($scheduledDate);
            $completed = \Carbon\Carbon::parse($completedDate);
            $metrics['on_time_completion'] = $completed <= $scheduled->addDays(1);
            $metrics['time_variance'] = $completed->diffInHours($scheduled);
        }

        if ($estimatedHours && $actualHours) {
            $metrics['efficiency'] = ($estimatedHours / $actualHours) * 100;
        }

        if ($workOrder->estimated_cost && $workOrder->actual_cost) {
            $metrics['cost_variance'] = $workOrder->actual_cost - $workOrder->estimated_cost;
        }

        return $metrics;
    }
}