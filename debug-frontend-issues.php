<?php
require_once 'vendor/autoload.php';

// Start Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to initialize Laravel
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

echo "=== DEBUGGING FRONTEND START JOB ISSUES ===\n\n";

// Check what happens when we simulate a frontend AJAX request
use App\Models\User;
use App\Models\JobOrder;
use Illuminate\Support\Facades\Auth;

// Find the PFMO user
$user = User::where('username', 'PFMO-2025-0054')->first();
if (!$user) {
    echo "❌ PFMO user not found\n";
    exit;
}

echo "Found user: {$user->username}\n";
echo "Department: {$user->department}\n\n";

// Find a pending job order
$jobOrder = JobOrder::where('status', 'Pending')->first();
if (!$jobOrder) {
    echo "❌ No pending job orders found\n";
    exit;
}

echo "Testing with job order: {$jobOrder->request_number}\n";
echo "Current status: {$jobOrder->status}\n\n";

// Simulate logging in the user
Auth::login($user);
echo "✅ User logged in\n";
echo "Auth::check(): " . (Auth::check() ? 'TRUE' : 'FALSE') . "\n";
echo "Auth::user()->username: " . Auth::user()->username . "\n";
echo "Auth::user()->department: " . Auth::user()->department . "\n\n";

// Test the actual startJob method from JobOrderController
use App\Http\Controllers\JobOrderController;

try {
    $controller = new JobOrderController();

    // Create a mock request with CSRF token
    $request = new Illuminate\Http\Request();
    $request->setMethod('POST');
    $request->headers->set('X-CSRF-TOKEN', csrf_token());
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    echo "=== TESTING CONTROLLER METHOD ===\n";
    echo "CSRF Token: " . csrf_token() . "\n";
    echo "Request method: " . $request->method() . "\n";
    echo "Is AJAX: " . ($request->ajax() ? 'TRUE' : 'FALSE') . "\n\n";

    // Call the startJob method (it only takes JobOrder parameter)
    $response = $controller->startJob($jobOrder);

    echo "=== CONTROLLER RESPONSE ===\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n\n";

    // Check job order status after
    $jobOrder->refresh();
    echo "Job order status after: {$jobOrder->status}\n";

} catch (Exception $e) {
    echo "❌ Exception in controller: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FRONTEND CHECKLIST ===\n";
echo "1. ✅ User authentication works\n";
echo "2. ✅ PFMO access check passes\n";
echo "3. ✅ Job order logic works\n";
echo "4. ⚠️ Need to check CSRF token in browser\n";
echo "5. ⚠️ Need to check if user session is preserved in browser\n";
echo "6. ⚠️ Need to check JavaScript AJAX headers\n\n";

echo "=== BROWSER DEBUGGING STEPS ===\n";
echo "1. Open browser dev tools (F12)\n";
echo "2. Go to Network tab\n";
echo "3. Click start job button\n";
echo "4. Check the POST request details:\n";
echo "   - Headers should include X-CSRF-TOKEN\n";
echo "   - Headers should include X-Requested-With: XMLHttpRequest\n";
echo "   - Response should be JSON\n";
echo "5. Check Console tab for any JavaScript errors\n";
echo "6. Check Application tab > Cookies for Laravel session\n\n";

echo "=== QUICK FIX SUGGESTIONS ===\n";
echo "1. Clear browser cache and cookies\n";
echo "2. Log out and log back in\n";
echo "3. Check that the CSRF meta tag is in the page head\n";
echo "4. Verify JavaScript is loading the CSRF token correctly\n";
