<?php

namespace App\Enums;

use App\Traits\HasEnumValues;

enum LeaveRequestStatusEnum: string
{
    use HasEnumValues;

    case DRAFT = 'draft';
    case PENDING_HR = 'pending_hr';
    case PENDING_MANAGER = 'pending_manager';
    case PENDING_CEO = 'pending_ceo';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case DUE_DATE = 'due_date';
}
