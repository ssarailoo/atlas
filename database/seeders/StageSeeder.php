<?php
namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    public function run(): void
    {

        $reviewHR = Stage::create([
            'name' => 'Review HR',
            'role' => 'hr',
            'order' => 1,
            'min_days' => 0,
            'next_stage_id' => null,
        ]);


        $managerApproval = Stage::create([
            'name' => 'Manager Approval',
            'role' => 'manager',
            'order' => 2,
            'min_days' => 3,
            'next_stage_id' => null,
        ]);


        $ceoApproval = Stage::create([
            'name' => 'CEO Approval',
            'role' => 'ceo',
            'order' => 3,
            'min_days' => 5,
            'next_stage_id' => null,
        ]);

        $reviewHR->next_stage_id = $managerApproval->id;
        $reviewHR->save();

        $managerApproval->next_stage_id = $ceoApproval->id;
        $managerApproval->save();
    }
}
