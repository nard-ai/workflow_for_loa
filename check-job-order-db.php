<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking database tables...\n";

if (Schema::hasTable('job_order_progress')) {
    echo "✓ job_order_progress table exists\n";
} else {
    echo "✗ job_order_progress table does NOT exist\n";
}

if (Schema::hasTable('job_orders')) {
    echo "✓ job_orders table exists\n";
    
    // Check table structure
    $columns = DB::select("DESCRIBE job_orders");
    echo "\nJob Orders table columns:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
} else {
    echo "✗ job_orders table does NOT exist\n";
}

// Test basic job order operations
echo "\nTesting job order operations...\n";
try {
    $testJobOrder = \App\Models\JobOrder::first();
    if ($testJobOrder) {
        echo "✓ Can read job orders\n";
        echo "Sample job order: {$testJobOrder->job_order_number}\n";
        
        // Test if we can call methods
        echo "Can start: " . ($testJobOrder->canStart() ? 'Yes' : 'No') . "\n";
        echo "Can complete: " . ($testJobOrder->canComplete() ? 'Yes' : 'No') . "\n";
        echo "Current status: {$testJobOrder->status}\n";
    } else {
        echo "No job orders found in database\n";
    }
} catch (Exception $e) {
    echo "✗ Error testing job orders: " . $e->getMessage() . "\n";
}
