<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobOrder;
use App\Models\FormRequest;

echo "Creating test scenario for job order fill-up...\n\n";

// Get a completed form request
$formRequest = FormRequest::where('status', 'Approved')->first();

if (!$formRequest) {
    echo "No approved form request found. Creating one...\n";
    // You would need to create one manually or use an existing one
    exit;
}

echo "Using Form Request: {$formRequest->form_id} - {$formRequest->title}\n";
echo "Requested by: {$formRequest->requested_by}\n";

// Check if this form request already has a job order
$existingJobOrder = JobOrder::where('form_id', $formRequest->form_id)->first();

if ($existingJobOrder) {
    echo "Existing job order found: {$existingJobOrder->job_order_number}\n";
    echo "Status: {$existingJobOrder->status}\n";
    echo "Has description: " . ($existingJobOrder->request_description ? 'Yes' : 'No') . "\n";
    
    if (empty($existingJobOrder->request_description) || trim($existingJobOrder->request_description) === '') {
        echo "\n✓ This job order is ready for fill-up testing!\n";
        echo "Job Order ID: {$existingJobOrder->job_order_id}\n";
        echo "Job Order Number: {$existingJobOrder->job_order_number}\n";
    } else {
        echo "\nJob order already filled up. Resetting for testing...\n";
        $existingJobOrder->update([
            'request_description' => '',
            'requestor_signature' => null,
            'requestor_signature_date' => null,
            'status' => 'Pending'
        ]);
        echo "✓ Job order reset and ready for testing!\n";
    }
} else {
    echo "No job order found for this request. This is normal for the new workflow.\n";
}

echo "\nTesting complete. You can now test the fill-up functionality in the track view.\n";
