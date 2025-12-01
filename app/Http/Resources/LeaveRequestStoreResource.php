<?php

namespace App\Http\Resources;

use App\Constants\LeaveStatusMessages;
use App\Enums\LeaveRequestTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;

class LeaveRequestStoreResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'leave_type' => $this->type,
            'status' => $this->status,
            'start_date' => [
                'gregorian' => $this->start_date->format('Y-m-d'),
                'jalali' => Jalalian::fromCarbon($this->start_date)->format('Y/m/d'),
            ],
            'end_date' => $this->when($this->end_date, [
                'gregorian' => $this->end_date?->format('Y-m-d'),
                'jalali' => $this->end_date ? Jalalian::fromCarbon($this->end_date)->format('Y/m/d') : null,
            ]),
            'start_time' => $this->start_time?->format('H:i'),
            'end_time' => $this->end_time?->format('H:i'),
            'days_count' => $this->getDaysCount(),
            'hours_count' => $this->getHoursCount(),
            'reason' => $this->reason,
            'rejection_reason' => $this->rejection_reason,
            'message' => $this->getStatusMessage(),
        ];
    }


    private function getDaysCount(): ?int
    {
        if ($this->type === LeaveRequestTypeEnum::HOURLY) {
            return null;
        }

        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }

        return null;
    }


    private function getHoursCount(): ?float
    {
        if ($this->type->value !== LeaveRequestTypeEnum::HOURLY) {
            return null;
        }

        if ($this->start_time && $this->end_time) {
            $start =Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);
            return round($start->diffInMinutes($end) / 60, 1);
        }

        return null;
    }


    private function getStatusMessage(): string
    {
        $locale = app()->getLocale();
        return LeaveStatusMessages::get($this->status->value, $locale);
    }
}
