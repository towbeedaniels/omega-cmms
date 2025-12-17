<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    AuthController,
    AssetManagementController,
    FacilityManagementController,
    ClusterManagementController,
    RegionManagementController,
    ZoneManagementController,
    SubsystemManagementController,
    InventoryController,
    WorkOrderController,
    WorkOrderCompletionController,
    ReportController,
    DashboardController
};

// API Versioning
Route::prefix('v1')->group(function () {
    
    // Authentication
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('refresh-token', [AuthController::class, 'refreshToken'])->middleware('auth:sanctum');
    
    // Protected Routes
    Route::middleware(['auth:sanctum', 'api.throttle:60,1'])->group(function () {
        
        // Dashboard
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('dashboard/upcoming-maintenance', [DashboardController::class, 'upcomingMaintenance']);
        Route::get('dashboard/critical-assets', [DashboardController::class, 'criticalAssets']);
        Route::get('dashboard/pending-work-orders', [DashboardController::class, 'pendingWorkOrders']);
        
        // Asset Information Modules
        Route::apiResource('facilities', FacilityManagementController::class);
        Route::apiResource('clusters', ClusterManagementController::class);
        Route::apiResource('regions', RegionManagementController::class);
        Route::apiResource('assets', AssetManagementController::class);
        Route::apiResource('zones', ZoneManagementController::class);
        Route::apiResource('subsystems', SubsystemManagementController::class);
        
        // Work Order Management
        Route::apiResource('work-orders', WorkOrderController::class);
        Route::post('work-orders/{work_order}/assign', [WorkOrderController::class, 'assign']);
        Route::post('work-orders/{work_order}/schedule', [WorkOrderController::class, 'schedule']);
        Route::post('work-orders/{work_order}/start', [WorkOrderController::class, 'start']);
        
        // Work Order Completions
        Route::apiResource('work-orders.completions', WorkOrderCompletionController::class)
            ->except(['index', 'store'])
            ->shallow();
        Route::get('work-order-completions', [WorkOrderCompletionController::class, 'index']);
        Route::post('work-orders/{work_order}/complete', [WorkOrderCompletionController::class, 'store']);
        
        // Inventory Management
        Route::apiResource('inventory/references', InventoryController::class);
        Route::get('inventory/transactions', [InventoryController::class, 'transactions']);
        Route::get('inventory/low-stock', [InventoryController::class, 'lowStock']);
        Route::post('inventory/adjust', [InventoryController::class, 'adjust']);
        
        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('scheduled', [ReportController::class, 'scheduledReports']);
            Route::get('user-facility', [ReportController::class, 'userFacilityReport']);
            Route::get('audit', [ReportController::class, 'auditReport']);
            Route::get('usage', [ReportController::class, 'usageReport']);
            Route::get('export', [ReportController::class, 'exportReport']);
            Route::get('download/{filename}', [ReportController::class, 'downloadReport']);
        });
        
        // User Management
        Route::apiResource('users', UserController::class);
        Route::put('users/{user}/permissions', [UserController::class, 'updatePermissions']);
        Route::put('users/{user}/status', [UserController::class, 'updateStatus']);
    });
    
    // Public endpoints (if any)
    Route::get('health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'version' => '1.0.0'
        ]);
    });
});