<?php

namespace App\Swagger\LeaveRequest\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="LeaveRequestResource",
 *     type="object",
 *     title="Leave Request Resource",
 *     description="Leave request data formatted for API responses",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Leave request ID",
 *         example=37
 *     ),
 *     @OA\Property(
 *         property="employee_id",
 *         type="integer",
 *         description="Employee ID",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="leave_type",
 *         type="string",
 *         description="Type of leave",
 *         enum={"annual", "sick", "hourly", "unpaid"},
 *         example="annual"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Current status of the leave request",
 *         enum={"draft", "pending_hr", "pending_manager", "pending_ceo", "approved", "rejected", "due_date"},
 *         example="draft"
 *     ),
 *     @OA\Property(
 *         property="start_date",
 *         type="object",
 *         description="Start date in both formats",
 *         @OA\Property(property="gregorian", type="string", format="date", example="2024-12-10"),
 *         @OA\Property(property="jalali", type="string", example="1403/09/20")
 *     ),
 *     @OA\Property(
 *         property="end_date",
 *         type="object",
 *         description="End date in both formats (null for hourly leaves)",
 *         nullable=true,
 *         @OA\Property(property="gregorian", type="string", format="date", example="2024-12-12"),
 *         @OA\Property(property="jalali", type="string", example="1403/09/22")
 *     ),
 *     @OA\Property(
 *         property="start_time",
 *         type="string",
 *         format="time",
 *         description="Start time (for hourly leaves only)",
 *         nullable=true,
 *         example="09:00"
 *     ),
 *     @OA\Property(
 *         property="end_time",
 *         type="string",
 *         format="time",
 *         description="End time (for hourly leaves only)",
 *         nullable=true,
 *         example="12:00"
 *     ),
 *     @OA\Property(
 *         property="days_count",
 *         type="integer",
 *         description="Number of days (null for hourly leaves)",
 *         nullable=true,
 *         example=3
 *     ),
 *     @OA\Property(
 *         property="hours_count",
 *         type="number",
 *         format="float",
 *         description="Number of hours (null for daily leaves)",
 *         nullable=true,
 *         example=3.5
 *     ),
 *     @OA\Property(
 *         property="reason",
 *         type="string",
 *         description="Reason for the leave request",
 *         nullable=true,
 *         example="استراحت"
 *     ),
 *     @OA\Property(
 *         property="rejection_reason",
 *         type="string",
 *         description="Reason for rejection or draft status",
 *         nullable=true,
 *         example="محدودیت ماهانه مرخصی پر شده است"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Status message based on current status",
 *         example="درخواست مرخصی به عنوان پیش‌نویس ذخیره شد و نیاز به بررسی و تایید دارد."
 *     )
 * )
 */
class LeaveRequestResource
{
    // Only for Swagger schema reference
}
