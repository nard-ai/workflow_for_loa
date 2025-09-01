<?php

echo "=== INVESTIGATING REQUEST 3 ISSUES ===\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FormRequest;
use App\Models\Department;
use App\Services\JobOrderService;

try {
    $request = FormRequest::find(3);
    if ($request) {
        echo "Request 3 raw data:\n";
        echo "- form_id: {$request->form_id}\n";
        echo "- title: {$request->title}\n";
        echo "- form_type: {$request->form_type}\n";
        echo "- status: {$request->status}\n";
        echo "- from_department_id: " . ($request->from_department_id ?? 'NULL') . "\n";
        echo "- to_department_id: " . ($request->to_department_id ?? 'NULL') . "\n";
        echo "- requested_by: {$request->requested_by}\n\n";
        
        // Fix the department relationships
        echo "=== FIXING DEPARTMENT RELATIONSHIPS ===\n";
        $pfmoDept = Department::where('dept_code', 'PFMO')->first();
        $ccsDept = Department::where('dept_code', 'CCS')->first();
        
        if ($pfmoDept && $ccsDept) {
            echo "Found departments:\n";
            echo "- PFMO ID: {$pfmoDept->id}\n";
            echo "- CCS ID: {$ccsDept->id}\n";
            
            $request->update([
                'to_department_id' => $pfmoDept->id,
                'from_department_id' => $ccsDept->id
            ]);
            
            echo "✓ Updated request 3 with proper department IDs\n\n";
            
            // Now test job order creation
            echo "=== TESTING JOB ORDER CREATION FOR REQUEST 3 ===\n";
            $request = $request->fresh(); // Reload
            
            $needsJobOrder = JobOrderService::needsJobOrder($request);
            echo "Needs job order: " . ($needsJobOrder ? "YES" : "NO") . "\n";
            
            if ($needsJobOrder) {
                $jobOrder = JobOrderService::createJobOrder($request);
                if ($jobOrder) {
                    echo "✓ Job order created: {$jobOrder->job_order_number}\n";
                } else {
                    echo "❌ Failed to create job order\n";
                }
            }
        } else {
            echo "❌ Could not find PFMO or CCS departments\n";
        }
    }
    
    // Also check if other departments have proper IDs
    echo "\n=== CHECKING DEPARTMENT IDs ===\n";
    $departments = Department::whereIn('dept_code', ['PFMO', 'CCS', 'VPAA'])->get();
    foreach ($departments as $dept) {
        echo "- {$dept->dept_name}: ID = {$dept->id}, Code = {$dept->dept_code}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nInvestigation completed.\n";
