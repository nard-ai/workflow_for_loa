<?php

echo "=== CHECKING DEPARTMENTS TABLE STRUCTURE ===\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== TABLE STRUCTURE ===\n";
    $columns = DB::select("DESCRIBE departments");
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type} " . ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . " " . ($column->Key ? "({$column->Key})" : '') . "\n";
    }
    
    echo "\n=== SAMPLE DEPARTMENT DATA ===\n";
    $departments = DB::table('departments')->take(5)->get();
    foreach ($departments as $dept) {
        echo "Department: ";
        foreach ((array)$dept as $key => $value) {
            echo "$key=" . ($value ?? 'NULL') . " ";
        }
        echo "\n";
    }
    
    echo "\n=== DEPARTMENT MODEL PRIMARY KEY ===\n";
    $deptModel = new \App\Models\Department();
    echo "Primary key: " . $deptModel->getKeyName() . "\n";
    echo "Key type: " . $deptModel->getKeyType() . "\n";
    echo "Incrementing: " . ($deptModel->getIncrementing() ? 'YES' : 'NO') . "\n";
    
    echo "\n=== CHECKING PFMO DEPARTMENT SPECIFICALLY ===\n";
    $pfmo = DB::table('departments')->where('dept_code', 'PFMO')->first();
    if ($pfmo) {
        echo "PFMO department found:\n";
        foreach ((array)$pfmo as $key => $value) {
            echo "  $key: " . ($value ?? 'NULL') . "\n";
        }
    } else {
        echo "PFMO department not found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTable check completed.\n";
