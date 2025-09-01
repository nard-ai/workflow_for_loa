<?php

echo "=== TESTING JOB ORDER CREATION AND START FUNCTIONALITY ===\n\n";

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FormRequest;
use App\Models\JobOrder;
use App\Models\User;
use App\Services\JobOrderService;
use Illuminate\Support\Facades\Auth;

try {
    echo "=== CHECKING FOR APPROVED IOM REQUESTS WITHOUT JOB ORDERS ===\n";
    
    $approvedRequests = FormRequest::where('status', 'Approved')
        ->where('form_type', 'IOM')
        ->whereHas('toDepartment', function ($q) {
            $q->where('dept_code', 'PFMO');
        })
        ->doesntHave('jobOrder')
        ->get();
    
    echo "Found " . $approvedRequests->count() . " approved IOM requests to PFMO without job orders\n\n";
    
    if ($approvedRequests->count() > 0) {
        $request = $approvedRequests->first();
        echo "Testing with Request ID: {$request->form_id}\n";
        echo "- Title: {$request->title}\n";
        echo "- Status: {$request->status}\n";
        echo "- To Department: {$request->toDepartment->dept_name} ({$request->toDepartment->dept_code})\n\n";
        
        echo "=== TESTING JobOrderService::needsJobOrder() ===\n";
        $needsJobOrder = JobOrderService::needsJobOrder($request);
        echo "Needs job order: " . ($needsJobOrder ? "YES" : "NO") . "\n\n";
        
        if ($needsJobOrder) {
            echo "=== TESTING JobOrderService::createJobOrder() ===\n";
            $jobOrder = JobOrderService::createJobOrder($request);
            
            if ($jobOrder) {
                echo "✓ Job order created successfully!\n";
                echo "- Job Order Number: {$jobOrder->job_order_number}\n";
                echo "- Status: {$jobOrder->status}\n";
                echo "- Created by: {$jobOrder->created_by}\n\n";
                
                echo "=== TESTING TRACK VIEW DATA ===\n";
                // Simulate what the track view would get
                $trackFormRequest = FormRequest::with([
                    'requester.department',
                    'fromDepartment',
                    'toDepartment',
                    'iomDetails',
                    'leaveDetails',
                    'approvals.approver.employeeInfo',
                    'currentApprover',
                    'jobOrder.created_by_user.employeeInfo'
                ])->find($request->form_id);
                
                if ($trackFormRequest->jobOrder) {
                    echo "✓ Job order properly loaded in track view\n";
                    echo "- Job Order: {$trackFormRequest->jobOrder->job_order_number}\n";
                    echo "- Status: {$trackFormRequest->jobOrder->status}\n";
                    if ($trackFormRequest->jobOrder->created_by_user) {
                        echo "- Created by: {$trackFormRequest->jobOrder->created_by_user->username}\n";
                    }
                } else {
                    echo "❌ Job order not found in track view query\n";
                }
                
                echo "\n=== TESTING START JOB FUNCTIONALITY ===\n";
                if ($jobOrder->canStart()) {
                    echo "✓ Job order can be started\n";
                    
                    $startResult = $jobOrder->start();
                    if ($startResult) {
                        echo "✓ Job order started successfully\n";
                        echo "- New status: {$jobOrder->fresh()->status}\n";
                        echo "- Started at: {$jobOrder->fresh()->job_started_at}\n";
                    } else {
                        echo "❌ Failed to start job order\n";
                    }
                } else {
                    echo "❌ Job order cannot be started (status: {$jobOrder->status})\n";
                }
                
            } else {
                echo "❌ Failed to create job order\n";
            }
        }
    } else {
        echo "No requests available for testing. Creating a sample approved request...\n\n";
        
        // Check if we have users and departments
        $user = User::first();
        $pfmoDept = \App\Models\Department::where('dept_code', 'PFMO')->first();
        $otherDept = \App\Models\Department::where('dept_code', '!=', 'PFMO')->first();
        
        if (!$user || !$pfmoDept || !$otherDept) {
            echo "❌ Missing required data (users/departments) to create test request\n";
            exit;
        }
        
        echo "Creating test IOM request...\n";
        $testRequest = FormRequest::create([
            'title' => 'Test Job Order Creation - ' . now()->format('Y-m-d H:i:s'),
            'form_type' => 'IOM',
            'requested_by' => $user->accnt_id,
            'from_department_id' => $otherDept->id,
            'to_department_id' => $pfmoDept->id,
            'date_submitted' => now(),
            'status' => 'Approved'
        ]);
        
        echo "✓ Test request created with ID: {$testRequest->form_id}\n";
        echo "Now testing job order creation...\n\n";
        
        $jobOrder = JobOrderService::createJobOrder($testRequest);
        if ($jobOrder) {
            echo "✓ Job order created for test request!\n";
            echo "- Job Order Number: {$jobOrder->job_order_number}\n";
        } else {
            echo "❌ Failed to create job order for test request\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
