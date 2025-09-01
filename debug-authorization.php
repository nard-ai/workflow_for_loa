<?php
require_once 'vendor/autoload.php';

// Start Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to initialize Laravel
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

echo "=== DEBUGGING AUTHORIZATION ISSUE ===\n\n";

use App\Models\User;
use App\Models\JobOrder;

// Find the PFMO user
$user = User::where('username', 'PFMO-2025-0054')->first();
echo "Current user: {$user->username}\n";
echo "User accnt_id: {$user->accnt_id}\n";
echo "User position: {$user->position}\n";
echo "User department code: " . ($user->department ? $user->department->dept_code : 'NULL') . "\n\n";

// Find the job order being tested
$jobOrder = JobOrder::where('job_order_number', 'JO-20250901-REQ004-001')->first();
if ($jobOrder) {
    echo "Job Order: {$jobOrder->job_order_number}\n";
    echo "Created by: {$jobOrder->created_by}\n";
    echo "Status: {$jobOrder->status}\n\n";

    // Check who created this job order
    $creator = User::where('accnt_id', $jobOrder->created_by)->first();
    if ($creator) {
        echo "Creator details:\n";
        echo "  Username: {$creator->username}\n";
        echo "  Position: {$creator->position}\n";
        echo "  Department: " . ($creator->department ? $creator->department->dept_code : 'NULL') . "\n\n";
    }

    // Test the authorization logic
    echo "=== AUTHORIZATION CHECKS ===\n";
    echo "1. Is creator? " . ($jobOrder->created_by === $user->accnt_id ? 'YES' : 'NO') . "\n";
    echo "2. Is PFMO Head? ";
    if ($user->position === 'Head' && $user->department && $user->department->dept_code === 'PFMO') {
        echo "YES\n";
    } else {
        echo "NO (Position: {$user->position}, Dept: " . ($user->department ? $user->department->dept_code : 'NULL') . ")\n";
    }
    echo "3. Can manage? " . (($jobOrder->created_by === $user->accnt_id || ($user->position === 'Head' && $user->department && $user->department->dept_code === 'PFMO')) ? 'YES' : 'NO') . "\n\n";
}

echo "=== SOLUTION ===\n";
echo "The current user (Staff) can only manage job orders they created.\n";
echo "For testing, we need either:\n";
echo "1. A job order created by PFMO-2025-0054, OR\n";
echo "2. Login as a PFMO Head user, OR\n";
echo "3. Modify the authorization logic if PFMO Staff should manage all job orders\n\n";

// Let's find a job order created by this user
$userJobOrders = JobOrder::where('created_by', $user->accnt_id)->get();
echo "=== JOB ORDERS CREATED BY CURRENT USER ===\n";
if ($userJobOrders->count() > 0) {
    foreach ($userJobOrders as $jo) {
        echo "- {$jo->job_order_number} (Status: {$jo->status})\n";
    }
} else {
    echo "No job orders found created by this user.\n";
}

echo "\n=== PFMO HEAD USERS ===\n";
$pfmoHeads = User::whereHas('department', function ($query) {
    $query->where('dept_code', 'PFMO');
})->where('position', 'Head')->get();

if ($pfmoHeads->count() > 0) {
    foreach ($pfmoHeads as $head) {
        echo "- {$head->username} (Position: {$head->position})\n";
    }
} else {
    echo "No PFMO Head users found.\n";
}
