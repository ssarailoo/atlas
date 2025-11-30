<?php

namespace App\Models;

use App\Enums\LeaveRequestStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{


    protected $fillable = [
        'employee_id',
        'approver_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'reason',
        'status',
        'stage_id',
        'rejection_reason'
    ];
    protected $casts = [
        'status' => LeaveRequestStatusEnum::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
    ];
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }


}
