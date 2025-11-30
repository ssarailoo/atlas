<?php

namespace App\DataTransferObjects;

use App\Enums\LeaveRequestStatusEnum;
use App\Enums\LeaveRequestTypeEnum;

readonly class StoreLeaveRequestDTO extends BaseDTO
{
    public function __construct(
        public int $employee_id,
        public string $start_date,
        public ?string $end_date,
        public ?string $start_time,
        public ?string $end_time,
        public string $reason,
        public LeaveRequestTypeEnum $type,
        public LeaveRequestStatusEnum  $status,
        public ?string $rejection_reason = null
    ) {}

    public static function fromRequest(array $data): static
    {
        return new static(
            employee_id: $data['employee_id'],
            start_date: $data['start_date'],
            end_date: $data['end_date'] ?? null,
            start_time: $data['start_time'] ?? null,
            end_time: $data['end_time'] ?? null,
            reason: $data['reason'],
            type: $data['type'],
            status: LeaveRequestStatusEnum::PENDING_HR,
            rejection_reason: null
        );
    }
    public function withStatus(LeaveRequestStatusEnum $status): static
    {
        return new static(
            employee_id: $this->employee_id,
            start_date: $this->start_date,
            end_date: $this->end_date,
            start_time: $this->start_time,
            end_time: $this->end_time,
            reason: $this->reason,
            type: $this->type,
            status: $status,
            rejection_reason: $this->rejection_reason
        );
    }
    public function withRejectionReason(?string $rejectionReason): static
    {
        return new static(
            employee_id: $this->employee_id,
            start_date: $this->start_date,
            end_date: $this->end_date,
            start_time: $this->start_time,
            end_time: $this->end_time,
            reason: $this->reason,
            type: $this->type,
            status: $this->status,
            rejection_reason: $rejectionReason
        );
    }
}
