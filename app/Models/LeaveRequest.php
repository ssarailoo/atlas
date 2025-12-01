<?php

namespace App\Models;

use App\Enums\LeaveRequestStatusEnum;
use App\Enums\LeaveRequestTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveRequest extends Model
{


    protected $fillable = [
        'employee_id',
        'approver_id',
        'type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'reason',
        'status',
        'stage_id',
        'rejection_reason',
        'max_stage_id'
    ];
    protected $casts = [
        'status' => LeaveRequestStatusEnum::class,
        'type' => LeaveRequestTypeEnum::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
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
    public function maxStage(): BelongsTo
    {
        return $this->belongsTo(Stage::class,'max_stage_id');
    }

    public function logs() : HasMany
    {
        return $this->hasMany(LeaveLog::class);
    }


}
