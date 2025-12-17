<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkOrderCompletionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'work_order_id' => $this->work_order_id,
            'work_order' => $this->whenLoaded('workOrder', function () {
                return [
                    'work_order_number' => $this->workOrder->work_order_number,
                    'title' => $this->workOrder->title,
                    'priority' => $this->workOrder->priority,
                    'status' => $this->workOrder->status,
                    'work_type' => $this->workOrder->work_type,
                    'asset' => $this->workOrder->asset ? [
                        'id' => $this->workOrder->asset->id,
                        'name' => $this->workOrder->asset->name,
                        'asset_tag' => $this->workOrder->asset->asset_tag,
                        'facility_name' => $this->workOrder->asset->facility->name ?? null
                    ] : null,
                    'subsystem' => $this->workOrder->subsystem ? [
                        'id' => $this->workOrder->subsystem->id,
                        'name' => $this->workOrder->subsystem->name,
                        'code' => $this->workOrder->subsystem->code
                    ] : null,
                    'assigned_technician' => $this->workOrder->assignedTechnician ? [
                        'id' => $this->workOrder->assignedTechnician->id,
                        'name' => $this->workOrder->assignedTechnician->name,
                        'email' => $this->workOrder->assignedTechnician->email
                    ] : null,
                    'scheduled_date' => $this->workOrder->scheduled_date,
                    'due_date' => $this->workOrder->due_date,
                    'estimated_hours' => $this->workOrder->estimated_hours,
                    'estimated_cost' => $this->workOrder->estimated_cost
                ];
            }),
            'completed_by' => $this->whenLoaded('completedBy', function () {
                return [
                    'id' => $this->completedBy->id,
                    'name' => $this->completedBy->name,
                    'email' => $this->completedBy->email,
                    'employee_id' => $this->completedBy->employee_id
                ];
            }),
            'completed_at' => $this->completed_at->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'actual_hours' => $this->actual_hours,
            'labor_cost' => $this->labor_cost,
            'material_cost' => $this->material_cost,
            'total_cost' => $this->total_cost,
            'notes' => $this->notes,
            'parts_used' => $this->when($this->parts_used, function () {
                return json_decode($this->parts_used, true);
            }, []),
            'signature_path' => $this->signature_path,
            'attachments' => $this->attachments,
            'downtime_hours' => $this->downtime_hours,
            'cost_savings' => $this->cost_savings,
            'customer_satisfaction' => $this->customer_satisfaction,
            'customer_feedback' => $this->customer_feedback,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}