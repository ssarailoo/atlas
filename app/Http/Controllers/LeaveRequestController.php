<?php

namespace App\Http\Controllers;

use App\Constants\LeaveRequestApprovalEvent;
use App\DataTransferObjects\ProcessLeaveRequestDTO;
use App\DataTransferObjects\StoreLeaveRequestDTO;
use App\Http\Requests\ProcessLeaveRequest;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Resources\LeaveRequestStoreResource;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class LeaveRequestController extends Controller
{
    public function __construct(readonly private LeaveRequestService $service)
    {

    }

    public function store(StoreLeaveRequest $request)
    {
        $leaveRequest = $this->service->create(
            StoreLeaveRequestDTO::fromRequest($request->validated())
        );

        return (new LeaveRequestStoreResource($leaveRequest))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function approve(ProcessLeaveRequest $request, LeaveRequest $leave)
    {
        $approver = Employee::find($request->approver_id);
        Gate::forUser($approver)->authorize(LeaveRequestApprovalEvent::APPROVE, $leave);
        $dto = ProcessLeaveRequestDTO::fromRequest($request->validated());
        $result = $this->service->approve($leave, $dto);

        return response()->json($result);
    }

    public function reject(ProcessLeaveRequest $request, LeaveRequest $leave)
    {
        $approver = Employee::find($request->approver_id);
        Gate::forUser($approver)->authorize(LeaveRequestApprovalEvent::APPROVE, $leave);
        $dto = ProcessLeaveRequestDTO::fromRequest($request->validated());
        $result = $this->service->reject($leave, $dto);

        return response()->json($result);
    }
}
