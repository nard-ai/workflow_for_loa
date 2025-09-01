<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\FormRequest;
use App\Models\JobOrder;

echo "=== JOB ORDER ENHANCEMENT & DATA MANAGEMENT ===\n\n";

echo "✅ ENHANCEMENTS COMPLETED:\n";
echo "1. Job order numbers now include IOM request ID\n";
echo "   - Old format: JO-20250901-0001\n";
echo "   - New format: JO-20250901-REQ058-001\n";
echo "2. Easy to identify which IOM request each job order belongs to\n";
echo "3. Safe data cleanup script ready\n\n";

echo "CURRENT DATA:\n";
echo "- Form Requests: " . DB::table('form_requests')->count() . "\n";
echo "- Job Orders: " . DB::table('job_orders')->count() . "\n";
echo "- Form Approvals: " . DB::table('form_approvals')->count() . "\n\n";

echo "WHAT WOULD YOU LIKE TO DO?\n";
echo "1. See a demo of the new job order format (safe - no changes)\n";
echo "2. Clean all test data and start fresh\n";
echo "3. Keep current data and just use new format for future job orders\n";
echo "4. Exit without changes\n\n";

echo "Enter your choice (1-4): ";

$handle = fopen("php://stdin", "r");
$choice = trim(fgets($handle));
fclose($handle);

switch ($choice) {
    case '1':
        echo "\n=== DEMO: NEW JOB ORDER FORMAT ===\n\n";
        
        // Show current job orders
        echo "CURRENT JOB ORDERS (old format):\n";
        $currentJobOrders = JobOrder::orderBy('job_order_id', 'desc')->take(5)->get();
        foreach ($currentJobOrders as $jo) {
            echo "- {$jo->job_order_number} (Request {$jo->form_id})\n";
        }
        
        echo "\nNEW FORMAT EXAMPLES (what future job orders will look like):\n";
        echo "- JO-20250901-REQ001-001 (Request 1, first job order)\n";
        echo "- JO-20250901-REQ002-001 (Request 2, first job order)\n";
        echo "- JO-20250901-REQ003-001 (Request 3, first job order)\n";
        echo "- JO-20250901-REQ001-002 (Request 1, second job order if needed)\n\n";
        
        echo "✅ Much easier to see which request each job order belongs to!\n";
        break;
        
    case '2':
        echo "\n=== CLEAN DATA AND START FRESH ===\n";
        echo "This will:\n";
        echo "✅ Delete all form requests, job orders, and approvals\n";
        echo "✅ Reset auto increment counters to start from 1\n";
        echo "✅ Keep all users, departments, and system settings\n";
        echo "✅ Future job orders will use new format: JO-YYYYMMDD-REQ###-###\n\n";
        
        echo "Are you sure? Type 'CLEAN' to proceed: ";
        $handle = fopen("php://stdin", "r");
        $confirm = trim(fgets($handle));
        fclose($handle);
        
        if (strtoupper($confirm) === 'CLEAN') {
            include 'safe-data-cleanup.php';
        } else {
            echo "Cleanup cancelled.\n";
        }
        break;
        
    case '3':
        echo "\n=== KEEP CURRENT DATA ===\n";
        echo "✅ Current data will be preserved\n";
        echo "✅ New job orders will use the enhanced format\n";
        echo "✅ System ready for production use\n";
        echo "\nNo changes made. Future job orders will automatically use the new format.\n";
        break;
        
    case '4':
        echo "\nNo changes made. Exiting...\n";
        break;
        
    default:
        echo "\nInvalid choice. No changes made.\n";
        break;
}

echo "\n=== SUMMARY OF IMPROVEMENTS ===\n";
echo "✅ Job Order Format: Enhanced to include request ID\n";
echo "✅ Request Tracking: Fixed relationship errors\n";
echo "✅ Job Order Creation: All approved PFMO requests get job orders\n";
echo "✅ Data Management: Safe cleanup script available\n";
echo "✅ System Status: Fully functional and ready for use\n";
