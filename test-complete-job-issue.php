<?php
require_once 'vendor/autoload.php';

// Start Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to initialize Laravel
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

echo "=== TESTING COMPLETE JOB FUNCTIONALITY ===\n\n";

use App\Models\User;
use App\Models\JobOrder;
use App\Http\Controllers\JobOrderController;
use Illuminate\Support\Facades\Auth;

// Find the PFMO user
$user = User::where('username', 'PFMO-2025-0054')->first();
echo "Current user: {$user->username}\n";
echo "User department: {$user->department->dept_code}\n\n";

// Find the job order (ID 3 from browser)
$jobOrder = JobOrder::find(3);
if (!$jobOrder) {
    echo "❌ Job order with ID 3 not found\n";
    exit;
}

echo "Job Order: {$jobOrder->job_order_number}\n";
echo "Current status: {$jobOrder->status}\n\n";

// Make sure job order is in a state that can be completed
if ($jobOrder->status === 'Pending') {
    echo "Setting job order to 'In Progress' first...\n";
    $jobOrder->status = 'In Progress';
    $jobOrder->save();
    echo "Status updated to: {$jobOrder->status}\n\n";
}

// Login as the PFMO user
Auth::login($user);

// Test if job order can be completed
echo "=== TESTING JOB ORDER COMPLETION ABILITY ===\n";
echo "Can complete? " . ($jobOrder->canComplete() ? 'YES ✅' : 'NO ❌') . "\n\n";

// Test the complete job controller method
echo "=== TESTING CONTROLLER METHOD ===\n";
try {
    $controller = new JobOrderController();

    // Create a mock request with completion data
    $mockRequest = new Illuminate\Http\Request();
    $mockRequest->merge([
        'request_description' => 'Test completion description',
        'findings' => 'Test findings',
        'actual_cost' => 1500.00,
        'notes' => 'Test completion notes'
    ]);

    // Mock the request headers
    $mockRequest->headers->set('X-CSRF-TOKEN', csrf_token());
    $mockRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
    $mockRequest->headers->set('Content-Type', 'application/json');

    echo "Request data:\n";
    echo "- Description: " . $mockRequest->get('request_description') . "\n";
    echo "- Findings: " . $mockRequest->get('findings') . "\n";
    echo "- Cost: " . $mockRequest->get('actual_cost') . "\n";
    echo "- Notes: " . $mockRequest->get('notes') . "\n\n";

    $response = $controller->completeJob($mockRequest, $jobOrder);

    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";

    // Check job order status
    $jobOrder->refresh();
    echo "Job order status after: {$jobOrder->status}\n\n";

    if ($response->getStatusCode() === 200) {
        $responseData = json_decode($response->getContent(), true);
        if ($responseData && $responseData['success']) {
            echo "🎉 SUCCESS! Complete job functionality works!\n";
            echo "The complete job button should now work in the browser.\n\n";
        } else {
            echo "❌ Controller returned error: " . ($responseData['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ HTTP error from controller\n";
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
echo "2. Try the 'Complete Job' button again\n";
echo "3. The SyntaxError should be resolved\n";
echo "4. Check that the AJAX request now includes X-Requested-With header\n";
