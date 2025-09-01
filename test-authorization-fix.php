<?php
require_once 'vendor/autoload.php';

// Start Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to initialize Laravel
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

echo "=== TESTING AUTHORIZATION FIX ===\n\n";

use App\Models\User;
use App\Models\JobOrder;
use App\Http\Controllers\JobOrderController;
use Illuminate\Support\Facades\Auth;

// Find the PFMO user
$user = User::where('username', 'PFMO-2025-0054')->first();
echo "Current user: {$user->username}\n";
echo "User position: {$user->position}\n";
echo "User department: {$user->department->dept_code}\n\n";

// Find the job order from the browser (ID 3)
$jobOrder = JobOrder::find(3);
if (!$jobOrder) {
    echo "❌ Job order with ID 3 not found\n";
    exit;
}

echo "Job Order: {$jobOrder->job_order_number}\n";
echo "Created by: {$jobOrder->created_by}\n";
echo "Status: {$jobOrder->status}\n\n";

// Login as the PFMO user
Auth::login($user);

// Test the new authorization logic manually
echo "=== TESTING NEW AUTHORIZATION LOGIC ===\n";
echo "1. Is PFMO department member? ";
if ($user->department && $user->department->dept_code === 'PFMO') {
    echo "YES ✅\n";
} else {
    echo "NO ❌\n";
}

echo "2. Is original creator? ";
if ($jobOrder->created_by === $user->accnt_id) {
    echo "YES\n";
} else {
    echo "NO\n";
}

echo "3. Should have access? ";
$shouldHaveAccess = ($user->department && $user->department->dept_code === 'PFMO') ||
    ($jobOrder->created_by === $user->accnt_id);
echo ($shouldHaveAccess ? "YES ✅" : "NO ❌") . "\n\n";

// Test the actual controller method
echo "=== TESTING CONTROLLER METHOD ===\n";
try {
    $controller = new JobOrderController();
    $response = $controller->startJob($jobOrder);

    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";

    // Check job order status
    $jobOrder->refresh();
    echo "Job order status after: {$jobOrder->status}\n\n";

    if ($response->getStatusCode() === 200) {
        echo "🎉 SUCCESS! Authorization fix works!\n";
        echo "The start job button should now work in the browser.\n\n";

        // Reset the job order back to Pending for browser testing
        $jobOrder->status = 'Pending';
        $jobOrder->save();
        echo "✅ Job order reset to Pending status for browser testing\n";
    } else {
        echo "❌ Still having authorization issues\n";
        $responseData = json_decode($response->getContent(), true);
        if ($responseData && isset($responseData['message'])) {
            echo "Error message: " . $responseData['message'] . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Try clicking the 'Start Job' button again\n";
echo "3. Check browser console for any remaining errors\n";
echo "4. The 403 Forbidden error should be resolved\n";
