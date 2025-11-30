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
        );
    }
}
