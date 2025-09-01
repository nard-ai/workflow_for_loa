<?php
require_once 'vendor/autoload.php';

// Start Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to initialize Laravel
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

echo "=== CREATING TEST JOB ORDER FOR CURRENT USER ===\n\n";

use App\Models\User;
use App\Models\JobOrder;
use App\Http\Controllers\JobOrderController;
use Illuminate\Support\Facades\Auth;

// Find the PFMO user
$user = User::where('username', 'PFMO-2025-0054')->first();
echo "Creating job order for user: {$user->username} (ID: {$user->accnt_id})\n\n";

// Create a test job order
$jobOrder = new JobOrder();
$jobOrder->job_order_number = 'JO-TEST-' . date('Ymd') . '-' . rand(1000, 9999);
$jobOrder->created_by = $user->accnt_id;
$jobOrder->control_number = 'CTRL-' . rand(10000, 99999);
$jobOrder->date_prepared = now()->format('Y-m-d');
$jobOrder->requestor_name = 'Test Requestor';
$jobOrder->department = 'IT Department';
$jobOrder->request_description = 'Test job order for start button functionality';
$jobOrder->assistance = 1; // true
$jobOrder->repair_repaint = 0;
$jobOrder->installation = 0;
$jobOrder->cleaning = 0;
$jobOrder->check_up_inspection = 0;
$jobOrder->others = 0;
$jobOrder->priority = 'Normal';
$jobOrder->status = 'Pending';
$jobOrder->date_requested = now()->format('Y-m-d');
$jobOrder->created_at = now();
$jobOrder->updated_at = now();

try {
    $jobOrder->save();
    echo "✅ Test job order created successfully!\n";
    echo "Job Order Number: {$jobOrder->job_order_number}\n";
    echo "Created by: {$jobOrder->created_by}\n";
    echo "Status: {$jobOrder->status}\n\n";

    // Now test if the user can manage this job order
    Auth::login($user);

    // Test the authorization logic
    echo "=== TESTING AUTHORIZATION ===\n";
    echo "1. Is creator? " . ($jobOrder->created_by === $user->accnt_id ? 'YES' : 'NO') . "\n";
    echo "2. Can start? " . ($jobOrder->canStart() ? 'YES' : 'NO') . "\n\n";

    // Test the controller logic
    echo "=== TESTING START JOB ===\n";

    $controller = new JobOrderController();
    $response = $controller->startJob($jobOrder);

    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";

    // Check the job order status
    $jobOrder->refresh();
    echo "Job order status after: {$jobOrder->status}\n\n";

    if ($response->getStatusCode() === 200) {
        echo "🎉 SUCCESS! The start job functionality works when user has proper authorization!\n\n";
        echo "=== NEXT STEPS ===\n";
        echo "1. Use this job order number in the browser: {$jobOrder->job_order_number}\n";
        echo "2. Or login as PFMO Head user: PFMO-2025-0051\n";
        echo "3. Or review if all PFMO staff should manage all job orders\n";
    }

} catch (Exception $e) {
    echo "❌ Error creating job order: " . $e->getMessage() . "\n";
}
