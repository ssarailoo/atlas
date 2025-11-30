<?php

namespace App\Enums;

enum LeaveRequestTypeEnum: string
{
    case ANNUAL = 'annual';
    case SICK = 'sick';
    case HOURLY = 'hourly';
    case UNPAID = 'unpaid';
}
