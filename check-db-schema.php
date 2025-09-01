<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking job_orders table schema...\n\n";

$result = DB::select('DESCRIBE job_orders');
foreach($result as $column) {
    if(in_array($column->Field, ['request_description', 'requestor_signature', 'requestor_signature_date', 'status'])) {
        echo $column->Field . ' - Type: ' . $column->Type . ' - Null: ' . $column->Null . ' - Default: ' . $column->Default . "\n";
    }
}

echo "\nChecking current job order data...\n";
$jobOrder = \App\Models\JobOrder::first();
if ($jobOrder) {
    echo "Sample job order request_description: '" . $jobOrder->request_description . "'\n";
    echo "Sample job order status: '" . $jobOrder->status . "'\n";
}
