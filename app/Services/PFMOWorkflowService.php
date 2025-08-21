<?php

namespace App\Services;

use App\Models\Department;
use App\Models\FormRequest;
use App\Models\FormApproval;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PFMOWorkflowService
{
    /**
     * PFMO workflow statuses for enhanced process tracking
     */
    const STATUS_PENDING_PFMO_APPROVAL = 'Pending PFMO Approval';
    const STATUS_UNDER_EVALUATION = 'Under Sub-Department Evaluation';
    const STATUS_AWAITING_PFMO_DECISION = 'Awaiting PFMO Decision';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';
    
    /**
     * Get PFMO dashboard data
     * 
     * @return array
     */
    public static function getPFMODashboard(): array
    {
        $pfmoDepartment = Department::where('dept_code', 'PFMO')->first();
        
        if (!$pfmoDepartment) {
            return ['error' => 'PFMO department not found'];
        }

        $today = now();
        $lastMonth = now()->subMonth();
        
        // Basic statistics
        $stats = [
            'total_requests' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)->count(),
            'pending_requests' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)
                ->whereIn('status', ['Pending', 'In Progress', 'Pending Target Department Approval'])
                ->count(),
            'approved_today' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)
                ->where('status', 'Approved')
                ->whereDate('updated_at', $today)
                ->count(),
            'under_evaluation' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)
                ->where('status', self::STATUS_UNDER_EVALUATION)
                ->count(),
        ];

        // Recent activity
        $recentRequests = FormRequest::with(['requester.employeeInfo', 'iomDetails'])
            ->where('to_department_id', $pfmoDepartment->department_id)
            ->orderBy('date_submitted', 'desc')
            ->limit(10)
            ->get();

        // Category breakdown
        $categoryBreakdown = self::getCategoryBreakdown($pfmoDepartment->department_id);
        
        // Performance metrics
        $performanceMetrics = self::getPerformanceMetrics($pfmoDepartment->department_id);

        return [
            'stats' => $stats,
            'recent_requests' => $recentRequests,
            'category_breakdown' => $categoryBreakdown,
            'performance_metrics' => $performanceMetrics,
            'last_updated' => now()->toISOString()
        ];
    }

    /**
     * Get PFMO recommendations for process improvements
     * 
     * @return array
     */
    public static function getPFMORecommendations(): array
    {
        $pfmoDepartment = Department::where('dept_code', 'PFMO')->first();
        
        if (!$pfmoDepartment) {
            return [];
        }

        $recommendations = [];
        
        // Check for overdue requests
        $overdueRequests = FormRequest::where('to_department_id', $pfmoDepartment->department_id)
            ->whereIn('status', ['Pending', 'In Progress'])
            ->where('date_submitted', '<', now()->subDays(7))
            ->count();
            
        if ($overdueRequests > 0) {
            $recommendations[] = [
                'type' => 'urgent',
                'title' => 'Overdue Requests Detected',
                'message' => "You have {$overdueRequests} requests pending for more than 7 days.",
                'action' => 'Review overdue requests',
                'priority' => 'high'
            ];
        }

        // Check for rush requests
        $rushRequests = FormRequest::where('to_department_id', $pfmoDepartment->department_id)
            ->whereHas('iomDetails', function($q) {
                $q->where('priority', 'Rush');
            })
            ->whereIn('status', ['Pending', 'In Progress'])
            ->count();
            
        if ($rushRequests > 0) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Rush Requests Pending',
                'message' => "You have {$rushRequests} rush priority requests requiring immediate attention.",
                'action' => 'Review rush requests',
                'priority' => 'high'
            ];
        }

        // Performance recommendations
        $avgProcessingTime = self::getAverageProcessingTime($pfmoDepartment->department_id);
        if ($avgProcessingTime > 72) { // More than 3 days
            $recommendations[] = [
                'type' => 'warning',
                'title' => 'Processing Time Alert',
                'message' => "Average processing time is {$avgProcessingTime} hours. Consider workflow optimization.",
                'action' => 'Review workflow efficiency',
                'priority' => 'medium'
            ];
        }

        return $recommendations;
    }

    /**
     * Categorize PFMO request based on content
     * 
     * @param string $description
     * @param string $title
     * @return array
     */
    public static function categorizePFMORequest(string $description, string $title = ''): array
    {
        // Use the RequestTypeService for categorization
        $autoAssignment = RequestTypeService::autoAssignDepartment($title, $description);
        
        if ($autoAssignment && $autoAssignment['department']->dept_code === 'PFMO') {
            $subDepartment = RequestTypeService::getPFMOSubDepartmentAssignment($title, $description);
            
            return [
                'category' => $autoAssignment['category'],
                'confidence' => $autoAssignment['confidence_score'],
                'sub_department' => $subDepartment,
                'suggested_priority' => self::suggestPriority($description, $title),
                'estimated_completion_days' => self::estimateCompletionTime($autoAssignment['category'])
            ];
        }
        
        return [
            'category' => 'general_maintenance',
            'confidence' => 0,
            'sub_department' => RequestTypeService::getPFMOSubDepartmentAssignment($title, $description),
            'suggested_priority' => self::suggestPriority($description, $title),
            'estimated_completion_days' => 3
        ];
    }

    /**
     * Process PFMO enhanced workflow
     * 
     * @param FormRequest $request
     * @param string $action
     * @param array $options
     * @return bool
     */
    public static function processEnhancedWorkflow(FormRequest $request, string $action, array $options = []): bool
    {
        try {
            DB::beginTransaction();
            
            $result = false;
            
            switch ($action) {
                case 'evaluate':
                    $result = self::processEvaluateAction($request, $options);
                    break;
                    
                case 'assign_sub_department':
                    $result = self::assignToSubDepartment($request, $options);
                    break;
                    
                case 'sub_department_feedback':
                    $result = self::processSubDepartmentFeedback($request, $options);
                    break;
                    
                case 'final_decision':
                    $result = self::processFinalDecision($request, $options);
                    break;
                    
                case 'create_job_order':
                    $result = self::createJobOrder($request, $options);
                    break;
                    
                default:
                    throw new \InvalidArgumentException("Unknown action: {$action}");
            }
            
            DB::commit();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PFMO Enhanced Workflow Error: ' . $e->getMessage(), [
                'request_id' => $request->form_id,
                'action' => $action,
                'options' => $options
            ]);
            return false;
        }
    }

    /**
     * Process evaluate action - PFMO Head decides to evaluate or reject
     */
    private static function processEvaluateAction(FormRequest $request, array $options): bool
    {
        $user = auth()->user();
        
        // Create approval record
        FormApproval::create([
            'form_id' => $request->form_id,
            'approver_id' => $user->accnt_id,
            'action' => 'Evaluate',
            'action_date' => now(),
            'remarks' => $options['remarks'] ?? 'Request approved for evaluation',
            'approval_level' => 'PFMO_HEAD'
        ]);
        
        // Update request status
        $request->status = self::STATUS_UNDER_EVALUATION;
        $request->save();
        
        // Auto-assign to appropriate sub-department if specified
        if (isset($options['sub_department'])) {
            return self::assignToSubDepartment($request, $options);
        }
        
        return true;
    }

    /**
     * Assign request to sub-department
     */
    private static function assignToSubDepartment(FormRequest $request, array $options): bool
    {
        $subDeptInfo = $options['sub_department'] ?? 
                      RequestTypeService::getPFMOSubDepartmentAssignment(
                          $request->title, 
                          $request->iomDetails->body ?? ''
                      );
        
        // Create assignment record
        FormApproval::create([
            'form_id' => $request->form_id,
            'approver_id' => auth()->user()->accnt_id,
            'action' => 'Assigned',
            'action_date' => now(),
            'remarks' => "Assigned to {$subDeptInfo['name']} for evaluation",
            'approval_level' => 'SUB_DEPARTMENT_ASSIGNMENT',
            'sub_department' => $subDeptInfo['sub_department'] ?? null
        ]);
        
        return true;
    }

    /**
     * Process sub-department feedback
     */
    private static function processSubDepartmentFeedback(FormRequest $request, array $options): bool
    {
        FormApproval::create([
            'form_id' => $request->form_id,
            'approver_id' => auth()->user()->accnt_id,
            'action' => 'Feedback',
            'action_date' => now(),
            'remarks' => $options['feedback'] ?? '',
            'approval_level' => 'SUB_DEPARTMENT_FEEDBACK',
            'estimated_completion_date' => isset($options['estimated_completion']) ? 
                                         \Carbon\Carbon::parse($options['estimated_completion'])->format('Y-m-d') : null,
            'estimated_cost' => $options['estimated_cost'] ?? null
        ]);
        
        // Update status to awaiting PFMO decision
        $request->status = self::STATUS_AWAITING_PFMO_DECISION;
        $request->save();
        
        return true;
    }

    /**
     * Process final PFMO decision
     */
    private static function processFinalDecision(FormRequest $request, array $options): bool
    {
        $action = $options['decision'] ?? 'Approved';
        
        FormApproval::create([
            'form_id' => $request->form_id,
            'approver_id' => auth()->user()->accnt_id,
            'action' => $action,
            'action_date' => now(),
            'remarks' => $options['remarks'] ?? '',
            'approval_level' => 'PFMO_FINAL_DECISION'
        ]);
        
        $request->status = $action;
        $request->save();
        
        // Auto-create job order if approved
        if ($action === 'Approved' && ($options['create_job_order'] ?? true)) {
            return self::createJobOrder($request, $options);
        }
        
        return true;
    }

    /**
     * Create job order automatically
     */
    private static function createJobOrder(FormRequest $request, array $options): bool
    {
        // This would integrate with your job order system
        // For now, we'll create a record indicating job order creation
        
        FormApproval::create([
            'form_id' => $request->form_id,
            'approver_id' => auth()->user()->accnt_id,
            'action' => 'Job Order Created',
            'action_date' => now(),
            'remarks' => 'Job order automatically created upon approval',
            'approval_level' => 'JOB_ORDER_CREATION',
            'job_order_number' => 'JO-' . now()->format('Ymd') . '-' . str_pad($request->form_id, 4, '0', STR_PAD_LEFT)
        ]);
        
        return true;
    }

    /**
     * Get category breakdown for dashboard
     */
    private static function getCategoryBreakdown(int $departmentId): array
    {
        $requests = FormRequest::where('to_department_id', $departmentId)
            ->with('iomDetails')
            ->get();
            
        $categories = [];
        
        foreach ($requests as $request) {
            $categorization = self::categorizePFMORequest(
                $request->iomDetails->body ?? '',
                $request->title
            );
            
            $category = $categorization['category'];
            $categories[$category] = ($categories[$category] ?? 0) + 1;
        }
        
        return $categories;
    }

    /**
     * Get performance metrics
     */
    private static function getPerformanceMetrics(int $departmentId): array
    {
        $requests = FormRequest::where('to_department_id', $departmentId)
            ->where('status', 'Approved')
            ->with('approvals')
            ->limit(100)
            ->get();
            
        $totalTime = 0;
        $count = 0;
        
        foreach ($requests as $request) {
            $firstApproval = $request->approvals->where('action', 'Evaluate')->first();
            $finalApproval = $request->approvals->where('action', 'Approved')->first();
            
            if ($firstApproval && $finalApproval) {
                $time = $firstApproval->action_date->diffInHours($finalApproval->action_date);
                $totalTime += $time;
                $count++;
            }
        }
        
        return [
            'average_processing_time_hours' => $count > 0 ? round($totalTime / $count, 1) : 0,
            'total_processed' => $count,
            'efficiency_rating' => $count > 0 ? min(100, max(0, 100 - ($totalTime / $count / 24 * 10))) : 0
        ];
    }

    /**
     * Get average processing time in hours
     */
    private static function getAverageProcessingTime(int $departmentId): float
    {
        $metrics = self::getPerformanceMetrics($departmentId);
        return $metrics['average_processing_time_hours'];
    }

    /**
     * Suggest priority based on content
     */
    private static function suggestPriority(string $description, string $title): string
    {
        $urgentKeywords = ['urgent', 'emergency', 'asap', 'immediate', 'critical', 'broken', 'not working', 'failure'];
        $rushKeywords = ['rush', 'priority', 'soon', 'needed today'];
        
        $text = strtolower($title . ' ' . $description);
        
        foreach ($urgentKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return 'Urgent';
            }
        }
        
        foreach ($rushKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return 'Rush';
            }
        }
        
        return 'Routine';
    }

    /**
     * Estimate completion time based on category
     */
    private static function estimateCompletionTime(string $category): int
    {
        $estimates = [
            'facility_maintenance' => 3,
            'electrical_maintenance' => 2,
            'plumbing_maintenance' => 2,
            'aircon_maintenance' => 3,
            'general_maintenance' => 5
        ];
        
        return $estimates[$category] ?? 3;
    }
}
