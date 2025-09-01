<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobOrder;
use App\Models\User;

echo "=== DEBUGGING PENDING JOB ORDER FILLUP LOGIC ===\n";

// Find the current user from the screenshots - looks like an IOM request
$users = App\Models\User::whereHas('employeeInfo', function($query) {
    $query->where('FirstName', 'like', '%Andro%')
          ->orWhere('LastName', 'like', '%Banag%');
})->get();

if ($users->count() > 0) {
    $user = $users->first();
    echo "Found user: " . $user->employeeInfo->FirstName . " " . $user->employeeInfo->LastName . " (ID: " . $user->accnt_id . ")\n";
    
    echo "\n=== CHECKING JOB ORDERS FOR THIS USER ===\n";
    
    $allJobOrders = JobOrder::whereHas('formRequest', function ($query) use ($user) {
        $query->where('requested_by', $user->accnt_id);
    })->with('formRequest')->get();
    
    echo "Total job orders for user: " . $allJobOrders->count() . "\n";
    
    foreach ($allJobOrders as $jo) {
        echo "\nJob Order: " . $jo->job_order_number . "\n";
        echo "  Status: " . $jo->status . "\n";
        echo "  Job Completed: " . ($jo->job_completed ? 'YES' : 'NO') . "\n";
        echo "  Needs Feedback: " . ($jo->needsFeedback() ? 'YES' : 'NO') . "\n";
        echo "  Form Request Status: " . $jo->formRequest->status . "\n";
        echo "  Has requestor_comments: " . ($jo->requestor_comments ? 'YES' : 'NO') . "\n";
        echo "  Has requestor_signature: " . ($jo->requestor_signature ? 'YES' : 'NO') . "\n";
        echo "  Date Completed: " . ($jo->date_completed ? $jo->date_completed : 'NULL') . "\n";
        echo "  Job Completed At: " . ($jo->job_completed_at ? $jo->job_completed_at : 'NULL') . "\n";
    }
    
    echo "\n=== TESTING LOGIC METHODS ===\n";
    
    $needingFeedback = JobOrder::needingFeedbackForUser($user->accnt_id);
    echo "needingFeedbackForUser count: " . $needingFeedback->count() . "\n";
    
    $hasPending = JobOrder::userHasPendingFeedback($user->accnt_id);
    echo "userHasPendingFeedback: " . ($hasPending ? 'YES' : 'NO') . "\n";
    
    if ($needingFeedback->count() > 0) {
        echo "\nJob orders needing feedback:\n";
        foreach ($needingFeedback as $jo) {
            echo "  - " . $jo->job_order_number . " (Status: " . $jo->status . ", Job Completed: " . ($jo->job_completed ? 'YES' : 'NO') . ")\n";
        }
    }
    
    echo "\n=== SHOULD USER BE BLOCKED? ===\n";
    $pendingCount = JobOrder::needingFeedbackForUser($user->accnt_id)->count();
    echo "Pending fillup count: " . $pendingCount . "\n";
    echo "Should be blocked (>=2): " . ($pendingCount >= 2 ? 'YES' : 'NO') . "\n";
    
} else {
    echo "User not found with name Andro Banag\n";
    
    echo "\n=== CHECKING ALL USERS WITH PENDING FEEDBACK ===\n";
    $allUsers = User::whereHas('employeeInfo')->with('employeeInfo')->get();
    
    foreach ($allUsers as $user) {
        $hasPending = JobOrder::userHasPendingFeedback($user->accnt_id);
        if ($hasPending) {
            $count = JobOrder::needingFeedbackForUser($user->accnt_id)->count();
            echo $user->employeeInfo->FirstName . " " . $user->employeeInfo->LastName . " has " . $count . " pending feedback\n";
        }
    }
}
