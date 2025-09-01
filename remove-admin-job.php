<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobOrder;

echo "Looking for ADMIN-0000 job orders...\n";

// Find job orders created by ADMIN (user_id = 2)
$adminJobOrders = JobOrder::where('created_by', 2)->get();

echo "Found " . $adminJobOrders->count() . " job orders created by ADMIN-0000\n";

foreach ($adminJobOrders as $jobOrder) {
    echo "Deleting job order: {$jobOrder->job_order_number} (ID: {$jobOrder->job_order_id})\n";
    $jobOrder->delete();
}

echo "All ADMIN-0000 job orders have been removed.\n";
