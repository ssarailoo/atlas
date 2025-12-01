<?php

namespace App\Swagger\LeaveRequest;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/api/v1/leave-requests",
 *     tags={"Leave Requests"},
 *     summary="Create a new leave request",
 *     description="Submit a new leave request. The system will validate business rules and may create as PENDING_HR or DRAFT status.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StoreLeaveRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Leave request created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/LeaveRequestResource"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Request rejected due to business rules",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="حداقل 3 روز از آخرین درخواست مرخصی نگذشته است.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="employee_id",
 *                     type="array",
 *                     @OA\Items(type="string", example="The selected employee id is invalid.")
 *                 ),
 *                 @OA\Property(
 *                     property="type",
 *                     type="array",
 *                     @OA\Items(type="string", example="The selected type is invalid.")
 *                 ),
 *                 @OA\Property(
 *                     property="start_date",
 *                     type="array",
 *                     @OA\Items(type="string", example="The start date must be a date after or equal to today.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class Store
{
    // Empty class; exists only for Swagger annotations
}
