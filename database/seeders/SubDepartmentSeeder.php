<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sub_departments')->insert([
            [
                'subdepartment_code' => 'PFMO-CONS',
                'name' => 'Construction',
                'description' => 'Responsible for all fabrication, building, and structural maintenance tasks.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subdepartment_code' => 'PFMO-HOUSEKEEPING',
                'name' => 'Housekeeping',
                'description' => 'Manages cleanliness, sanitation, and general upkeep of facilities.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subdepartment_code' => 'PFMO-GEN',
                'name' => 'General Services',
                'description' => 'Handles repairs and maintenance of equipment, furniture, and utilities.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
