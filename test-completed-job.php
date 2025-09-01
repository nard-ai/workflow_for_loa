<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Create database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => dirname(__FILE__) . '/database/database.sqlite',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Use DB facade
use Illuminate\Database\Capsule\Manager as DB;

echo "=== TESTING COMPLETED JOB ORDER WORKFLOW ===\n\n";

try {
    // Find first pending job order
    $jobOrder = $capsule::table('job_orders')
        ->where('status', 'Pending')
        ->first();
    
    if (!$jobOrder) {
        echo "No pending job orders found to test with.\n";
        exit;
    }
    
    echo "Found job order: {$jobOrder->job_order_number}\n";
    echo "Current status: {$jobOrder->status}\n\n";
    
    // Mark as completed
    $capsule::table('job_orders')
        ->where('id', $jobOrder->id)
        ->update([
            'status' => 'Completed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    
    echo "✅ Job order marked as Completed\n";
    echo "Now the two-step modal should appear for this job order.\n\n";
    
    // Show updated status
    $updated = $capsule::table('job_orders')->where('id', $jobOrder->id)->first();
    echo "Updated status: {$updated->status}\n";
    echo "Request description: '{$updated->request_description}'\n";
    echo "Has feedback: " . ($updated->feedback_comment ? 'Yes' : 'No') . "\n\n";
    
    echo "🎯 TEST RESULT: Job order {$updated->job_order_number} is now eligible for the two-step modal!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
