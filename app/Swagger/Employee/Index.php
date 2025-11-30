<?php

namespace App\Swagger\Employee;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/v1/employees",
 *     tags={"Employees"},
 *     summary="Get list of employees",
 *     @OA\Parameter(ref="#/components/parameters/EmployeeName"),
 *     @OA\Parameter(ref="#/components/parameters/EmployeePosition"),
 *     @OA\Parameter(ref="#/components/parameters/EmployeeLeaveBalance"),
 *     @OA\Parameter(ref="#/components/parameters/EmployeeLeaveBalanceMax"),
 *     @OA\Parameter(ref="#/components/parameters/EmployeeLeaveBalanceMin"),
 *     @OA\Parameter(ref="#/components/parameters/Page"),
 *     @OA\Parameter(ref="#/components/parameters/PerPage"),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/EmployeeResource")
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 ref="#/components/schemas/MetaPagination"
 *             )
 *         )
 *     )
 * )
 */
class Index
{
    // Empty class; exists only for Swagger annotations
}
