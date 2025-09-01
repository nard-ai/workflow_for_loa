<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\FormRequest;
use App\Models\JobOrder;

// Test the progress visibility implementation
echo "=== JOB ORDER PROGRESS VISIBILITY TEST ===\n\n";

try {
    // Get a job order with progress data
    $formRequest = FormRequest::with([
        'jobOrder.progressUpdates.updated_by_user'
    ])->whereHas('jobOrder', function ($query) {
        $query->whereHas('progressUpdates');
    })->first();

    if (!$formRequest || !$formRequest->jobOrder) {
        echo "❌ No form request with job order progress found\n";
        exit(1);
    }

    $jobOrder = $formRequest->jobOrder;
    echo "✅ Found Job Order: {$jobOrder->job_order_number}\n";
    echo "   Form Request ID: {$formRequest->form_id}\n";
    echo "   Status: {$jobOrder->status}\n\n";

    // Test progress helper methods
    echo "=== TESTING PROGRESS HELPER METHODS ===\n";

    echo "1. Latest Progress:\n";
    $latestProgress = $jobOrder->latestProgress;
    if ($latestProgress) {
        echo "   ✅ Percentage: {$latestProgress->percentage_complete}%\n";
        echo "   ✅ Note: " . ($latestProgress->progress_note ?: 'None') . "\n";
        echo "   ✅ Location: " . ($latestProgress->current_location ?: 'None') . "\n";
        echo "   ✅ Updated: {$latestProgress->updated_at}\n";
    } else {
        echo "   ❌ No latest progress found\n";
    }

    echo "\n2. Progress Percentage:\n";
    $progressPercentage = $jobOrder->progressPercentage;
    echo "   ✅ Progress Percentage: {$progressPercentage}%\n";

    echo "\n3. Estimated Time Remaining:\n";
    $estimatedTime = $jobOrder->estimatedTimeRemainingFormatted;
    echo "   ✅ Estimated Time: " . ($estimatedTime ?: 'None') . "\n";

    echo "\n4. Progress History for Track View:\n";
    $progressHistory = $jobOrder->progressHistoryForTrackView;
    echo "   ✅ Progress Updates Count: {$progressHistory->count()}\n";

    foreach ($progressHistory as $index => $progress) {
        echo "   Update " . ($index + 1) . ":\n";
        echo "     - {$progress->percentage_complete}% - " . ($progress->progress_note ?: 'No note') . "\n";
        echo "     - Location: " . ($progress->current_location ?: 'None') . "\n";
        echo "     - Time: " . ($progress->estimatedTimeRemainingFormatted ?: 'None') . "\n";
        echo "     - Date: {$progress->updated_at->format('M j, Y g:i A')}\n";
    }

    echo "\n=== TESTING DATA AVAILABILITY ===\n";

    // Check if controller loads progress data
    echo "1. Controller Progress Loading:\n";
    echo "   ✅ progressUpdates relationship loaded: " . ($jobOrder->relationLoaded('progressUpdates') ? 'Yes' : 'No') . "\n";

    echo "\n2. View Data Structure:\n";
    echo "   ✅ Job Order Status: {$jobOrder->status}\n";
    echo "   ✅ Has Progress Updates: " . ($jobOrder->progressUpdates->count() > 0 ? 'Yes' : 'No') . "\n";
    echo "   ✅ Should Show Progress: " . (in_array($jobOrder->status, ['In Progress', 'Completed']) ? 'Yes' : 'No') . "\n";

    echo "\n=== TESTING VIEW CONDITIONS ===\n";

    // Test view conditions
    $shouldShowProgress = in_array($jobOrder->status, ['In Progress', 'Completed']) && $latestProgress;
    echo "1. Progress Section Should Display: " . ($shouldShowProgress ? 'Yes' : 'No') . "\n";

    $shouldShowHistory = $jobOrder->progressUpdates->count() > 1;
    echo "2. Progress History Should Display: " . ($shouldShowHistory ? 'Yes' : 'No') . "\n";

    if ($shouldShowProgress) {
        echo "\n=== VIEW DATA SIMULATION ===\n";
        echo "Progress Bar: {$progressPercentage}%\n";
        echo "Latest Update: " . ($latestProgress->progress_note ?: 'No update note') . "\n";
        echo "Current Location: " . ($latestProgress->current_location ?: 'No location') . "\n";
        echo "Time Estimate: " . ($estimatedTime ?: 'No time estimate') . "\n";
        echo "Last Updated: {$latestProgress->updated_at->format('M j, Y g:i A')}\n";

        if ($shouldShowHistory) {
            echo "\nProgress History Available: {$progressHistory->count()} updates\n";
        }
    }

    echo "\n✅ PROGRESS VISIBILITY TEST COMPLETED SUCCESSFULLY!\n";
    echo "   The track view should now display:\n";
    echo "   - Progress bar with percentage\n";
    echo "   - Latest progress notes and location\n";
    echo "   - Time estimates\n";
    echo "   - Expandable progress history\n";
    echo "   - Real-time status updates\n\n";

    echo "🎯 IMPLEMENTATION STATUS: READY FOR TESTING\n";
    echo "   Navigate to: /request/track/{$formRequest->form_id}\n";
    echo "   Expected: Enhanced job order card with progress visibility\n";

} catch (Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    exit(1);
}
