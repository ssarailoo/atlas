<?php

namespace App\Policies;

use App\Constants\LeaveRequestApprovalEvent;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Enums\RoleEnum;
use App\Enums\LeaveRequestStatusEnum;
use Illuminate\Auth\Access\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class LeaveRequestPolicy
{
    public function approve(Employee $user, LeaveRequest $leave): Response
    {
        return $this->evaluate($user, $leave, LeaveRequestApprovalEvent::APPROVE);
    }

    public function reject(Employee $user, LeaveRequest $leave): Response
    {
        return $this->evaluate($user, $leave, LeaveRequestApprovalEvent::REJECT);
    }

    private function evaluate(Employee $user, LeaveRequest $leave, string $action): Response
    {
        if ($user->role === RoleEnum::CEO) {
            return Response::allow();
        }


        if ($leave->status === LeaveRequestStatusEnum::DRAFT) {
            return Response::deny("Only CEO can {$action} draft leave requests.")
                ->withStatus(HttpResponse::HTTP_FORBIDDEN);
        }

        if (!$leave->stage) {
            return Response::deny('Leave request stage is missing or invalid.')
                ->withStatus(HttpResponse::HTTP_FORBIDDEN);
        }

        $requiredRole = $leave->stage->role;


        if ($requiredRole === RoleEnum::HR) {
            if ($user->role !== RoleEnum::HR) {
                return Response::deny("Only HR can {$action} this leave request.")
                    ->withStatus(HttpResponse::HTTP_FORBIDDEN);
            }
            return Response::allow();
        }


        if ($requiredRole === RoleEnum::MANAGER) {
            if ($user->role !== RoleEnum::MANAGER) {
                return Response::deny("Only managers can {$action} this leave request.")
                    ->withStatus(HttpResponse::HTTP_FORBIDDEN);
            }
            if ($leave->employee->manager_id !== $user->id) {
                return Response::deny("You are not the assigned manager for this employee.")
                    ->withStatus(HttpResponse::HTTP_FORBIDDEN);
            }

            return Response::allow();
        }

        if ($requiredRole === RoleEnum::CEO) {
            return Response::deny("Only CEO can {$action} this stage.")
                ->withStatus(HttpResponse::HTTP_FORBIDDEN);
        }

        return Response::deny("Unauthorized to {$action} this leave request.")
            ->withStatus(HttpResponse::HTTP_FORBIDDEN);
    }
}
