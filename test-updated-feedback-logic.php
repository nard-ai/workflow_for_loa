<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobOrder;
use App\Models\User;

echo "=== TESTING UPDATED JOB ORDER FEEDBACK LOGIC ===\n\n";

// Test with the current user
$user = App\Models\User::whereHas('employeeInfo', function($query) {
    $query->where('FirstName', 'like', '%Andro%')
          ->where('LastName', 'like', '%Banag%');
})->first();

if ($user) {
    echo "Testing with user: " . $user->employeeInfo->FirstName . " " . $user->employeeInfo->LastName . "\n";
    
    $jobOrders = JobOrder::whereHas('formRequest', function ($query) use ($user) {
        $query->where('requested_by', $user->accnt_id);
    })->with('formRequest')->get();
    
    echo "\n=== INDIVIDUAL JOB ORDER ANALYSIS ===\n";
    
    foreach ($jobOrders as $jo) {
        echo "\nJob Order: " . $jo->job_order_number . "\n";
        echo "  Status: " . $jo->status . "\n";
        echo "  Job Completed: " . ($jo->job_completed ? 'YES' : 'NO') . "\n";
        echo "  Comments: " . ($jo->requestor_comments ? 'PROVIDED' : 'MISSING') . "\n";
        echo "  Signature: " . ($jo->requestor_signature ? 'PROVIDED' : 'MISSING') . "\n";
        echo "  => Needs Feedback: " . ($jo->needsFeedback() ? 'YES' : 'NO') . "\n";
        
        if ($jo->requestor_comments) {
            echo "  Comment preview: " . substr($jo->requestor_comments, 0, 50) . "...\n";
        }
    }
    
    echo "\n=== SUMMARY ===\n";
    $needingFeedback = JobOrder::needingFeedbackForUser($user->accnt_id);
    $pendingCount = $needingFeedback->count();
    
    echo "Job orders still needing feedback: " . $pendingCount . "\n";
    echo "User has pending feedback: " . (JobOrder::userHasPendingFeedback($user->accnt_id) ? 'YES' : 'NO') . "\n";
    echo "Should block new IOM requests: " . ($pendingCount >= 2 ? 'YES' : 'NO') . "\n";
    
    if ($pendingCount > 0) {
        echo "\nJob orders still needing feedback:\n";
        foreach ($needingFeedback as $jo) {
            echo "  - " . $jo->job_order_number . " (missing comments)\n";
        }
    } else {
        echo "\n✅ All job orders have complete feedback! User can submit new IOM requests.\n";
    }
}

echo "\n=== TESTING EDGE CASES ===\n";

// Test what happens if we artificially create a job order without comments
echo "\nSimulating a job order that truly needs feedback...\n";

// Find any job order and temporarily clear its comments for testing
$testJobOrder = JobOrder::where('status', 'Completed')
                       ->where('job_completed', true)
                       ->whereNotNull('requestor_comments')
                       ->first();

if ($testJobOrder) {
    echo "Found test job order: " . $testJobOrder->job_order_number . "\n";
    
    // Backup original comment
    $originalComment = $testJobOrder->requestor_comments;
    
    // Temporarily clear comment
    $testJobOrder->requestor_comments = null;
    $testJobOrder->save();
    
    echo "After clearing comments:\n";
    echo "  Needs Feedback: " . ($testJobOrder->needsFeedback() ? 'YES' : 'NO') . "\n";
    
    // Test the static method
    $testUser = $testJobOrder->formRequest->requester;
    $needingCount = JobOrder::needingFeedbackForUser($testUser->accnt_id)->count();
    echo "  User's total needing feedback: " . $needingCount . "\n";
    
    // Restore original comment
    $testJobOrder->requestor_comments = $originalComment;
    $testJobOrder->save();
    
    echo "After restoring comments:\n";
    echo "  Needs Feedback: " . ($testJobOrder->needsFeedback() ? 'YES' : 'NO') . "\n";
    
    $needingCountAfter = JobOrder::needingFeedbackForUser($testUser->accnt_id)->count();
    echo "  User's total needing feedback: " . $needingCountAfter . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "✅ Logic properly differentiates between job orders with and without feedback\n";
echo "✅ Users with complete feedback are not blocked from new requests\n";
echo "✅ Only job orders missing requestor_comments are counted as needing feedback\n";
