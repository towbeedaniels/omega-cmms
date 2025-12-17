<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\ScheduledReport;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function scheduledReports(Request $request): JsonResponse
    {
        try {
            $query = ScheduledReport::query();

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('schedule_type')) {
                $query->where('schedule_type', $request->schedule_type);
            }

            $reports = $query->orderBy('next_run_date')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => ReportResource::collection($reports),
                'summary' => [
                    'total' => ScheduledReport::count(),
                    'active' => ScheduledReport::where('is_active', true)->count(),
                    'next_due' => ScheduledReport::where('is_active', true)
                        ->where('next_run_date', '>', now())
                        ->orderBy('next_run_date')
                        ->first()
                        ->next_run_date ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('ReportController@scheduledReports Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load scheduled reports'
            ], 500);
        }
    }

    public function userFacilityReport(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->startOfMonth());
            $endDate = $request->get('end_date', now()->endOfMonth());

            $report = $this->reportService->generateUserFacilityReport($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $report,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate
                ],
                'filters' => $request->only(['role_id', 'facility_id'])
            ]);

        } catch (\Exception $e) {
            Log::error('ReportController@userFacilityReport Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate user facility report'
            ], 500);
        }
    }

    public function auditReport(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subDays(30));
            $endDate = $request->get('end_date', now());
            $actionType = $request->get('action_type');
            $userId = $request->get('user_id');

            $report = $this->reportService->generateAuditReport($startDate, $endDate, $actionType, $userId);

            return response()->json([
                'success' => true,
                'data' => $report,
                'summary' => [
                    'total_actions' => count($report['activities']),
                    'unique_users' => count(array_unique(array_column($report['activities'], 'causer_name'))),
                    'most_active_user' => $report['summary']['most_active_user'] ?? null,
                    'most_common_action' => $report['summary']['most_common_action'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('ReportController@auditReport Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate audit report'
            ], 500);
        }
    }

    public function usageReport(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->startOfMonth());
            $endDate = $request->get('end_date', now()->endOfMonth());
            $facilityId = $request->get('facility_id');

            $report = $this->reportService->generateUsageReport($startDate, $endDate, $facilityId);

            return response()->json([
                'success' => true,
                'data' => $report,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate
                ],
                'trends' => $this->reportService->getUsageTrends($startDate, $endDate)
            ]);

        } catch (\Exception $e) {
            Log::error('ReportController@usageReport Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate usage report'
            ], 500);
        }
    }

    public function exportReport(Request $request): JsonResponse
    {
        try {
            $reportType = $request->get('report_type');
            $format = $request->get('format', 'csv');

            $data = match($reportType) {
                'user_facility' => $this->userFacilityReport($request)->getData()->data,
                'audit' => $this->auditReport($request)->getData()->data,
                'usage' => $this->usageReport($request)->getData()->data,
                default => null
            };

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid report type'
                ], 400);
            }

            $filename = "report_{$reportType}_" . now()->format('Y-m-d_H-i-s') . ".{$format}";
            $exportData = $this->reportService->exportReport($data, $format);

            return response()->json([
                'success' => true,
                'filename' => $filename,
                'data' => $exportData,
                'download_url' => route('api.reports.download', ['filename' => $filename])
            ]);

        } catch (\Exception $e) {
            Log::error('ReportController@exportReport Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export report'
            ], 500);
        }
    }
}