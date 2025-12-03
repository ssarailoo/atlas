<?php

namespace App\DataTransferObjects;

use App\Enums\LeaveRequestStatusEnum;

readonly class IndexLeaveRequestOfEmployeeDTO extends BaseDTO
{
    public function __construct(
        public int                     $employee_id,
        public ?LeaveRequestStatusEnum $status =null
    )
    {

    }

    public static function fromRequest(array $data): static
    {
        return new static(
            employee_id: $data['employee_id'],
            status: $data['status'] ?? null
        );
    }
}
