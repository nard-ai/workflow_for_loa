<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PFMOWorkflowService;
use App\Models\Department;
use App\Models\User;
use App\Models\FormRequest;
use App\Models\FormApproval;
use Illuminate\Support\Facades\Auth;

class PFMOController extends Controller
{
    /**
     * Display PFMO dashboard
     */
    public function dashboard()
    {
        $dashboard = PFMOWorkflowService::getPFMODashboard();
        $recommendations = PFMOWorkflowService::getPFMORecommendations();
        
        // Get feedback data for dashboard
        $feedbackData = \App\Services\PFMOFeedbackService::getDashboardSummary();
        
        return view('pfmo.dashboard', compact('dashboard', 'recommendations', 'feedbackData'));
    }

    /**
     * Show PFMO facility requests
     */
    public function facilityRequests(Request $request)
    {
        $pfmoDepartment = Department::where('dept_code', 'PFMO')->first();
        
        if (!$pfmoDepartment) {
            return redirect()->back()->with('error', 'PFMO department not found');
        }

        $query = FormRequest::with(['requester.employeeInfo', 'requester.department', 'iomDetails', 'approvals'])
            ->where('to_department_id', $pfmoDepartment->department_id);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->whereHas('iomDetails', function($q) use ($request) {
                $q->where('priority', $request->priority);
            });
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('date_submitted', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('date_submitted', '<=', $request->date_to);
        }

        $requests = $query->orderBy('date_submitted', 'desc')
            ->paginate(20)
            ->appends($request->all());

        $stats = [
            'total' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)->count(),
            'pending' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)
                ->whereIn('status', ['Pending', 'In Progress', 'Pending Target Department Approval'])->count(),
            'approved' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)
                ->where('status', 'Approved')->count(),
            'rush' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)
                ->whereHas('iomDetails', function($q) {
                    $q->where('priority', 'Rush');
                })->count()
        ];

        return view('pfmo.facility-requests', compact('requests', 'stats'));
    }

    /**
     * Show single facility request details
     */
    public function showRequest($id)
    {
        $request = FormRequest::with([
            'requester.employeeInfo', 
            'requester.department', 
            'iomDetails', 
            'approvals.approver.employeeInfo',
            'department'
        ])->findOrFail($id);

        // Check if user has permission to view this request
        $user = Auth::user();
        $pfmoDepartment = Department::where('dept_code', 'PFMO')->first();
        
        if (!$pfmoDepartment || $user->department_id !== $pfmoDepartment->department_id) {
            if ($user->accessRole !== 'Admin' && $request->current_approver_id !== $user->accnt_id) {
                return redirect()->back()->with('error', 'Access denied');
            }
        }

        // Get categorization suggestions
        $categorySuggestions = PFMOWorkflowService::categorizePFMORequest(
            $request->iomDetails->description ?? '',
            $request->title
        );

        return view('pfmo.request-details', compact('request', 'categorySuggestions'));
    }

    /**
     * Process PFMO approval
     */
    public function processApproval(Request $request, $requestId)
    {
        $request->validate([
            'action' => 'required|in:Approved,Denied',
            'remarks' => 'nullable|string|max:1000',
            'signature_style' => 'nullable|string'
        ]);

        $formRequest = FormRequest::findOrFail($requestId);
        $user = Auth::user();

        // Verify user can approve this request
        if ($formRequest->current_approver_id !== $user->accnt_id && $user->accessRole !== 'Admin') {
            return redirect()->back()->with('error', 'You are not authorized to approve this request');
        }

        try {
            \DB::beginTransaction();

            // Create approval record
            $approval = new FormApproval();
            $approval->form_id = $formRequest->form_id;
            $approval->approver_id = $user->accnt_id;
            $approval->action = $request->action;
            $approval->action_date = now();
            $approval->remarks = $request->remarks;
            $approval->approval_level = 'PFMO';
            $approval->signature_style = $request->signature_style;
            $approval->save();

            // Update request status
            $formRequest->status = $request->action;
            if ($request->action === 'Approved') {
                $formRequest->date_approved = now();
            }
            $formRequest->save();

            // Clear approval caches
            app(\App\Services\ApprovalCacheService::class)->clearAllApprovalCaches();

            \DB::commit();

            $message = $request->action === 'Approved' ? 
                'Request approved successfully' : 
                'Request denied successfully';

            return redirect()->route('pfmo.facility-requests')->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('PFMO approval processing failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process approval');
        }
    }

    /**
     * PFMO performance metrics
     */
    public function metrics()
    {
        $pfmoDepartment = Department::where('dept_code', 'PFMO')->first();
        
        if (!$pfmoDepartment) {
            return redirect()->back()->with('error', 'PFMO department not found');
        }

        // Last 30 days metrics
        $last30Days = now()->subDays(30);
        
        $metrics = [
            'requests_received' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)
                ->where('date_submitted', '>=', $last30Days)->count(),
            'requests_processed' => FormRequest::where('to_department_id', $pfmoDepartment->department_id)
                ->where('date_submitted', '>=', $last30Days)
                ->where('status', 'Approved')->count(),
            'avg_processing_time' => 0,
            'by_category' => [],
            'monthly_trend' => []
        ];

        // Calculate average processing time
        $processedRequests = FormRequest::where('to_department_id', $pfmoDepartment->department_id)
            ->where('status', 'Approved')
            ->where('date_submitted', '>=', $last30Days)
            ->with('approvals')
            ->get();

        if ($processedRequests->count() > 0) {
            $totalHours = $processedRequests->sum(function($request) {
                $approval = $request->approvals->where('action', 'Approved')->first();
                return $approval ? $request->date_submitted->diffInHours($approval->action_date) : 0;
            });
            $metrics['avg_processing_time'] = round($totalHours / $processedRequests->count(), 1);
        }

        // Monthly trend (last 6 months)
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $monthlyCount = FormRequest::where('to_department_id', $pfmoDepartment->department_id)
                ->whereBetween('date_submitted', [$monthStart, $monthEnd])
                ->count();
                
            $metrics['monthly_trend'][] = [
                'month' => $monthStart->format('M Y'),
                'count' => $monthlyCount
            ];
        }

        return view('pfmo.metrics', compact('metrics'));
    }

    /**
     * Bulk action for multiple requests
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:tb_form_request,form_id',
            'action' => 'required|in:approve,deny,assign',
            'remarks' => 'nullable|string|max:1000',
            'assignee_id' => 'required_if:action,assign|exists:tb_account,accnt_id'
        ]);

        $user = Auth::user();
        $successCount = 0;
        $errors = [];

        try {
            \DB::beginTransaction();

            foreach ($request->request_ids as $requestId) {
                $formRequest = FormRequest::find($requestId);
                
                if (!$formRequest) {
                    continue;
                }

                switch ($request->action) {
                    case 'approve':
                    case 'deny':
                        if ($formRequest->current_approver_id === $user->accnt_id || $user->accessRole === 'Admin') {
                            $approval = new FormApproval();
                            $approval->form_id = $formRequest->form_id;
                            $approval->approver_id = $user->accnt_id;
                            $approval->action = $request->action === 'approve' ? 'Approved' : 'Denied';
                            $approval->action_date = now();
                            $approval->remarks = $request->remarks;
                            $approval->approval_level = 'PFMO';
                            $approval->save();

                            $formRequest->status = $approval->action;
                            if ($approval->action === 'Approved') {
                                $formRequest->date_approved = now();
                            }
                            $formRequest->save();
                            
                            $successCount++;
                        }
                        break;
                        
                    case 'assign':
                        if ($user->accessRole === 'Admin' || $user->position === 'Head') {
                            $formRequest->current_approver_id = $request->assignee_id;
                            $formRequest->save();
                            $successCount++;
                        }
                        break;
                }
            }

            // Clear approval caches
            app(\App\Services\ApprovalCacheService::class)->clearAllApprovalCaches();

            \DB::commit();

            return redirect()->back()->with('success', "Successfully processed {$successCount} requests");

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('PFMO bulk action failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process bulk action');
        }
    }
}
