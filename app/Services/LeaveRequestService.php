<?php

namespace App\Services;

use App\Constants\LeaveConstant;
use App\Constants\LeaveRejectionMessages;
use App\Constants\TotalAnnualLeaves;
use App\DataTransferObjects\IndexLeaveRequestOfEmployeeDTO;
use App\DataTransferObjects\ProcessLeaveRequestDTO;
use App\DataTransferObjects\StoreLeaveRequestDTO;
use App\Enums\LeaveRequestStatusEnum;
use App\Enums\LeaveRequestTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Stage;
use App\QueryFilters\LeaveReqeust\StatusFilter;
use App\StateMachines\LeaveRequestStateMachine;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;
use Illuminate\Database\Eloquent\Builder;

readonly class LeaveRequestService
{
    public function create(StoreLeaveRequestDTO $dto): LeaveRequest
    {
        $validationResult = $this->validateBusinessRules($dto);

        if ($validationResult['reject_completely']) {
            abort(403, $validationResult['rejection_reason']);
        }

        if ($validationResult['is_draft']) {
            $dto = $dto->withStatus(LeaveRequestStatusEnum::DRAFT)
                ->withStageId($this->getStageForDraft())
                ->withMaxStage($this->getStageForDraft())
                ->withRejectionReason($validationResult['rejection_reason']);

        } else {
            $days = $this->calculateDaysDuration($dto);
            $maxStage = $this->determineMaxStage($days);
            $dto = $dto->withMaxStage($maxStage);
        }
        return $this->query()->create($dto->toArray());

    }

    public function approve(LeaveRequest $leave, ProcessLeaveRequestDTO $dto): LeaveRequest
    {
        $approver = Employee::findOrFail($dto->approver_id);
        $sm = new LeaveRequestStateMachine($leave, $approver);

        return $sm->approve();
    }

    public function reject(LeaveRequest $leave, ProcessLeaveRequestDTO $dto): LeaveRequest
    {
        $approver = Employee::findOrFail($dto->approver_id);

        $sm = new LeaveRequestStateMachine($leave, $approver);

        return $sm->reject(
            reason: $dto->rejection_reason ?? 'Rejected by approver'
        );
    }

    public function getLeaveRequestsOfEmployee(IndexLeaveRequestOfEmployeeDTO $dto)
    {
        $query = $this->query();
        $pipelineFilters = $this->getPipelineFilters();

        return app(Pipeline::class)
            ->send($query)
            ->through($pipelineFilters)
            ->thenReturn()
            ->get();
    }

    public function getBalanceOfEmployee(int $employeeId): array
    {
        $leaveBalance = $this->getLeaveBalanceOfEmployee($employeeId);
        $totalLeaves = LeaveConstant::TOTAL_ANNUAL - $leaveBalance;
        return [
            "employee_id" => $employeeId,
            "total_leaves" => $totalLeaves,
            "remaining_balance" => $leaveBalance
        ];
    }

    private function validateBusinessRules(StoreLeaveRequestDTO $data): array
    {
        $results = [
            'is_draft' => false,
            'reject_completely' => false,
            'rejection_reason' => null
        ];

        if (!$this->checkThreeDayGap($data->employee_id, $data->start_date)) {
            $results['reject_completely'] = true;
            $results['rejection_reason'] = LeaveRejectionMessages::THREE_DAY_GAP;
            return $results;
        }

        if ($data->type === LeaveRequestTypeEnum::ANNUAL) {
            if (!$this->checkLeaveBalance($data)) {
                $results['reject_completely'] = true;
                $results['rejection_reason'] = LeaveRejectionMessages::INSUFFICIENT_BALANCE;
                return $results;
            }
        }

        if (!$this->checkMaxDuration($data)) {
            $results['is_draft'] = true;
            $results['rejection_reason'] = LeaveRejectionMessages::MAX_DURATION_EXCEEDED;
        }

        if (!$this->checkMonthlyLimits($data)) {
            $results['is_draft'] = true;
            $results['rejection_reason'] = LeaveRejectionMessages::MONTHLY_LIMIT_EXCEEDED;
        }

        return $results;
    }

    private function determineMaxStage(int $days)
    {
        return Stage::where('min_days', '<=', $days)
            ->orderByDesc('order')
            ->first()->id;
    }

    private function getStageForDraft()
    {
        return Stage::where('role', RoleEnum::CEO)->first()->id;
    }

    private function checkThreeDayGap(int $employeeId, string $startDate): bool
    {

        $latestApprovedRequest = $this->query()
            ->where('employee_id', $employeeId)
            ->where('status', LeaveRequestStatusEnum::APPROVED)
            ->latest('end_date')
            ->first();

        if ($latestApprovedRequest) {
            $lastEndDate = Carbon::parse($latestApprovedRequest->end_date);
            $newStartDate = Carbon::parse($startDate);
            return $newStartDate->diffInDays($lastEndDate, true) >= 3;
        }
        return true;
    }


    private function checkLeaveBalance(StoreLeaveRequestDTO $data): bool
    {
        $employee = Employee::find($data->employee_id);

        if (!$employee) {
            return false;
        }

        $requestedDays = $this->calculateDaysDuration($data);

        return $employee->leave_balance >= $requestedDays;
    }


    private function checkMaxDuration(StoreLeaveRequestDTO $data): bool
    {
        if ($data->type === LeaveRequestTypeEnum::HOURLY) {
            return $this->calculateHourlyDuration($data) <= 8;
        }


        $startDate = Carbon::parse($data->start_date);
        $endDate = Carbon::parse($data->end_date);

        return $startDate->diffInDays($endDate) + 1 <= 30;
    }

    private function checkMonthlyLimits(StoreLeaveRequestDTO $dto): bool
    {

        if ($dto->type === LeaveRequestTypeEnum::UNPAID) {
            return true;
        }


        $jalalianDate = Jalalian::fromCarbon(Carbon::parse($dto->start_date));


        $currentMonthStart = $jalalianDate->getFirstDayOfMonth()->toCarbon();
        $currentMonthEnd = $jalalianDate->getEndDayOfMonth()->toCarbon();

        $currentMonthLeaves = $this->query()
            ->where('employee_id', $dto->employee_id)
            ->where('type', $dto->type)
            ->whereIn('status', [
                LeaveRequestStatusEnum::PENDING_HR,
                LeaveRequestStatusEnum::APPROVED
            ])
            ->whereBetween('start_date', [$currentMonthStart, $currentMonthEnd])
            ->get();


        if ($dto->type === LeaveRequestTypeEnum::HOURLY) {
            $totalHours = $currentMonthLeaves->sum(fn($leave) => $this->calculateHourlyDuration($leave)
            );
            $newHours = $this->calculateHourlyDuration($dto);

            return ($totalHours + $newHours) <= 20;
        }

        if ($dto->type === LeaveRequestTypeEnum::ANNUAL) {
            $totalDays = $currentMonthLeaves->sum(fn($leave) => $this->calculateDaysDuration($leave)
            );
            $newDays = $this->calculateDaysDuration($dto);

            return ($totalDays + $newDays) <= 2.5;
        }


        if ($dto->type === LeaveRequestTypeEnum::SICK) {
            $totalDays = $currentMonthLeaves->sum(fn($leave) => $this->calculateDaysDuration($leave)
            );
            $newDays = $this->calculateDaysDuration($dto);

            return ($totalDays + $newDays) <= 5;
        }

        return true;
    }


    private function calculateHourlyDuration($data): float
    {
        if (isset($data->start_time) && isset($data->end_time)) {
            $start = Carbon::createFromFormat('H:i', $data->start_time);
            $end = Carbon::createFromFormat('H:i', $data->end_time);
            return $start->diffInMinutes($end) / 60;
        }
        return 0;
    }


    private function calculateDaysDuration($data): int
    {
        if (isset($data->start_date) && isset($data->end_date)) {
            $start = Carbon::parse($data->start_date);
            $end = Carbon::parse($data->end_date);
            return $start->diffInDays($end) + 1;
        }
        return 0;
    }

    private function getLeaveBalanceOfEmployee(int $employeeId)
    {
        return Employee::query()->find($employeeId)->leave_balance;
    }

    private function getPipelineFilters(): array
    {
        return [
            StatusFilter::class
        ];
    }

    private function query(): Builder
    {
        return LeaveRequest::query();
    }


}
