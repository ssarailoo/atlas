<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $ceo = Employee::factory()->create([
            'full_name' => 'Corporate Overlord',
            'role' => RoleEnum::CEO->value,
            'manager_id' => null,
        ]);


        $managers = Employee::factory()->count(3)->create([
            'role' =>RoleEnum::MANAGER,
            'manager_id' => $ceo->id,
        ]);


        foreach ($managers as $manager) {
            Employee::factory()->count(5)->create([
                'role' => RoleEnum::EMPLOYEE,
                'manager_id' => $manager->id,
            ]);
        }


        Employee::factory()->create([
            'full_name' => 'HR Guardian',
            'role' => RoleEnum::HR,
            'manager_id' => $ceo->id,
        ]);
    }
}
