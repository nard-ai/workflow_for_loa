<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/', 'GET');
$kernel->handle($request);

use App\Models\JobOrder;

$jobOrder = JobOrder::find(3);
if ($jobOrder) {
    $jobOrder->status = 'In Progress';
    $jobOrder->save();
    echo "✅ Job order reset to 'In Progress' status for testing\n";
    echo "Current status: {$jobOrder->status}\n";
} else {
    echo "❌ Job order not found\n";
}
