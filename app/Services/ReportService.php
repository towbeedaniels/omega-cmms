<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\ScheduledReport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    public function generateUserFacilityReport($startDate, $endDate, $filters = [])
    {
        $query = User::with(['role', 'facilities'])
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.role_id',
                'users.facility_scope',
                'users.access_level',
                'users.last_login_at',
                'users.created_at',
                DB::raw('COUNT(DISTINCT work_orders.id) as total_work_orders'),
                DB::raw('COUNT(DISTINCT CASE WHEN work_orders.status = "completed" THEN work_orders.id END) as completed_work_orders'),
                DB::raw('SUM(CASE WHEN work_orders.status = "completed" THEN work_orders.actual_hours ELSE 0 END) as total_hours_worked')
            ])
            ->leftJoin('work_orders', function ($join) use ($startDate, $endDate) {
                $join->on('work_orders.assigned_to', '=', 'users.id')
                     ->whereBetween('work_orders.created_at', [$startDate, $endDate]);
            });

        // Apply filters
        if (isset($filters['role_id'])) {
            $query->where('users.role_id', $filters['role_id']);
        }

        if (isset($filters['facility_id'])) {
            $query->whereJsonContains('users.facility_scope', (int)$filters['facility_id']);
        }

        $results = $query->groupBy('users.id')
            ->orderBy('users.name')
            ->get();

        $summary = [
            'total_users' => $results->count(),
            'active_users' => $results->whereNotNull('last_login_at')->count(),
            'avg_work_orders_per_user' => round($results->avg('total_work_orders'), 2),
            'total_hours_worked' => $results->sum('total_hours_worked'),
            'completion_rate' => $results->sum('completed_work_orders') / max(1, $results->sum('total_work_orders')) * 100
        ];

        return [
            'users' => $results,
            'summary' => $summary,
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ];
    }

    public function generateAuditReport($startDate, $endDate, $actionType = null, $userId = null)
    {
        $query = DB::table('activity_log')
            ->select([
                'activity_log.*',
                'users.name as causer_name',
                'users.email as causer_email'
            ])
            ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
            ->whereBetween('activity_log.created_at', [$startDate, $endDate])
            ->orderBy('activity_log.created_at', 'desc');

        if ($actionType) {
            $query->where('activity_log.description', 'like', "%{$actionType}%");
        }

        if ($userId) {
            $query->where('activity_log.causer_id', $userId);
        }

        $activities = $query->paginate(50);

        $summary = [
            'total_activities' => $activities->total(),
            'unique_users' => DB::table('activity_log')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->distinct('causer_id')
                ->count('causer_id'),
            'most_common_action' => DB::table('activity_log')
                ->select('description', DB::raw('COUNT(*) as count'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('description')
                ->orderBy('count', 'desc')
                ->first(),
            'peak_hour' => $this->calculatePeakActivityHour($startDate, $endDate)
        ];

        return [
            'activities' => $activities->items(),
            'pagination' => [
                'total' => $activities->total(),
                'per_page' => $activities->perPage(),
                'current_page' => $activities->currentPage()
            ],
            'summary' => $summary
        ];
    }

    public function generateUsageReport($startDate, $endDate, $facilityId = null)
    {
        $workOrderStats = $this->getWorkOrderStats($startDate, $endDate, $facilityId);
        $userStats = $this->getUserStats($startDate, $endDate, $facilityId);
        $assetStats = $this->getAssetStats($startDate, $endDate, $facilityId);
        $inventoryStats = $this->getInventoryStats($startDate, $endDate, $facilityId);

        return [
            'work_orders' => $workOrderStats,
            'users' => $userStats,
            'assets' => $assetStats,
            'inventory' => $inventoryStats,
            'summary' => [
                'total_work_orders' => $workOrderStats['total'],
                'total_users_active' => $userStats['active_users'],
                'total_assets_maintained' => $assetStats['total_maintained'],
                'total_inventory_movement' => $inventoryStats['total_transactions'],
                'overall_efficiency' => $workOrderStats['completion_rate']
            ]
        ];
    }

    private function getWorkOrderStats($startDate, $endDate, $facilityId = null)
    {
        $query = WorkOrder::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($facilityId) {
            $query->where('facility_id', $facilityId);
        }

        $total = $query->count();
        $completed = $query->where('status', 'completed')->count();
        $inProgress = $query->where('status', 'in_progress')->count();
        $cancelled = $query->where('status', 'cancelled')->count();

        $avgCompletionTime = WorkOrder::where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, completed_at)'));

        $priorityBreakdown = WorkOrder::whereBetween('created_at', [$startDate, $endDate])
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->groupBy('priority')
            ->select('priority', DB::raw('COUNT(*) as count'))
            ->get()
            ->pluck('count', 'priority');

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'cancelled' => $cancelled,
            'completion_rate' => $total > 0 ? ($completed / $total * 100) : 0,
            'avg_completion_time_hours' => round($avgCompletionTime, 2),
            'priority_breakdown' => $priorityBreakdown,
            'daily_trend' => $this->getDailyWorkOrderTrend($startDate, $endDate, $facilityId)
        ];
    }

    private function getUserStats($startDate, $endDate, $facilityId = null)
    {
        $activeUsers = User::whereBetween('last_login_at', [$startDate, $endDate])
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->whereJsonContains('facility_scope', $facilityId);
            })
            ->count();

        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->whereJsonContains('facility_scope', $facilityId);
            })
            ->count();

        $avgSessionTime = User::whereBetween('last_login_at', [$startDate, $endDate])
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->whereJsonContains('facility_scope', $facilityId);
            })
            ->avg('active_session_time');

        return [
            'active_users' => $activeUsers,
            'new_users' => $newUsers,
            'avg_session_time_minutes' => round($avgSessionTime, 2),
            'role_distribution' => $this->getRoleDistribution($facilityId)
        ];
    }

    private function getAssetStats($startDate, $endDate, $facilityId = null)
    {
        $query = Asset::query()
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            });

        $total = $query->count();
        $underMaintenance = $query->where('status', 'under_maintenance')->count();
        $outOfService = $query->where('status', 'out_of_service')->count();
        $critical = $query->where('is_critical', true)->count();

        $maintenanceDue = Asset::where('next_maintenance_date', '<=', $endDate)
            ->where('next_maintenance_date', '>=', $startDate)
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->count();

        $calibrationDue = Asset::where('requires_calibration', true)
            ->where('next_calibration_date', '<=', $endDate)
            ->where('next_calibration_date', '>=', $startDate)
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->count();

        return [
            'total' => $total,
            'under_maintenance' => $underMaintenance,
            'out_of_service' => $outOfService,
            'critical' => $critical,
            'maintenance_due' => $maintenanceDue,
            'calibration_due' => $calibrationDue,
            'uptime_percentage' => $this->calculateAssetUptime($startDate, $endDate, $facilityId)
        ];
    }

    private function getInventoryStats($startDate, $endDate, $facilityId = null)
    {
        $transactions = DB::table('inventory_transactions')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->count();

        $totalMovement = DB::table('inventory_transactions')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->sum('quantity');

        $lowStockItems = DB::table('items')
            ->select(DB::raw('COUNT(*) as count'))
            ->whereRaw('current_stock <= reorder_point')
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->first();

        return [
            'total_transactions' => $transactions,
            'total_movement' => abs($totalMovement),
            'low_stock_items' => $lowStockItems->count ?? 0,
            'transaction_types' => $this->getTransactionTypeBreakdown($startDate, $endDate, $facilityId)
        ];
    }

    private function calculatePeakActivityHour($startDate, $endDate)
    {
        return DB::table('activity_log')
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('count', 'desc')
            ->first();
    }

    private function getDailyWorkOrderTrend($startDate, $endDate, $facilityId = null)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $trend = [];
        for ($date = $start; $date <= $end; $date->addDay()) {
            $count = WorkOrder::whereDate('created_at', $date)
                ->when($facilityId, function ($q) use ($facilityId) {
                    return $q->where('facility_id', $facilityId);
                })
                ->count();
            
            $trend[$date->format('Y-m-d')] = $count;
        }
        
        return $trend;
    }

    private function getRoleDistribution($facilityId = null)
    {
        return User::when($facilityId, function ($q) use ($facilityId) {
                return $q->whereJsonContains('facility_scope', $facilityId);
            })
            ->groupBy('role_id')
            ->select('role_id', DB::raw('COUNT(*) as count'))
            ->with('role:id,name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->role->name => $item->count];
            });
    }

    private function calculateAssetUptime($startDate, $endDate, $facilityId = null)
    {
        $totalAssets = Asset::when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->count();

        if ($totalAssets === 0) {
            return 100;
        }

        $downAssets = WorkOrder::whereBetween('created_at', [$startDate, $endDate])
            ->where('work_type', 'corrective')
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->distinct('asset_id')
            ->count('asset_id');

        return max(0, 100 - ($downAssets / $totalAssets * 100));
    }

    private function getTransactionTypeBreakdown($startDate, $endDate, $facilityId = null)
    {
        return DB::table('inventory_transactions')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->when($facilityId, function ($q) use ($facilityId) {
                return $q->where('facility_id', $facilityId);
            })
            ->groupBy('transaction_type')
            ->select('transaction_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->transaction_type => [
                    'count' => $item->count,
                    'total_quantity' => abs($item->total_quantity)
                ]];
            });
    }
}