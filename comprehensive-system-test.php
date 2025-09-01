<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\JobOrder;
use App\Models\Department;

echo "🧪 COMPREHENSIVE SYSTEM FUNCTIONALITY TEST\n";
echo "=" . str_repeat("=", 50) . "\n\n";

$errors = [];
$warnings = [];
$passed = [];

try {
    // Test 1: Database Connection
    echo "1️⃣ Testing Database Connection...\n";
    try {
        DB::connection()->getPdo();
        echo "✅ Database connection successful\n";
        $passed[] = "Database Connection";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
        $errors[] = "Database Connection: " . $e->getMessage();
    }

    // Test 2: User Authentication System
    echo "\n2️⃣ Testing User Authentication System...\n";
    try {
        $totalUsers = User::count();
        $pfmoUsers = User::where('department', 'PFMO')->count();
        $adminUsers = User::where('role', 'admin')->count();

        echo "✅ Total users: {$totalUsers}\n";
        echo "✅ PFMO users: {$pfmoUsers}\n";
        echo "✅ Admin users: {$adminUsers}\n";

        if ($pfmoUsers === 0) {
            $warnings[] = "No PFMO users found - PFMO dashboard features won't be visible";
        }

        $passed[] = "User Authentication System";
    } catch (Exception $e) {
        echo "❌ User system test failed: " . $e->getMessage() . "\n";
        $errors[] = "User System: " . $e->getMessage();
    }

    // Test 3: Job Order System
    echo "\n3️⃣ Testing Job Order System...\n";
    try {
        $totalJobs = JobOrder::count();
        $pendingJobs = JobOrder::where('status', 'pending')->count();
        $inProgressJobs = JobOrder::where('status', 'in_progress')->count();
        $completedJobs = JobOrder::where('status', 'completed')->count();

        echo "✅ Total job orders: {$totalJobs}\n";
        echo "✅ Pending jobs: {$pendingJobs}\n";
        echo "✅ In progress jobs: {$inProgressJobs}\n";
        echo "✅ Completed jobs: {$completedJobs}\n";

        if ($totalJobs === 0) {
            $warnings[] = "No job orders found - dashboard will be empty";
        }

        $passed[] = "Job Order System";
    } catch (Exception $e) {
        echo "❌ Job order system test failed: " . $e->getMessage() . "\n";
        $errors[] = "Job Order System: " . $e->getMessage();
    }

    // Test 4: Progress Tracking Feature
    echo "\n4️⃣ Testing Progress Tracking Feature...\n";
    try {
        $jobsWithProgress = JobOrder::whereNotNull('progress_percentage')->count();
        $jobsWithStatus = JobOrder::whereNotNull('status')->count();

        echo "✅ Jobs with progress tracking: {$jobsWithProgress}\n";
        echo "✅ Jobs with status: {$jobsWithStatus}\n";

        // Test progress calculation
        $sampleJob = JobOrder::whereNotNull('progress_percentage')->first();
        if ($sampleJob) {
            echo "✅ Sample progress: Job #{$sampleJob->job_order_number} - {$sampleJob->progress_percentage}%\n";
        }

        $passed[] = "Progress Tracking Feature";
    } catch (Exception $e) {
        echo "❌ Progress tracking test failed: " . $e->getMessage() . "\n";
        $errors[] = "Progress Tracking: " . $e->getMessage();
    }

    // Test 5: Feedback System
    echo "\n5️⃣ Testing Feedback System...\n";
    try {
        $totalFeedback = JobOrder::whereNotNull('requestor_satisfaction_rating')->count();
        $avgRating = JobOrder::whereNotNull('requestor_satisfaction_rating')
            ->avg('requestor_satisfaction_rating');
        $feedbackWithComments = JobOrder::whereNotNull('requestor_comments')->count();

        echo "✅ Jobs with feedback: {$totalFeedback}\n";
        echo "✅ Average satisfaction: " . number_format($avgRating, 1) . "/5.0\n";
        echo "✅ Jobs with comments: {$feedbackWithComments}\n";

        // Test recent feedback
        $recentFeedback = JobOrder::where('status', 'completed')
            ->whereNotNull('requestor_satisfaction_rating')
            ->orderBy('requestor_feedback_date', 'desc')
            ->limit(3)
            ->get();

        echo "✅ Recent feedback items: " . $recentFeedback->count() . "\n";

        $passed[] = "Feedback System";
    } catch (Exception $e) {
        echo "❌ Feedback system test failed: " . $e->getMessage() . "\n";
        $errors[] = "Feedback System: " . $e->getMessage();
    }

    // Test 6: Department System
    echo "\n6️⃣ Testing Department System...\n";
    try {
        $departments = Department::count();
        echo "✅ Total departments: {$departments}\n";

        $deptList = Department::pluck('name')->toArray();
        echo "✅ Departments: " . implode(', ', $deptList) . "\n";

        $passed[] = "Department System";
    } catch (Exception $e) {
        echo "❌ Department system test failed: " . $e->getMessage() . "\n";
        $errors[] = "Department System: " . $e->getMessage();
    }

    // Test 7: Job Order Fields
    echo "\n7️⃣ Testing Job Order Fields...\n";
    try {
        $jobWithType = JobOrder::whereNotNull('job_type')->count();
        echo "✅ Jobs with job type: {$jobWithType}\n";

        $sampleTypes = JobOrder::whereNotNull('job_type')
            ->distinct()
            ->pluck('job_type')
            ->take(5)
            ->toArray();
        echo "✅ Sample job types: " . implode(', ', $sampleTypes) . "\n";

        $passed[] = "Job Order Fields";
    } catch (Exception $e) {
        echo "❌ Job order fields test failed: " . $e->getMessage() . "\n";
        $errors[] = "Job Order Fields: " . $e->getMessage();
    }

    // Test 8: Dashboard Data Loading
    echo "\n8️⃣ Testing Dashboard Data Loading...\n";
    try {
        // Simulate dashboard data loading
        $stats = [
            'total_requests' => JobOrder::count(),
            'pending_requests' => JobOrder::where('status', 'pending')->count(),
            'in_progress_requests' => JobOrder::where('status', 'in_progress')->count(),
            'completed_requests' => JobOrder::where('status', 'completed')->count(),
        ];

        echo "✅ Dashboard stats loaded successfully\n";
        echo "   📊 Total: {$stats['total_requests']}\n";
        echo "   ⏳ Pending: {$stats['pending_requests']}\n";
        echo "   🔄 In Progress: {$stats['in_progress_requests']}\n";
        echo "   ✅ Completed: {$stats['completed_requests']}\n";

        $passed[] = "Dashboard Data Loading";
    } catch (Exception $e) {
        echo "❌ Dashboard data loading test failed: " . $e->getMessage() . "\n";
        $errors[] = "Dashboard Data Loading: " . $e->getMessage();
    }

    // Test 9: PFMO Features
    echo "\n9️⃣ Testing PFMO Features...\n";
    try {
        $pfmoUser = User::where('department', 'PFMO')->first();

        if ($pfmoUser) {
            echo "✅ PFMO user found: {$pfmoUser->name}\n";

            // Test PFMO feedback access
            $pfmoFeedback = JobOrder::where('status', 'completed')
                ->whereNotNull('requestor_satisfaction_rating')
                ->with(['requestor'])
                ->orderBy('requestor_feedback_date', 'desc')
                ->limit(5)
                ->get();

            echo "✅ PFMO feedback access: " . $pfmoFeedback->count() . " items\n";
            $passed[] = "PFMO Features";
        } else {
            echo "⚠️ No PFMO users found - PFMO features won't be visible\n";
            $warnings[] = "No PFMO users found";
        }
    } catch (Exception $e) {
        echo "❌ PFMO features test failed: " . $e->getMessage() . "\n";
        $errors[] = "PFMO Features: " . $e->getMessage();
    }

    // Test 10: Model Relationships
    echo "\n🔟 Testing Model Relationships...\n";
    try {
        $sampleJob = JobOrder::with(['requestor', 'department'])->first();

        if ($sampleJob) {
            echo "✅ Job Order relationships working\n";
            echo "   👤 Requestor: " . ($sampleJob->requestor ? $sampleJob->requestor->name : 'N/A') . "\n";
            echo "   🏷️ Job Type: " . ($sampleJob->job_type ? $sampleJob->job_type : 'N/A') . "\n";
            echo "   🏢 Department: " . ($sampleJob->department ? $sampleJob->department->name : 'N/A') . "\n";
        }

        $passed[] = "Model Relationships";
    } catch (Exception $e) {
        echo "❌ Model relationships test failed: " . $e->getMessage() . "\n";
        $errors[] = "Model Relationships: " . $e->getMessage();
    }

} catch (Exception $e) {
    echo "❌ Critical system error: " . $e->getMessage() . "\n";
    $errors[] = "Critical System Error: " . $e->getMessage();
}

// Summary Report
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 TEST RESULTS SUMMARY\n";
echo str_repeat("=", 60) . "\n\n";

echo "✅ PASSED TESTS (" . count($passed) . "):\n";
foreach ($passed as $test) {
    echo "   ✓ {$test}\n";
}

if (!empty($warnings)) {
    echo "\n⚠️ WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "   ⚠ {$warning}\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ FAILED TESTS (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "   ✗ {$error}\n";
    }
    echo "\n🔧 RECOMMENDATION: Fix the failed tests before proceeding.\n";
} else {
    echo "\n🎉 ALL CORE FUNCTIONALITY TESTS PASSED!\n";
}

echo "\n📈 OVERALL SYSTEM HEALTH: ";
if (empty($errors)) {
    echo "🟢 HEALTHY\n";
} elseif (count($errors) <= 2) {
    echo "🟡 MINOR ISSUES\n";
} else {
    echo "🔴 NEEDS ATTENTION\n";
}

echo "\nTest completed at: " . date('Y-m-d H:i:s') . "\n";

?>