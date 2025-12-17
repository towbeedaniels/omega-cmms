<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\{
    AuthController,
    FacilityManagementController,
    ClusterManagementController,
    RegionManagementController,
    AssetManagementController,
    ZoneManagementController,
    SubsystemManagementController,
    WorkOrderController,
    WorkOrderCompletionController,
    ReportController
};

// API Versioning
Route::prefix('v1')->group(function () {
    
    // Authentication
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('refresh-token', [AuthController::class, 'refreshToken'])->middleware('auth:sanctum');
    
    // Protected Routes
    Route::middleware(['auth:sanctum', 'api.throttle:60,1'])->group(function () {
        
        // Asset Information Modules
        Route::apiResource('facilities', FacilityManagementController::class);
        Route::apiResource('clusters', ClusterManagementController::class);
        Route::apiResource('regions', RegionManagementController::class);
        Route::apiResource('assets', AssetManagementController::class);
        Route::apiResource('zones', ZoneManagementController::class);
        Route::apiResource('subsystems', SubsystemManagementController::class);
        
        // Work Order Management
        Route::apiResource('work-orders', WorkOrderController::class);
        Route::get('work-orders/{work_order}/completions', [WorkOrderCompletionController::class, 'index']);
        Route::post('work-orders/{work_order}/complete', [WorkOrderCompletionController::class, 'store']);
        
        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('scheduled', [ReportController::class, 'scheduledReports']);
            Route::get('user-facility', [ReportController::class, 'userFacilityReport']);
            Route::get('audit', [ReportController::class, 'auditReport']);
            Route::get('usage', [ReportController::class, 'usageReport']);
        });
        
        // Dashboard
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('dashboard/upcoming-maintenance', [DashboardController::class, 'upcomingMaintenance']);
        Route::get('dashboard/critical-assets', [DashboardController::class, 'criticalAssets']);
    });
});