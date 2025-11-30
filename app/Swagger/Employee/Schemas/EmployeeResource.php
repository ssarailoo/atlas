<?php

namespace App\Swagger\Employee\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="EmployeeResource",
 *     type="object",
 *     title="Employee Resource",
 *     description="Employee data formatted for API responses",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="position", type="string", example="Software Engineer"),
 *     @OA\Property(property="leave_balance", type="integer", example=14),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-05T09:30:00Z")
 * )
 */
class EmployeeResource
{
    // Only for Swagger schema reference
}
