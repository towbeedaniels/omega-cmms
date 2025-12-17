<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubsystemRequest;
use App\Http\Resources\SubsystemResource;
use App\Models\Subsystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubsystemManagementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Subsystem::with([
                'building:id,name,code,facility_id',
                'building.facility:id,name,code',
                'parent:id,name,code',
                'children:id,parent_id,name,code,type',
                'assets:id,subsystem_id,name,asset_tag,status',
                'workOrders:id,subsystem_id,title,status,priority'
            ])->where('is_active', true);

            // Apply filters
            if ($request->has('building_id')) {
                $query->where('building_id', $request->building_id);
            }

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('facility_id')) {
                $query->whereHas('building.facility', function ($q) use ($request) {
                    $q->where('id', $request->facility_id);
                });
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Ordering
            $orderBy = $request->get('order_by', 'name');
            $orderDir = $request->get('order_dir', 'asc');
            $query->orderBy($orderBy, $orderDir);

            $subsystems = $query->paginate($request->get('per_page', 25));

            return response()->json([
                'success' => true,
                'data' => SubsystemResource::collection($subsystems),
                'meta' => [
                    'total' => $subsystems->total(),
                    'per_page' => $subsystems->perPage(),
                    'current_page' => $subsystems->currentPage(),
                    'last_page' => $subsystems->lastPage(),
                    'types' => Subsystem::select('type')->distinct()->pluck('type')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SubsystemManagementController@index Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load subsystems',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(SubsystemRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $subsystem = Subsystem::create($request->validated());

            // Create hierarchy closure if parent is specified
            if ($request->has('parent_id') && $request->parent_id) {
                $this->createHierarchyClosure($subsystem, $request->parent_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subsystem created successfully',
                'data' => new SubsystemResource($subsystem->load(['building', 'parent']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('SubsystemManagementController@store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create subsystem',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(Subsystem $subsystem): JsonResponse
    {
        try {
            $subsystem->load([
                'building.facility',
                'parent',
                'children',
                'assets' => function ($query) {
                    $query->select('id', 'subsystem_id', 'name', 'asset_tag', 'status', 'model_id')
                          ->with('model:id,name,model_number');
                },
                'workOrders' => function ($query) {
                    $query->select('id', 'subsystem_id', 'title', 'status', 'priority', 'scheduled_date')
                          ->orderBy('scheduled_date', 'desc')
                          ->limit(10);
                }
            ]);

            return response()->json([
                'success' => true,
                'data' => new SubsystemResource($subsystem)
            ]);

        } catch (\Exception $e) {
            Log::error('SubsystemManagementController@show Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load subsystem details'
            ], 500);
        }
    }

    public function update(SubsystemRequest $request, Subsystem $subsystem): JsonResponse
    {
        try {
            DB::beginTransaction();

            $subsystem->update($request->validated());

            // Update hierarchy if parent changed
            if ($request->has('parent_id') && $request->parent_id != $subsystem->parent_id) {
                $this->updateHierarchyClosure($subsystem, $request->parent_id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subsystem updated successfully',
                'data' => new SubsystemResource($subsystem->fresh()->load(['building', 'parent']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('SubsystemManagementController@update Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update subsystem'
            ], 500);
        }
    }

    public function destroy(Subsystem $subsystem): JsonResponse
    {
        try {
            // Check if subsystem has children or assets
            if ($subsystem->children()->exists() || $subsystem->assets()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete subsystem with children or assigned assets'
                ], 400);
            }

            DB::beginTransaction();

            $subsystem->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subsystem deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('SubsystemManagementController@destroy Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subsystem'
            ], 500);
        }
    }

    private function createHierarchyClosure(Subsystem $child, $parentId): void
    {
        // Get all ancestors of parent
        $parentAncestors = DB::table('subsystem_hierarchies')
            ->where('descendant_id', $parentId)
            ->get();

        // Insert self relationship
        DB::table('subsystem_hierarchies')->insert([
            'ancestor_id' => $child->id,
            'descendant_id' => $child->id,
            'depth' => 0
        ]);

        // Insert parent relationships
        foreach ($parentAncestors as $ancestor) {
            DB::table('subsystem_hierarchies')->insert([
                'ancestor_id' => $ancestor->ancestor_id,
                'descendant_id' => $child->id,
                'depth' => $ancestor->depth + 1
            ]);
        }
    }

    private function updateHierarchyClosure(Subsystem $child, $newParentId): void
    {
        // Remove old hierarchy relationships
        DB::table('subsystem_hierarchies')
            ->where('descendant_id', $child->id)
            ->where('depth', '>', 0)
            ->delete();

        // Create new hierarchy if parent exists
        if ($newParentId) {
            $this->createHierarchyClosure($child, $newParentId);
        }
    }
}