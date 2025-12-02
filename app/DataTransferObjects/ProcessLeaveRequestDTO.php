<?php

namespace App\DataTransferObjects;

readonly class ProcessLeaveRequestDTO extends BaseDTO
{
    public function __construct(
        public int $approver_id,
        public ?string $rejection_reason = null,
    ) {}

    public static function fromRequest(array $data): static
    {
        return new static(
            approver_id: $data['approver_id'],
            rejection_reason: $data['rejection_reason'] ?? null,
        );
    }
}
