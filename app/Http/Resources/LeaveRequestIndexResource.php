<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class LeaveRequestIndexResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_name' => $this->employee->full_name,
            'start_date' =>  Jalalian::fromCarbon($this->start_date)->format('Y/m/d'),
            'end_date' => $this->end_date ? Jalalian::fromCarbon($this->end_date)->format('Y/m/d') : null,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            "current_stage"=>$this->stage_id,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
        ];
    }
}
