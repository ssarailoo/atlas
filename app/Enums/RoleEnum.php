<?php

namespace App\Enums;

use App\Traits\HasEnumValues;

enum RoleEnum: string
{
    use HasEnumValues;

    case CEO = 'ceo';
    case HR = 'hr';
    case MANAGER = 'manager';
    case EMPLOYEE = 'employee';
}
