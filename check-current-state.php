<?php

echo "=== CHECKING CURRENT SYSTEM STATE ===\n\n";

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FormRequest;
use App\Models\JobOrder;
use App\Models\Department;
use App\Services\JobOrderService;

try {
    echo "=== FORM REQUESTS ===\n";
    $requests = FormRequest::with(['toDepartment', 'fromDepartment', 'jobOrder'])->orderBy('form_id', 'desc')->take(5)->get();
    
    foreach ($requests as $req) {
        echo "Request {$req->form_id}:\n";
        echo "  - Type: {$req->form_type}\n";
        echo "  - Status: {$req->status}\n";
        echo "  - To Dept: " . ($req->toDepartment ? $req->toDepartment->dept_name . " ({$req->toDepartment->dept_code})" : "NULL") . "\n";
        echo "  - From Dept: " . ($req->fromDepartment ? $req->fromDepartment->dept_name . " ({$req->fromDepartment->dept_code})" : "NULL") . "\n";
        echo "  - Has Job Order: " . ($req->jobOrder ? "YES ({$req->jobOrder->job_order_number})" : "NO") . "\n";
        
        // Test if it needs job order
        $needs = JobOrderService::needsJobOrder($req);
        echo "  - Needs Job Order: " . ($needs ? "YES" : "NO") . "\n\n";
    }
    
    echo "=== JOB ORDERS ===\n";
    $jobOrders = JobOrder::with(['formRequest'])->orderBy('job_order_id', 'desc')->take(5)->get();
    
    foreach ($jobOrders as $jo) {
        echo "Job Order {$jo->job_order_number}:\n";
        echo "  - Status: {$jo->status}\n";
        echo "  - Form ID: {$jo->form_id}\n";
        echo "  - Created by: {$jo->created_by}\n";
        if ($jo->job_started_at) {
            echo "  - Started at: {$jo->job_started_at}\n";
        }
        echo "\n";
    }
    
    echo "=== DEPARTMENTS ===\n";
    $departments = Department::all();
    foreach ($departments as $dept) {
        echo "- {$dept->dept_name} (Code: {$dept->dept_code}, ID: {$dept->id})\n";
    }
    
    echo "\n=== MANUAL JOB ORDER CREATION TEST ===\n";
    // Find an approved IOM request to PFMO
    $testRequest = FormRequest::where('status', 'Approved')
        ->where('form_type', 'IOM')
        ->whereHas('toDepartment', function ($q) {
            $q->where('dept_code', 'PFMO');
        })
        ->doesntHave('jobOrder')
        ->first();
        
    if ($testRequest) {
        echo "Found suitable request: {$testRequest->form_id}\n";
        echo "Attempting to create job order...\n";
        
        $jobOrder = JobOrderService::createJobOrder($testRequest);
        if ($jobOrder) {
            echo "✓ Success! Created job order: {$jobOrder->job_order_number}\n";
        } else {
            echo "❌ Failed to create job order\n";
        }
    } else {
        echo "No suitable requests found for job order creation\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nCheck completed.\n";
