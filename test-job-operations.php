<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobOrder;
use App\Models\JobOrderProgress;
use App\Models\User;

echo "Testing job order update progress and complete functionality...\n\n";

// Get the existing job order
$jobOrder = JobOrder::where('status', 'In Progress')->first();

if (!$jobOrder) {
    echo "No job order in progress found. Creating a test job order...\n";
    $jobOrder = JobOrder::first();
    if ($jobOrder) {
        $jobOrder->update(['status' => 'In Progress']);
        echo "Updated job order {$jobOrder->job_order_number} to In Progress\n";
    } else {
        echo "No job orders found in database\n";
        exit;
    }
}

echo "Testing with Job Order: {$jobOrder->job_order_number}\n";
echo "Current Status: {$jobOrder->status}\n\n";

// Test progress creation
echo "Testing progress creation...\n";
try {
    $progressData = [
        'job_order_id' => $jobOrder->job_order_id,
        'user_id' => 53, // Use PFMO staff user
        'update_type' => 'progress',
        'progress_note' => 'Test progress update',
        'percentage_complete' => 50,
        'current_location' => 'Test location',
        'issues_encountered' => null,
        'estimated_time_remaining' => 120,
    ];
    
    $progress = $jobOrder->progress()->create($progressData);
    echo "✓ Progress entry created successfully with ID: {$progress->id}\n";
    
} catch (Exception $e) {
    echo "✗ Error creating progress: " . $e->getMessage() . "\n";
}

// Test job completion
echo "\nTesting job completion...\n";
try {
    $canComplete = $jobOrder->canComplete();
    echo "Can complete: " . ($canComplete ? 'Yes' : 'No') . "\n";
    
    if ($canComplete) {
        $result = $jobOrder->complete([
            'request_description' => 'Test completion description',
            'findings' => 'Test findings',
            'date_completed' => now()->toDateString(),
            'job_completed_by' => 'Test User'
        ]);
        
        echo "✓ Job completion result: " . ($result ? 'Success' : 'Failed') . "\n";
        echo "New status: " . $jobOrder->fresh()->status . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error completing job: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
