<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobOrder;

echo "=== CURRENT JOB ORDER STATUS ===\n\n";

$jobOrders = JobOrder::with('creator')->get();
echo "Current Job Orders:\n";
foreach ($jobOrders as $jo) {
    echo "- {$jo->job_order_number} - Status: {$jo->status}\n";
    echo "  Request Description: '" . substr($jo->request_description, 0, 50) . "...'\n";
    echo "  Has Feedback: " . ($jo->requestor_comments ? 'Yes' : 'No') . "\n\n";
}
