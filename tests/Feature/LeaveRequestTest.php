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
        $this->storeRoute = route("leave-requests.store");
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

    #[Test]
    public function it_rejects_request_when_three_day_gap_not_passed()
    {

        LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'status' => LeaveRequestStatusEnum::APPROVED,
            'start_date' => now()->subDays(4),
            'end_date' => now()->subDays(2),
            'type' => LeaveRequestTypeEnum::ANNUAL,
        ]);

        $data = [
            'employee_id' => $this->employee->id,
            'type' => LeaveRequestTypeEnum::ANNUAL->value,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'reason' => 'test',
        ];

        $response = $this->postJson($this->storeRoute, $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson([
                'message' => LeaveRejectionMessages::THREE_DAY_GAP,
            ]);
    }

    #[Test]
    public function it_allows_request_when_three_day_gap_passed()
    {
        $this->withoutExceptionHandling();

        $lastStart = now()->subDays(10)->startOfDay();
        $lastEnd = $lastStart->copy()->addDay();

        LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'status' => LeaveRequestStatusEnum::APPROVED,
            'start_date' => $lastStart,
            'end_date' => $lastEnd,
            'type' => LeaveRequestTypeEnum::ANNUAL,
        ]);

        $newStart = now()->addDay()->format('Y-m-d');
        $newEnd = now()->addDays(2)->format('Y-m-d');

        $data = [
            'employee_id' => $this->employee->id,
            'type' => LeaveRequestTypeEnum::ANNUAL->value,
            'start_date' => $newStart,
            'end_date' => $newEnd,
            'reason' => 'test',
        ];

        $response = $this->postJson($this->storeRoute, $data);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    #[Test]
    public function it_rejects_annual_leave_when_insufficient_balance()
    {
        $this->employee->update(['leave_balance' => 2]);

        $data = [
            'employee_id' => $this->employee->id,
            'type' => LeaveRequestTypeEnum::ANNUAL->value,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(7)->format('Y-m-d'),
            'reason' => 'test',
        ];

        $response = $this->postJson($this->storeRoute, $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson([
                'message' => LeaveRejectionMessages::INSUFFICIENT_BALANCE,
            ]);
    }

    /** @test */
    public function it_creates_draft_when_exceeding_max_duration_for_hourly_leave()
    {
        $data = [
            'employee_id' => $this->employee->id,
            'type' => LeaveRequestTypeEnum::HOURLY->value,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '18:00',
            'reason' => 'test',
        ];

        $response = $this->postJson($this->storeRoute, $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->employee->id,
            'status' => LeaveRequestStatusEnum::DRAFT->value,
            'rejection_reason' => LeaveRejectionMessages::MAX_DURATION_EXCEEDED,
        ]);
    }

}
