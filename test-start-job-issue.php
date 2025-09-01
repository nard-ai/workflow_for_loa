<?php

echo "=== TESTING START JOB FUNCTIONALITY ===\n\n";

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

try {
    // Find a job order to test
    $jobOrder = JobOrder::where('status', 'Pending')->first();
    
    if (!$jobOrder) {
        echo "No pending job orders found. Let's check all job orders:\n";
        $allJobOrders = JobOrder::with(['formRequest', 'created_by_user'])->get();
        
        foreach ($allJobOrders as $jo) {
            echo "- Job Order {$jo->job_order_number}: Status = {$jo->status}\n";
        }
        
        if ($allJobOrders->isEmpty()) {
            echo "No job orders exist in the system.\n";
        }
        
        exit;
    }
    
    echo "Found job order to test:\n";
    echo "- Number: {$jobOrder->job_order_number}\n";
    echo "- Status: {$jobOrder->status}\n";
    echo "- Created by: {$jobOrder->created_by}\n";
    
    // Test the canStart method
    echo "\n=== TESTING canStart() METHOD ===\n";
    if ($jobOrder->canStart()) {
        echo "✓ Job order can be started\n";
    } else {
        echo "❌ Job order cannot be started (status: {$jobOrder->status})\n";
    }
    
    // Test the start method
    echo "\n=== TESTING start() METHOD ===\n";
    if ($jobOrder->status === 'Pending') {
        echo "Attempting to start job order...\n";
        
        $result = $jobOrder->start();
        
        if ($result) {
            echo "✓ Job order started successfully\n";
            echo "- New status: {$jobOrder->fresh()->status}\n";
            echo "- Started at: {$jobOrder->fresh()->job_started_at}\n";
        } else {
            echo "❌ Failed to start job order\n";
        }
    } else {
        echo "Skipping start test - job order is not pending\n";
    }
    
    // Check if there are any PFMO users who can manage job orders
    echo "\n=== CHECKING PFMO USERS ===\n";
    $pfmoUsers = User::whereHas('department', function ($query) {
        $query->where('dept_code', 'PFMO');
    })->get();
    
    echo "PFMO users found: " . $pfmoUsers->count() . "\n";
    foreach ($pfmoUsers as $user) {
        echo "- {$user->username} (ID: {$user->accnt_id}) - Position: {$user->position}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
