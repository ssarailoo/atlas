<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $ceo = Employee::factory()->create([
            'full_name' => 'Corporate Overlord',
            'role' => 'ceo',
            'manager_id' => null,
        ]);

        $managers = Employee::factory()->count(3)->create([
            'role' => 'manager',
            'manager_id' => $ceo->id,
        ]);


        foreach ($managers as $manager) {
            Employee::factory()->count(5)->create([
                'role' => 'employee',
                'manager_id' => $manager->id,
            ]);
        }


        Employee::factory()->create([
            'full_name' => 'HR Guardian',
            'role' => 'hr',
            'manager_id' => $ceo->id,
        ]);
    }
}
