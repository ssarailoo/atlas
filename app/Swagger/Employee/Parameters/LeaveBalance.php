<?php

namespace App\Swagger\Employee\Parameters;

use OpenApi\Annotations as OA;

/**
 * @OA\Parameter(
 *     parameter="EmployeeLeaveBalance",
 *     name="leave_balance",
 *     in="query",
 *     description="Exact leave balance. Cannot be combined with min/max values.",
 *     required=false,
 *     @OA\Schema(type="integer")
 * )
 * @OA\Parameter(
 *     parameter="EmployeeLeaveBalanceMin",
 *     name="leave_balance[min]",
 *     in="query",
 *     description="Minimum leave balance. Cannot be used with exact leave_balance.",
 *     required=false,
 *     @OA\Schema(type="integer")
 * )
 * @OA\Parameter(
 *     parameter="EmployeeLeaveBalanceMax",
 *     name="leave_balance[max]",
 *     in="query",
 *     description="Maximum leave balance. Cannot be used with exact leave_balance. If both min and max are provided, min must not exceed max.",
 *     required=false,
 *     @OA\Schema(type="integer")
 * )
 */
class LeaveBalance
{
    // Only for Swagger references
}
