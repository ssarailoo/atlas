<?php

namespace Tests\Feature;

use App\Enums\LeaveRequestStatusEnum;
use App\Enums\LeaveRequestTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Stage;
use Database\Seeders\StageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeaveRequestApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

    }

    private function makeEmployee(RoleEnum $role, ?int $managerId = null): Employee
    {
        return Employee::factory()->create([
            'role' => $role,
            'manager_id' => $managerId,
        ]);
    }

    private function makeLeave(Employee $employee, Stage $stage): LeaveRequest
    {
        $status = match ($stage->role) {
            RoleEnum::HR->value => LeaveRequestStatusEnum::PENDING_HR->value,
            RoleEnum::MANAGER->value => LeaveRequestStatusEnum::PENDING_MANAGER->value,
            RoleEnum::CEO->value => LeaveRequestStatusEnum::PENDING_CEO->value,
        };

        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'status' => $status,
            'type' => LeaveRequestTypeEnum::ANNUAL,
            'stage_id' => $stage->id,
        ]);
        return $leave;
    }

    #[Test]
    public function ceo_can_approve_any_leave_request(): void
    {
        $ceo = $this->makeEmployee(RoleEnum::CEO);

        $hrStage = Stage::where('role', 'hr')->first();
        $leave = $this->makeLeave($ceo, $hrStage);

        $response = $this->postJson(route('leave-requests.approve', $leave), [
            'approver_id' => $ceo->id,
        ]);

        $response->assertOk();
        $this->assertEquals(LeaveRequestStatusEnum::APPROVED, $leave->fresh()->status);
    }

    #[Test]
    public function hr_can_approve_only_hr_stage(): void
    {
        $this->withoutExceptionHandling();
        $hr = $this->makeEmployee(RoleEnum::HR);
        $employee = $this->makeEmployee(RoleEnum::MANAGER);

        $hrStage = Stage::where('role', RoleEnum::HR->value)->first();
        $leave = $this->makeLeave($employee, $hrStage);


        $response = $this->postJson(route('leave-requests.approve', $leave), [
            'approver_id' => $hr->id,
        ]);

        $response->assertOk();
        $this->assertEquals(Stage::where('role', 'manager')->first()->id, $leave->fresh()->stage_id);
    }

    #[Test]
    public function manager_can_approve_only_if_he_is_employee_manager(): void
    {

        $this->withoutExceptionHandling();
        $manager = $this->makeEmployee(RoleEnum::MANAGER);
        $employee = $this->makeEmployee(RoleEnum::HR, $manager->id);

        $managerStage = Stage::where('role', 'manager')->first();
        $leave = $this->makeLeave($employee, $managerStage);

        $response = $this->postJson(route('leave-requests.approve', $leave), [
            'approver_id' => $manager->id,
        ]);

        $response->assertOk();
        $this->assertEquals(Stage::where('role', 'ceo')->first()->id, $leave->fresh()->stage_id);
    }

    #[Test]
    public function non_manager_cannot_approve_manager_stage(): void
    {
        $hr = $this->makeEmployee(RoleEnum::HR);
        $employee = $this->makeEmployee(RoleEnum::HR);

        $managerStage = Stage::where('role', 'manager')->first();
        $leave = $this->makeLeave($employee, $managerStage);

        $response = $this->postJson(route('leave-requests.approve', $leave), [
            'approver_id' => $hr->id,
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function approving_last_stage_sets_status_to_approved(): void
    {
        $manager = $this->makeEmployee(RoleEnum::MANAGER);
        $employee = $this->makeEmployee(RoleEnum::HR, $manager->id);

        $ceoStage = Stage::where('role', 'ceo')->first();
        $leave = $this->makeLeave($employee, $ceoStage);

        $ceo = $this->makeEmployee(RoleEnum::CEO);

        $response = $this->postJson(route('leave-requests.approve', $leave), [
            'approver_id' => $ceo->id,
        ]);

        $response->assertOk();
        $this->assertEquals(LeaveRequestStatusEnum::APPROVED, $leave->fresh()->status);
    }
}
