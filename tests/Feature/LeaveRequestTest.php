<?php

namespace Tests\Feature;

use App\Constants\LeaveRejectionMessages;
use App\Enums\LeaveRequestStatusEnum;
use App\Enums\LeaveRequestTypeEnum;
use App\Enums\RoleEnum;
use App\Http\Resources\LeaveRequestStoreResource;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Morilog\Jalali\Jalalian;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->employee = Employee::create([
            "full_name"=>"test",
            "email"=>"test@test.com",
            "position"=>"test",
            "role"=>RoleEnum::EMPLOYEE,
            'leave_balance' => 30,
        ]);
    }

#[Test]
    public function it_can_create_annual_leave_request_successfully()
    {
        $this->withoutExceptionHandling();
        $data = [
            'employee_id' => $this->employee->id,
            'type' => LeaveRequestTypeEnum::ANNUAL->value,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(6)->format('Y-m-d'),
            'reason' => 'test reason',
        ];
        $response = $this->postJson(route('leave-requests.store'), $data);
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


}
