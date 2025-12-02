<?php

namespace App\StateMachines;

use App\Models\LeaveRequest;
use App\Models\Stage;
use App\Models\Employee;
use App\Enums\LeaveRequestStatusEnum;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\DB;

readonly class LeaveRequestStateMachine
{
    public function __construct(
       readonly private LeaveRequest $request,
       readonly private Employee $actor
    ) {}

    public function approve(): LeaveRequest
    {
        return DB::transaction(function () {

            if ($this->actor->role === RoleEnum::CEO) {
                return $this->approveAsCEO();
            }

            return $this->approveNormal();
        });
    }

    private function approveAsCEO(): LeaveRequest
    {
        $this->request->update([
            'status' => LeaveRequestStatusEnum::APPROVED,
        ]);
        return $this->request;
    }

    private function approveNormal(): LeaveRequest
    {
        $currentStage = $this->request->stage;
        if (!$currentStage->next_stage_id ||
            $currentStage->id == $this->request->max_stage_id) {

            $this->request->update([
                'status' => LeaveRequestStatusEnum::APPROVED,
            ]);

            return $this->request;
        }

        $next = Stage::find($currentStage->next_stage_id);
        $this->request->update([
            'stage_id' => $next->id,
            'status' => $this->statusForStage($next),
        ]);
        return $this->request;
    }

    public function reject(string $reason): LeaveRequest
    {
        return DB::transaction(function () use ($reason) {
            $this->request->update([
                'status' => LeaveRequestStatusEnum::REJECTED,
                'rejection_reason' => $reason,
            ]);
            return $this->request;
        });
    }

    private function statusForStage(Stage $stage): LeaveRequestStatusEnum
    {
        return match ($stage->role) {
            RoleEnum::HR->value => LeaveRequestStatusEnum::PENDING_HR,
            RoleEnum::MANAGER->value => LeaveRequestStatusEnum::PENDING_MANAGER,
            RoleEnum::CEO->value => LeaveRequestStatusEnum::PENDING_CEO,
        };
    }
}
