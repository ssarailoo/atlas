<?php

namespace App\Swagger\Employee\Parameters;

use OpenApi\Annotations as OA;

/**
 * @OA\Parameter(
 *     parameter="EmployeePosition",
 *     name="position",
 *     in="query",
 *     description="Filter employees by exact position",
 *     required=false,
 *     @OA\Schema(type="string")
 * )
 *
 */
class Position
{
    // Only for Swagger references
}
