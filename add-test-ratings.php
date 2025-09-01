<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JobOrder;
use Illuminate\Support\Facades\DB;

// Add some test rating data
echo "=== ADDING TEST RATING DATA ===\n\n";

try {
    // Get some completed job orders
    $jobOrders = JobOrder::where('status', 'Completed')
        ->whereNotNull('requestor_comments')
        ->take(3)
        ->get();
    
    if ($jobOrders->count() > 0) {
        echo "Adding test ratings to existing job orders...\n\n";
        
        $testRatings = [5, 4, 3]; // Different ratings for testing
        
        foreach ($jobOrders as $index => $jobOrder) {
            $rating = $testRatings[$index % count($testRatings)];
            
            $jobOrder->update([
                'requestor_satisfaction_rating' => $rating,
                'requestor_feedback_submitted' => true,
                'requestor_feedback_date' => now()->subHours(rand(1, 24))
            ]);
            
            echo "✅ Updated {$jobOrder->job_order_number} with {$rating} star rating\n";
        }
        
        echo "\n=== TESTING UPDATED DATA ===\n\n";
        
        // Test the feedback service again
        $stats = \App\Services\PFMOFeedbackService::getFeedbackStatistics();
        echo "Updated Statistics:\n";
        echo "   Average rating: {$stats['average_rating']}\n";
        echo "   Total feedback: {$stats['total_feedback']}\n";
        echo "   Completion rate: {$stats['completion_rate']}%\n\n";
        
        $recentFeedback = \App\Services\PFMOFeedbackService::getRecentFeedback(5);
        echo "Recent feedback with ratings:\n";
        foreach ($recentFeedback as $feedback) {
            $stars = str_repeat('⭐', $feedback['rating']);
            echo "   {$feedback['job_order_number']}: {$stars} ({$feedback['rating']}/5)\n";
            echo "   \"{$feedback['comments']}\"\n\n";
        }
        
        $distribution = \App\Services\PFMOFeedbackService::getRatingDistribution();
        echo "Rating distribution:\n";
        foreach ($distribution as $rating) {
            if ($rating['count'] > 0) {
                $stars = str_repeat('⭐', $rating['stars']);
                echo "   {$stars} ({$rating['stars']} star): {$rating['count']} ratings ({$rating['percentage']}%)\n";
            }
        }
        
        echo "\n✅ TEST DATA ADDED SUCCESSFULLY!\n";
        echo "🎯 PFMO Dashboard will now show feedback data with star ratings!\n";
        
    } else {
        echo "❌ No completed job orders with comments found for testing\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error adding test data: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
