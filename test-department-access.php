<?php

echo "=== TESTING DEPARTMENT MODEL ACCESS ===\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Department;
use App\Models\FormRequest;
use App\Services\JobOrderService;

try {
    echo "=== TESTING DEPARTMENT MODEL ===\n";
    $departments = Department::all();
    echo "Found " . $departments->count() . " departments:\n";
    
    foreach ($departments->take(5) as $dept) {
        echo "- ID: {$dept->department_id}, Name: {$dept->dept_name}, Code: {$dept->dept_code}\n";
    }
    
    echo "\n=== FINDING SPECIFIC DEPARTMENTS ===\n";
    $pfmo = Department::where('dept_code', 'PFMO')->first();
    $ccs = Department::where('dept_code', 'CCS')->first();
    
    echo "PFMO: " . ($pfmo ? "ID {$pfmo->department_id} - {$pfmo->dept_name}" : "Not found") . "\n";
    echo "CCS: " . ($ccs ? "ID {$ccs->department_id} - {$ccs->dept_name}" : "Not found") . "\n";
    
    echo "\n=== FIXING REQUEST 3 DEPARTMENTS ===\n";
    $request3 = FormRequest::find(3);
    if ($request3 && $pfmo && $ccs) {
        $request3->update([
            'to_department_id' => $pfmo->department_id,
            'from_department_id' => $ccs->department_id
        ]);
        echo "✓ Updated request 3 with proper department IDs\n";
        echo "- To: {$pfmo->department_id} ({$pfmo->dept_code})\n";
        echo "- From: {$ccs->department_id} ({$ccs->dept_code})\n";
        
        // Test with fresh model
        $request3 = $request3->fresh();
        echo "\n=== TESTING JOB ORDER CREATION AGAIN ===\n";
        $needsJobOrder = JobOrderService::needsJobOrder($request3);
        echo "Needs job order: " . ($needsJobOrder ? "YES" : "NO") . "\n";
        
        if ($needsJobOrder) {
            $jobOrder = JobOrderService::createJobOrder($request3);
            if ($jobOrder) {
                echo "✓ Job order created successfully: {$jobOrder->job_order_number}\n";
            } else {
                echo "❌ Failed to create job order\n";
            }
        }
    }
    
    echo "\n=== CHECKING ALL FORM REQUESTS ===\n";
    $requests = FormRequest::with(['toDepartment', 'fromDepartment', 'jobOrder'])->get();
    foreach ($requests as $req) {
        echo "Request {$req->form_id}: ";
        echo "Type={$req->form_type}, Status={$req->status}, ";
        echo "To=" . ($req->toDepartment ? $req->toDepartment->dept_code : 'NULL') . ", ";
        echo "From=" . ($req->fromDepartment ? $req->fromDepartment->dept_code : 'NULL') . ", ";
        echo "JobOrder=" . ($req->jobOrder ? $req->jobOrder->job_order_number : 'NONE') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
