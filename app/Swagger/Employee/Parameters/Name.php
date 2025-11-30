<?php

namespace App\Swagger\Employee\Parameters;

use OpenApi\Annotations as OA;

/**
 * @OA\Parameter(
 *     parameter="EmployeeName",
 *     name="name",
 *     in="query",
 *     description="Filter employees by exact name",
 *     required=false,
 *     @OA\Schema(type="string")
 * )
 *
 */
class Name
{
    // Only for Swagger references
}
