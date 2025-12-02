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
        public ?string $reason,
        public LeaveRequestTypeEnum $type,
        public LeaveRequestStatusEnum  $status,
        public ?string $rejection_reason = null,
        public ?int $max_stage_id = null,
        public int $stage_id=1,
    ) {}

    public static function fromRequest(array $data): static
    {
        return new static(
            employee_id: $data['employee_id'],
            start_date: $data['start_date'],
            end_date: $data['end_date'] ?? null,
            start_time: $data['start_time'] ?? null,
            end_time: $data['end_time'] ?? null,
            reason: $data['reason']??null,
            type: LeaveRequestTypeEnum::from($data['type']),
            status: LeaveRequestStatusEnum::PENDING_HR,
            rejection_reason: null,
            max_stage_id: null
        );
    }
    public function withStatus(LeaveRequestStatusEnum $status): static
    {
        return $this->cloneWith(['status' => $status]);
    }

    public function withRejectionReason(?string $reason): static
    {
        return $this->cloneWith(['rejection_reason' => $reason]);
    }

    public function withMaxStage(int $stageId): static
    {
        return $this->cloneWith(['max_stage_id' => $stageId]);
    }
    public function withStageId(int $stageId): static
    {
        return $this->cloneWith(['stage_id' => $stageId]);
    }
}
