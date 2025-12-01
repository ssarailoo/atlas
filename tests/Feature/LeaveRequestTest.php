<?php

namespace Tests\Feature;

use App\Constants\LeaveRejectionMessages;
use App\Enums\LeaveRequestStatusEnum;
use App\Enums\LeaveRequestTypeEnum;
use App\Enums\RoleEnum;
use App\Http\Resources\LeaveRequestStoreResource;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Morilog\Jalali\Jalalian;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;
    protected bool $seed = true;
    private Employee $employee;
    private string $storeRoute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = Employee::create([
            "full_name" => "test",
            "email" => "test@test.com",
            "position" => "test",
            "role" => RoleEnum::EMPLOYEE,
            'leave_balance' => 30,
        ]);
        $this->storeRoute=route("leave-requests.store");
    }

    #[Test]
    public function it_can_create_annual_leave_request_successfully()
    {
        $data = [
            'employee_id' => $this->employee->id,
            'type' => LeaveRequestTypeEnum::ANNUAL->value,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(6)->format('Y-m-d'),
            'reason' => 'annual test reason',
        ];
        $response = $this->postJson($this->storeRoute, $data);
        $leaveRequest = \App\Models\LeaveRequest::latest()->first();
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson(
            (new LeaveRequestStoreResource($leaveRequest))->response()->getData(true)
        );

        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->employee->id,
            'type' => LeaveRequestTypeEnum::ANNUAL->value,
            'status' => LeaveRequestStatusEnum::PENDING_HR->value,
        ]);
    }

    #[Test]
    public function it_can_create_hourly_leave_request_successfully()
    {
        $this->withoutExceptionHandling();
        $data = [
            'employee_id' => $this->employee->id,
            'type' => LeaveRequestTypeEnum::HOURLY->value,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'reason' => 'hourly test reason',
        ];

        $response = $this->postJson($this->storeRoute, $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->employee->id,
            'type' => LeaveRequestTypeEnum::HOURLY->value,
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
        ]);
    }

}
