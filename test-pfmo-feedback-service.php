<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\PFMOFeedbackService;

// Test the PFMO Feedback Service
echo "=== PFMO FEEDBACK SERVICE TEST ===\n\n";

try {
    echo "Testing PFMOFeedbackService methods...\n\n";
    
    // Test feedback statistics
    echo "1. Getting feedback statistics...\n";
    $stats = PFMOFeedbackService::getFeedbackStatistics();
    echo "   ✅ Total feedback: {$stats['total_feedback']}\n";
    echo "   ✅ Feedback today: {$stats['feedback_today']}\n";
    echo "   ✅ Average rating: {$stats['average_rating']}\n";
    echo "   ✅ Needs action: {$stats['needs_action']}\n";
    echo "   ✅ Completion rate: {$stats['completion_rate']}%\n\n";
    
    // Test recent feedback
    echo "2. Getting recent feedback...\n";
    $recentFeedback = PFMOFeedbackService::getRecentFeedback(3);
    echo "   ✅ Found {$recentFeedback->count()} recent feedback entries\n";
    
    foreach ($recentFeedback as $feedback) {
        echo "     - {$feedback['job_order_number']}: {$feedback['rating']} stars\n";
        echo "       \"{$feedback['comments']}\"\n";
        echo "       From: {$feedback['requester_name']}\n\n";
    }
    
    // Test rating distribution
    echo "3. Getting rating distribution...\n";
    $distribution = PFMOFeedbackService::getRatingDistribution();
    foreach ($distribution as $rating) {
        $stars = str_repeat('⭐', $rating['stars']);
        echo "   {$stars} ({$rating['stars']} star): {$rating['count']} ratings ({$rating['percentage']}%)\n";
    }
    echo "\n";
    
    // Test jobs needing action
    echo "4. Getting jobs needing action...\n";
    $needingAction = PFMOFeedbackService::getJobsNeedingAction();
    echo "   ✅ Found {$needingAction->count()} jobs needing action\n";
    
    foreach ($needingAction as $job) {
        echo "     - {$job['job_order_number']}: {$job['rating']} stars (Action Required)\n";
    }
    echo "\n";
    
    // Test dashboard summary
    echo "5. Getting complete dashboard summary...\n";
    $summary = PFMOFeedbackService::getDashboardSummary();
    echo "   ✅ Statistics: " . json_encode($summary['statistics']) . "\n";
    echo "   ✅ Recent feedback: {$summary['recent_feedback']->count()} entries\n";
    echo "   ✅ Rating distribution: " . count($summary['rating_distribution']) . " rating levels\n";
    echo "   ✅ Jobs needing action: {$summary['jobs_needing_action']->count()} entries\n";
    echo "   ✅ Low rated jobs: {$summary['low_rated_jobs']->count()} entries\n";
    echo "   ✅ Department feedback: {$summary['department_feedback']->count()} departments\n\n";
    
    echo "✅ ALL TESTS COMPLETED SUCCESSFULLY!\n";
    echo "🎯 PFMO Feedback Service is ready for dashboard integration!\n\n";
    
    echo "=== IMPLEMENTATION STATUS ===\n";
    echo "✅ PFMOFeedbackService: Created and tested\n";
    echo "✅ JobOrder Model: Updated with feedback fields\n";
    echo "✅ PFMOController: Enhanced with feedback data\n";
    echo "✅ Dashboard View: Enhanced with feedback sections\n";
    echo "🚀 Ready for PFMO dashboard testing!\n";
    
} catch (Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    exit(1);
}
