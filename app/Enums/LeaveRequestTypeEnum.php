<?php

namespace App\Enums;

use App\Traits\HasEnumValues;

enum LeaveRequestTypeEnum: string
{
    use HasEnumValues;
    case ANNUAL = 'annual';
    case SICK = 'sick';
    case HOURLY = 'hourly';
    case UNPAID = 'unpaid';
}
