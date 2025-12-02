<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestProcessResource extends JsonResource
{
    public function toArray($request)
    {
        $currentStage = $this->stage?->name;
        $nextStage    = $this->stage?->next?->name;

        $message = match ($this->status->value) {
            'approved' => "Request approved. No further approval required.",
            'rejected' => "Request rejected.",
            default    => "Request approved by {$currentStage}. Waiting for {$nextStage} approval."
        };

        return [
            'status'        => $this->status->value,
            'approved_by'   => $this->approved_by,
            'current_stage' => $currentStage,
            'next_stage'    => $nextStage,
            'message'       => $message,
        ];
    }
}
