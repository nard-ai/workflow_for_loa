<?php
/**
 * TEST CURRENT USER AUTHENTICATION AND JOB ORDER ACCESS
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING CURRENT USER AUTHENTICATION ===\n\n";

// Check if we can authenticate as the current user from screenshot
$user = User::where('username', 'PFMO-2025-0054')->first();

if (!$user) {
    echo "❌ User PFMO-2025-0054 not found\n";
    echo "Available PFMO users:\n";
    $pfmoUsers = User::whereHas('department', function ($query) {
        $query->where('dept_code', 'PFMO');
    })->get();
    foreach ($pfmoUsers as $u) {
        echo "- {$u->user_code}: {$u->username}\n";
    }
    exit;
}

echo "✅ Found user: {$user->username} ({$user->user_code})\n";
echo "Department: " . ($user->department ? $user->department->dept_code : 'None') . "\n";
echo "Position: {$user->position}\n\n";

// Authenticate as this user
Auth::login($user);

echo "=== TESTING JOB ORDER ACCESS ===\n";
$jobOrder = JobOrder::where('job_order_number', 'JO-20250901-REQ004-001')->first();

if (!$jobOrder) {
    echo "❌ Job order not found\n";
    exit;
}

echo "Job Order: {$jobOrder->job_order_number}\n";
echo "Status: {$jobOrder->status}\n";

// Test PFMO access
echo "\n=== TESTING PFMO ACCESS CHECK ===\n";
try {
    $currentUser = Auth::user();
    echo "Current user: " . ($currentUser ? $currentUser->username : 'Not authenticated') . "\n";
    echo "Department: " . ($currentUser && $currentUser->department ? $currentUser->department->dept_code : 'None') . "\n";

    if (!$currentUser || !$currentUser->department || $currentUser->department->dept_code !== 'PFMO') {
        echo "❌ PFMO access check would FAIL\n";
        echo "This explains why the start job request fails!\n\n";
    } else {
        echo "✅ PFMO access check would PASS\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Access check failed: " . $e->getMessage() . "\n\n";
}

// Reset job order back to pending for testing
if ($jobOrder->status !== 'Pending') {
    echo "=== RESETTING JOB ORDER TO PENDING ===\n";
    $jobOrder->update([
        'status' => 'Pending',
        'job_started_at' => null
    ]);
    echo "✅ Job order reset to Pending status\n\n";
}

echo "=== SOLUTION ===\n";
echo "The issue appears to be authentication or CSRF token related.\n";
echo "The user needs to be properly logged in as a PFMO department member.\n\n";

echo "=== IMMEDIATE FIX NEEDED ===\n";
echo "1. Verify user is logged in as PFMO member in browser\n";
echo "2. Check CSRF token is being sent in JavaScript request\n";
echo "3. Clear browser cache and session storage\n";
echo "4. Test with a fresh login\n";
