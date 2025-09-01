<?php

echo "=== CHECKING DATABASE TABLES ===\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== ALL TABLES IN DATABASE ===\n";
    $tables = DB::select('SHOW TABLES');
    $dbName = DB::connection()->getDatabaseName();
    
    foreach ($tables as $table) {
        $tableName = $table->{"Tables_in_$dbName"};
        echo "- $tableName\n";
    }
    
    echo "\n=== CHECKING FOR DEPARTMENT-RELATED TABLES ===\n";
    foreach ($tables as $table) {
        $tableName = $table->{"Tables_in_$dbName"};
        if (stripos($tableName, 'dept') !== false || stripos($tableName, 'department') !== false) {
            echo "Found department-related table: $tableName\n";
            
            // Check its structure
            $columns = DB::select("DESCRIBE $tableName");
            foreach ($columns as $column) {
                echo "  - {$column->Field}: {$column->Type}\n";
            }
            
            // Check sample data
            $sampleData = DB::table($tableName)->take(3)->get();
            echo "  Sample data (" . count($sampleData) . " records):\n";
            foreach ($sampleData as $record) {
                $values = [];
                foreach ((array)$record as $key => $value) {
                    $values[] = "$key=" . ($value ?? 'NULL');
                }
                echo "    " . implode(', ', $values) . "\n";
            }
            echo "\n";
        }
    }
    
    echo "\n=== CHECKING FORM_REQUESTS TABLE FOR DEPARTMENT REFERENCES ===\n";
    $formRequestColumns = DB::select("DESCRIBE form_requests");
    foreach ($formRequestColumns as $column) {
        if (stripos($column->Field, 'dept') !== false || stripos($column->Field, 'department') !== false) {
            echo "Found department column: {$column->Field} ({$column->Type})\n";
        }
    }
    
    // Check what department IDs are being used
    echo "\n=== CHECKING DEPARTMENT IDs IN FORM_REQUESTS ===\n";
    $uniqueDeptIds = DB::select("
        SELECT DISTINCT from_department_id, to_department_id 
        FROM form_requests 
        WHERE from_department_id IS NOT NULL OR to_department_id IS NOT NULL
    ");
    
    foreach ($uniqueDeptIds as $ids) {
        echo "- from_department_id: " . ($ids->from_department_id ?? 'NULL') . 
             ", to_department_id: " . ($ids->to_department_id ?? 'NULL') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDatabase check completed.\n";
