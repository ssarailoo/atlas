<?php

namespace App\Console\Commands;

use App\Enums\LeaveRequestStatusEnum;
use App\Enums\RoleEnum;
use App\Models\LeaveRequest;
use App\Models\Stage;
use Carbon\Carbon;
use Illuminate\Console\Command;


class LeaveMaintenanceCommand extends Command
{
    protected $signature = 'leave:maintenance';
    protected $description = 'Handle auto-forward, auto-reject and cleanup of old leave requests';

    public function handle()
    {
        $this->info("Leave maintenance started...");

        $this->forwardPendingRequests();
        $this->rejectExpiredHRRequests();
        $this->deleteOldRequests();

        $this->info("Leave maintenance finished.");

        return Command::SUCCESS;
    }


    private function forwardPendingRequests(): void
    {
        $this->info("Checking for pending requests older than 48 hours...");

        $requests = LeaveRequest::where('status', LeaveRequestStatusEnum::PENDING_CEO)
            ->where('stage_id', 1)
            ->where('created_at', '<=', Carbon::now()->subHours(48))
            ->get();
        $hrStage = Stage::query()->where('role', RoleEnum::HR->value)->first();
        foreach ($requests as $request) {
            $request->update([
                'stage_id' => $hrStage->id,
                'max_stage_id' => $hrStage->id,
                'status' => LeaveRequestStatusEnum::PENDING_HR
            ]);
            $request->logs()->create([
                'action' => 'system_forwarded_to_hr',
                'performed_by' => null,
                'meta' => [
                    'from_status' => LeaveRequestStatusEnum::PENDING_CEO->value,
                    'to_stage' => LeaveRequestStatusEnum::PENDING_HR->value
                ],
            ]);
        }

        $this->info("Forwarded: " . $requests->count() . " requests.");
    }

    private function rejectExpiredHRRequests(): void
    {
        $this->info("Rejecting HR pending requests older than 48 hours...");
        $hrStage = Stage::query()->where('role', RoleEnum::HR->value)->first();
        $requests = LeaveRequest::where('status', LeaveRequestStatusEnum::PENDING_HR)
            ->where('stage_id')
            ->where('updated_at', '<=', Carbon::now()->subHours(48))
            ->get();

        foreach ($requests as $request) {
            $request->update([
                'status' => LeaveRequestStatusEnum::REJECTED,
                'rejection_reason' => 'Auto-rejected by system due to 48 hours no response.'
            ]);
            $request->logs()->create([
                'action' => 'system_forwarded_to_hr',
                'performed_by' => null,
                'meta' => [
                    'from_status' => LeaveRequestStatusEnum::PENDING_HR->value,
                    'to_status' => LeaveRequestStatusEnum::REJECTED->value
                ],
            ]);
        }

        $this->info("Auto-rejected: " . $requests->count() . " requests.");
    }


    private function deleteOldRequests(): void
    {
        $this->info("Deleting old due_date requests older than 1 month...");

        $requests = LeaveRequest::where('updated_at', '<=', Carbon::now()->subMonth())->where('status', LeaveRequestStatusEnum::DUE_DATE)->get();

        foreach ($requests as $request) {
            $request->logs->create([
                'leave_request_id' => $request->id,
                'action' => 'system_auto_deleted_due_date_expired',
                'performed_by' => null,
                'meta' => null,
            ]);
            $request->delete();
        }

        $this->info("Deleted: " . $requests->count() . " requests.");
    }
}
