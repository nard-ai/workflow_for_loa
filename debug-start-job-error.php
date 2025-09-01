<?php
/**
 * DEBUG START JOB ERROR
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== DEBUGGING START JOB ERROR ===\n\n";

// Get the job order from the screenshot (JO-20250901-REQ004-001)
$jobOrder = JobOrder::where('job_order_number', 'JO-20250901-REQ004-001')->first();

if (!$jobOrder) {
    echo "❌ Job order JO-20250901-REQ004-001 not found\n";
    echo "Available job orders:\n";
    $allJobOrders = JobOrder::all();
    foreach ($allJobOrders as $jo) {
        echo "- {$jo->job_order_number} (Status: {$jo->status})\n";
    }
    exit;
}

echo "✅ Found job order: {$jobOrder->job_order_number}\n";
echo "Current status: {$jobOrder->status}\n";
echo "Form ID: {$jobOrder->form_id}\n";
echo "Created by: {$jobOrder->created_by}\n\n";

// Test canStart method
echo "=== TESTING canStart() ===\n";
$canStart = $jobOrder->canStart();
echo "canStart(): " . ($canStart ? "TRUE" : "FALSE") . "\n";
echo "Expected: TRUE (status should be 'Pending')\n\n";

if (!$canStart) {
    echo "❌ PROBLEM: Job order cannot be started\n";
    echo "Current status: '{$jobOrder->status}'\n";
    echo "Required status: 'Pending'\n\n";
}

// Test user authentication and PFMO access
echo "=== TESTING USER ACCESS ===\n";
$pfmoUsers = User::whereHas('department', function ($query) {
    $query->where('dept_code', 'PFMO');
})->get();

if ($pfmoUsers->isEmpty()) {
    echo "❌ PROBLEM: No PFMO users found in system\n";
    echo "Users need to belong to PFMO department to start job orders\n\n";
} else {
    echo "✅ PFMO users found:\n";
    foreach ($pfmoUsers as $user) {
        echo "- User ID {$user->accnt_id}: {$user->username} (Position: {$user->position})\n";
    }
    echo "\n";
}

// Test authentication with a PFMO user
if (!$pfmoUsers->isEmpty()) {
    $pfmoUser = $pfmoUsers->first();
    Auth::login($pfmoUser);

    echo "=== TESTING WITH PFMO USER (ID: {$pfmoUser->accnt_id}) ===\n";
    echo "Authenticated as: {$pfmoUser->username}\n";
    echo "Department: " . ($pfmoUser->department ? $pfmoUser->department->dept_code : 'None') . "\n\n";

    // Test the start() method directly
    echo "=== TESTING start() METHOD ===\n";
    try {
        if ($jobOrder->canStart()) {
            $result = $jobOrder->start();
            if ($result) {
                echo "✅ SUCCESS: Job order started\n";
                echo "New status: {$jobOrder->fresh()->status}\n";
                echo "Started at: {$jobOrder->fresh()->job_started_at}\n\n";
            } else {
                echo "❌ FAILURE: start() method returned false\n\n";
            }
        } else {
            echo "❌ FAILURE: canStart() returned false\n\n";
        }
    } catch (\Exception $e) {
        echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
    }
}

echo "=== POTENTIAL ISSUES ===\n";
echo "1. Check if user is logged in as PFMO department member\n";
echo "2. Check if job order status is exactly 'Pending'\n";
echo "3. Check for database constraints or triggers\n";
echo "4. Check Laravel logs for detailed error messages\n";
echo "5. Verify CSRF token is being sent with the request\n\n";

echo "=== RECOMMENDATIONS ===\n";
echo "1. Check browser console for detailed JavaScript errors\n";
echo "2. Check Laravel logs: storage/logs/laravel.log\n";
echo "3. Verify user is authenticated and belongs to PFMO department\n";
echo "4. Test with a fresh browser session\n";
