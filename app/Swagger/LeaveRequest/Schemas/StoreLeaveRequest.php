<?php

namespace App\Swagger\LeaveRequest\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="StoreLeaveRequest",
 *     type="object",
 *     title="Store Leave Request",
 *     description="Request body for creating a new leave request",
 *     required={"employee_id", "type", "start_date"},
 *     @OA\Property(
 *         property="employee_id",
 *         type="integer",
 *         description="ID of the employee requesting leave",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="Type of leave request",
 *         enum={"annual", "sick", "hourly", "unpaid"},
 *         example="annual"
 *     ),
 *     @OA\Property(
 *         property="start_date",
 *         type="string",
 *         format="date",
 *         description="Start date in  Jalali (YYYY-MM-DD) format. Must be today or future date.",
 *         example="1404-05-05"
 *     ),
 *     @OA\Property(
 *         property="end_date",
 *         type="string",
 *         format="date",
 *         description="End date in  Jalali (YYYY-MM-DD) format (required for non-hourly leaves). Must be after start_date.",
 *         example="1404-05-06"
 *     ),
 *     @OA\Property(
 *         property="start_time",
 *         type="string",
 *         format="time",
 *         description="Start time (required for hourly leaves). Format: HH:MM",
 *         example="09:00"
 *     ),
 *     @OA\Property(
 *         property="end_time",
 *         type="string",
 *         format="time",
 *         description="End time (required for hourly leaves). Must be after start_time. Format: HH:MM",
 *         example="12:00"
 *     ),
 *     @OA\Property(
 *         property="reason",
 *         type="string",
 *         description="Reason for leave request (optional, max 1000 characters)",
 *         example="استراحت و مرخصی سالانه"
 *     )
 * )
 */
class StoreLeaveRequest
{
    // Only for Swagger schema reference
}
