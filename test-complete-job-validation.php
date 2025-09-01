<?php
require_once 'vendor/autoload.php';

// Start Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to initialize Laravel
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

echo "=== TESTING COMPLETE JOB VALIDATION FIX ===\n\n";

use App\Models\User;
use App\Models\JobOrder;
use App\Http\Controllers\JobOrderController;
use Illuminate\Support\Facades\Auth;

// Find the PFMO user
$user = User::where('username', 'PFMO-2025-0054')->first();
echo "Current user: {$user->username}\n\n";

// Find the job order (ID 3 from browser)
$jobOrder = JobOrder::find(3);
if (!$jobOrder) {
    echo "❌ Job order with ID 3 not found\n";
    exit;
}

echo "Job Order: {$jobOrder->job_order_number}\n";
echo "Current status: {$jobOrder->status}\n\n";

// Make sure job order is in progress
if ($jobOrder->status !== 'In Progress') {
    echo "Setting job order to 'In Progress'...\n";
    $jobOrder->status = 'In Progress';
    $jobOrder->save();
    echo "Status updated to: {$jobOrder->status}\n\n";
}

// Login as the PFMO user
Auth::login($user);

// Test the complete job with the EXACT data from the form
echo "=== TESTING WITH CORRECT FORM DATA ===\n";
try {
    $controller = new JobOrderController();

    // Create a mock request with the exact field names from the form
    $mockRequest = new Illuminate\Http\Request();
    $mockRequest->merge([
        'findings' => 'sdfas',  // From your screenshot
        'actions_taken' => 'asdasd',  // From your screenshot - this is required
        'recommendations' => 'asdas'  // From your screenshot
    ]);

    // Mock the headers
    $mockRequest->headers->set('X-CSRF-TOKEN', csrf_token());
    $mockRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
    $mockRequest->headers->set('Content-Type', 'application/json');

    echo "Request data (matching form exactly):\n";
    echo "- findings: " . $mockRequest->get('findings') . "\n";
    echo "- actions_taken: " . $mockRequest->get('actions_taken') . "\n";
    echo "- recommendations: " . $mockRequest->get('recommendations') . "\n\n";

    $response = $controller->completeJob($mockRequest, $jobOrder);

    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";

    // Check job order status
    $jobOrder->refresh();
    echo "Job order status after: {$jobOrder->status}\n\n";

    if ($response->getStatusCode() === 200) {
        $responseData = json_decode($response->getContent(), true);
        if ($responseData && $responseData['success']) {
            echo "🎉 SUCCESS! Complete job validation is now fixed!\n";
            echo "The 422 validation error should be resolved.\n\n";

            // Show the completion data that was saved
            echo "=== SAVED COMPLETION DATA ===\n";
            echo "- Actions Taken: " . ($jobOrder->actions_taken ?? 'NULL') . "\n";
            echo "- Findings: " . ($jobOrder->findings ?? 'NULL') . "\n";
            echo "- Recommendations: " . ($jobOrder->recommendations ?? 'NULL') . "\n";
            echo "- Completed By: " . ($jobOrder->job_completed_by ?? 'NULL') . "\n";
            echo "- Date Completed: " . ($jobOrder->date_completed ?? 'NULL') . "\n";
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

echo "\n=== VALIDATION REQUIREMENTS SUMMARY ===\n";
echo "✅ Required field: actions_taken (matches form)\n";
echo "✅ Optional field: findings (matches form)\n";
echo "✅ Optional field: recommendations (matches form)\n";
echo "❌ Removed: request_description (not in form)\n";
echo "❌ Removed: actual_cost (not in form)\n";
echo "❌ Removed: notes (not in form)\n\n";

echo "=== NEXT STEPS ===\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Try the 'Complete Job' button again\n";
echo "3. Fill in at least the 'Actions Taken' field (required)\n";
echo "4. The 422 validation error should be fixed\n";
