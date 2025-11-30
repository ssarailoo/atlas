<?php

namespace App\Services;

use App\DataTransferObjects\StoreLeaveRequestDTO;
use App\Enums\LeaveRequestStatusEnum;
use App\Enums\LeaveRequestTypeEnum;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

readonly class LeaveRequestService
{
    public function create(StoreLeaveRequestDTO $dto): LeaveRequest
    {
        $validationResult = $this->validateBusinessRules($dto);

        if ($validationResult['reject_completely']) {
            throw new \Exception($validationResult['rejection_reason']);
        }

        if ($validationResult['is_draft']) {
            $dto = $dto->withStatus(LeaveRequestStatusEnum::DRAFT)
                ->withRejectionReason($validationResult['rejection_reason']);
        }

        return $this->query()->create($dto->toArray());
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
            $results['rejection_reason'] = 'اجازه ثبت درخواست جدید داده نشود. حداقل 3 روز از آخرین درخواست مرخصی گذشته باشد.';
            return $results;
        }


        if (!$this->checkMaxDuration($data)) {
            $results['is_draft'] = true;
            $results['rejection_reason'] = 'مدت زمان مرخصی بیش از حد مجاز (30 روز یا 8 ساعت) است.';
        }

        if (!$this->checkMonthlyLimits($data)) {
            $results['is_draft'] = true;
            $results['rejection_reason'] = 'محدودیت ماهانه مرخصی (2.5 روز استحقاقی، 5 روز استعلاجی، 20 ساعت ساعتی) پر شده است.';
        }

        return $results;
    }



    private function checkThreeDayGap(int $employeeId, string $startDate): bool
    {
        $latestRequest = $this->query()
            ->where('employee_id', $employeeId)
            ->latest('created_at')
            ->first();

        if ($latestRequest) {
            $lastRequestDate = Carbon::parse($latestRequest->created_at);
            $newRequestDate = Carbon::parse($startDate);
            return $newRequestDate->diffInDays($lastRequestDate, false) >= 3;
        }
        return true;
    }

    private function checkMaxDuration(StoreLeaveRequestDTO $data): bool
    {
        if ($data->type === LeaveRequestTypeEnum::HOURLY && $this->calculateHourlyDuration($data) > 8) {
            return false;
        }

        if ($data->type !== LeaveRequestTypeEnum::HOURLY) {
            $startDate = Carbon::parse($data->start_date);
            $endDate = Carbon::parse($data->end_date);

            if ($startDate->diffInDays($endDate) > 30) {
                return false;
            }
        }
        return true;
    }

    private function checkMonthlyLimits(StoreLeaveRequestDTO $dto): bool
    {
        $currentMonthStart = Carbon::parse($dto->start_date)->startOfMonth();
        $currentMonthEnd = Carbon::parse($dto->start_date)->endOfMonth();

        $currentMonthLeaves = $this->query()
            ->where('employee_id', $dto->employee_id)
            ->where('type', $dto->type)
            ->where('status', '!=', LeaveRequestStatusEnum::REJECTED) // فقط مرخصی‌های تأیید شده/در حال بررسی
            ->whereBetween('start_date', [$currentMonthStart, $currentMonthEnd])
            ->get();

        if ($dto->type === LeaveRequestTypeEnum::HOURLY) {
            $totalHours = $currentMonthLeaves->sum(fn($leave) => $this->calculateHourlyDuration($leave));
            $newHours = $this->calculateHourlyDuration($dto);
            return ($totalHours + $newHours) <= 20; // محدودیت 20 ساعت
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



    private function query(): Builder
    {
        return LeaveRequest::query();
    }

}
