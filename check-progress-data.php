<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobOrder;
use App\Models\JobOrderProgress;

echo "=== CHECKING EXISTING PROGRESS DATA ===\n";

$jobOrders = JobOrder::with(['progress', 'formRequest.requester.employeeInfo'])
    ->whereIn('status', ['In Progress', 'Completed'])
    ->get();

foreach ($jobOrders as $jo) {
    echo "\nJob Order: " . $jo->job_order_number . "\n";
    echo "Status: " . $jo->status . "\n";
    echo "Requester: " . ($jo->formRequest->requester->employeeInfo->FirstName ?? 'N/A') . " " . ($jo->formRequest->requester->employeeInfo->LastName ?? 'N/A') . "\n";
    echo "Progress Updates: " . $jo->progress->count() . "\n";

    if ($jo->progress->count() > 0) {
        $latest = $jo->progress->sortByDesc('created_at')->first();
        echo "Latest Progress: " . $latest->percentage_complete . "% - " . $latest->progress_note . "\n";
        if ($latest->current_location)
            echo "Location: " . $latest->current_location . "\n";
        if ($latest->estimated_time_remaining)
            echo "Time Remaining: " . $latest->estimated_time_remaining . " minutes\n";
        echo "Updated: " . $latest->created_at->format('M j, Y g:i A') . "\n";
    }
}

echo "\n=== TESTING PROGRESS RELATIONSHIPS ===\n";
$firstJobOrder = JobOrder::with('progress')->first();
if ($firstJobOrder) {
    echo "Job Order Model has progress relationship: " . ($firstJobOrder->progress ? 'YES' : 'NO') . "\n";
    echo "Progress count: " . $firstJobOrder->progress->count() . "\n";
}
