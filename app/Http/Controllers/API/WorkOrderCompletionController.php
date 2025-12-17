<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkOrderCompletionRequest;
use App\Http\Resources\WorkOrderCompletionResource;
use App\Models\WorkOrder;
use App\Models\WorkOrderCompletion;
use App\Services\WorkOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkOrderCompletionController extends Controller
{
    protected $workOrderService;

    public function __construct(WorkOrderService $workOrderService)
    {
        $this->workOrderService = $workOrderService;
        $this->authorizeResource(WorkOrderCompletion::class, 'completion');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = WorkOrderCompletion::with([
                'workOrder:id,work_order_number,title,priority,status,asset_id,assigned_to,scheduled_date',
                'workOrder.asset:id,name,asset_tag',
                'workOrder.assignedTechnician:id,name,email',
                'completedBy:id,name,email',
                'parts.item:id,code,name,unit_of_measurement'
            ]);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('technician_id')) {
                $query->where('completed_by', $request->technician_id);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('completed_at', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            if ($request->has('facility_id')) {
                $query->whereHas('workOrder', function ($q) use ($request) {
                    $q->where('facility_id', $request->facility_id);
                });
            }

            $completions = $query->orderBy('completed_at', 'desc')
                                ->paginate($request->get('per_page', 20));

            return response()->json([
                'success' => true,
                'data' => WorkOrderCompletionResource::collection($completions),
                'meta' => [
                    'current_page' => $completions->currentPage(),
                    'last_page' => $completions->lastPage(),
                    'per_page' => $completions->perPage(),
                    'total' => $completions->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('WorkOrderCompletionController@index Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load work order completions',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(WorkOrderCompletionRequest $request, WorkOrder $workOrder): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Validate work order can be completed
            if (!$workOrder->canBeCompleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Work order cannot be completed in its current state'
                ], 400);
            }

            // Create completion record
            $completion = $this->workOrderService->completeWorkOrder(
                $workOrder,
                $request->validated(),
                auth()->id()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Work order completed successfully',
                'data' => new WorkOrderCompletionResource($completion->load([
                    'workOrder',
                    'completedBy',
                    'parts'
                ]))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('WorkOrderCompletionController@store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'work_order_id' => $workOrder->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete work order',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(WorkOrderCompletion $completion): JsonResponse
    {
        try {
            $completion->load([
                'workOrder.asset.facility',
                'workOrder.assignedTechnician',
                'completedBy',
                'parts.item',
                'workOrder.attachments'
            ]);

            return response()->json([
                'success' => true,
                'data' => new WorkOrderCompletionResource($completion)
            ]);

        } catch (\Exception $e) {
            Log::error('WorkOrderCompletionController@show Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load completion details'
            ], 500);
        }
    }

    public function update(WorkOrderCompletionRequest $request, WorkOrderCompletion $completion): JsonResponse
    {
        try {
            DB::beginTransaction();

            $completion->update($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Completion updated successfully',
                'data' => new WorkOrderCompletionResource($completion->fresh())
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('WorkOrderCompletionController@update Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update completion'
            ], 500);
        }
    }

    public function destroy(WorkOrderCompletion $completion): JsonResponse
    {
        try {
            DB::beginTransaction();

            $completion->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Completion deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('WorkOrderCompletionController@destroy Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete completion'
            ], 500);
        }
    }
}