<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JobOrder;
use Illuminate\Support\Facades\Schema;

echo "=== JOB ORDER FEEDBACK DATA ANALYSIS ===\n\n";

try {
    // Get table columns to see what feedback fields exist
    $columns = Schema::getColumnListing('job_orders');
    echo "📋 Available columns in job_orders table:\n";

    $feedbackColumns = [];
    foreach ($columns as $column) {
        if (strpos($column, 'requestor') !== false || strpos($column, 'satisfaction') !== false || strpos($column, 'rating') !== false || strpos($column, 'feedback') !== false) {
            $feedbackColumns[] = $column;
            echo "   ✅ $column (feedback-related)\n";
        }
    }

    if (empty($feedbackColumns)) {
        echo "   ❌ No obvious feedback columns found\n";
        echo "   Looking for comments and signature fields...\n";

        foreach ($columns as $column) {
            if (strpos($column, 'comment') !== false || strpos($column, 'signature') !== false) {
                $feedbackColumns[] = $column;
                echo "   ✅ $column (comment/signature)\n";
            }
        }
    }

    echo "\n📊 Sample job order with feedback data:\n";

    // Get a job order with feedback
    $jobOrder = JobOrder::where('status', 'Completed')
        ->whereNotNull('requestor_comments')
        ->first();

    if ($jobOrder) {
        echo "Job Order: {$jobOrder->job_order_number}\n";
        echo "Status: {$jobOrder->status}\n";
        echo "Requestor Comments: " . ($jobOrder->requestor_comments ? 'YES' : 'NO') . "\n";

        // Check if satisfaction rating field exists
        $attributes = $jobOrder->getAttributes();
        foreach ($attributes as $key => $value) {
            if (strpos($key, 'satisfaction') !== false || strpos($key, 'rating') !== false) {
                echo "Rating Field: $key = $value\n";
            }
        }

        if (isset($attributes['requestor_satisfaction_rating'])) {
            echo "Satisfaction Rating: {$attributes['requestor_satisfaction_rating']}\n";
        } else {
            echo "❌ No satisfaction rating field found\n";
        }

        echo "Requestor: " . ($jobOrder->formRequest ? $jobOrder->formRequest->requester->employeeInfo->FirstName . ' ' . $jobOrder->formRequest->requester->employeeInfo->LastName : 'N/A') . "\n";
        echo "Job Type: " . ($jobOrder->formRequest ? $jobOrder->formRequest->iomDetails?->purpose : 'N/A') . "\n";
        echo "Date Completed: " . ($jobOrder->date_completed ? $jobOrder->date_completed->format('M j, Y') : 'N/A') . "\n";

        if ($jobOrder->requestor_comments) {
            echo "Comments Preview: " . substr($jobOrder->requestor_comments, 0, 100) . "...\n";
        }
    } else {
        echo "❌ No completed job orders with feedback found\n";
    }

    echo "\n🔍 Total job orders with feedback:\n";
    $withFeedback = JobOrder::where('status', 'Completed')
        ->whereNotNull('requestor_comments')
        ->count();

    $totalCompleted = JobOrder::where('status', 'Completed')->count();

    echo "Completed job orders: $totalCompleted\n";
    echo "With feedback: $withFeedback\n";
    echo "Feedback rate: " . ($totalCompleted > 0 ? round(($withFeedback / $totalCompleted) * 100) : 0) . "%\n";

    echo "\n🎯 PFMO Dashboard Feedback Plan:\n";
    echo "✅ Show recent job order feedback for PFMO users\n";
    echo "✅ Display: Job Order Number, Comments, Requestor Name, Date\n";
    echo "✅ Include job type/purpose from IOM details\n";
    echo "✅ Show 5-10 most recent feedback entries\n";
    echo "✅ Filter by PFMO department users only\n";

    if (in_array('requestor_satisfaction_rating', $feedbackColumns)) {
        echo "✅ Include star ratings in display\n";
    } else {
        echo "⚠️ No star rating field found - will show comments only\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
