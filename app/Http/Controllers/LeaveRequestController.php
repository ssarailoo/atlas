<?php

namespace App\Http\Controllers;

use App\Constants\LeaveRequestApprovalEvent;
use App\DataTransferObjects\IndexLeaveRequestOfEmployeeDTO;
use App\DataTransferObjects\ProcessLeaveRequestDTO;
use App\DataTransferObjects\StoreLeaveRequestDTO;
use App\Http\Requests\IndexLeaveRequestOfEmployeeReqeust;
use App\Http\Requests\ProcessLeaveRequest;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Resources\LeaveRequestIndexResource;
use App\Http\Resources\LeaveRequestProcessResource;
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
        $updated = $this->service->approve($leave, $dto);

        return (new LeaveRequestProcessResource($updated))->response();
    }

    public function reject(ProcessLeaveRequest $request, LeaveRequest $leave)
    {
        $approver = Employee::find($request->approver_id);
        Gate::forUser($approver)->authorize(LeaveRequestApprovalEvent::REJECT, $leave);
        $dto = ProcessLeaveRequestDTO::fromRequest($request->validated());
        $updated = $this->service->reject($leave, $dto);

        return (new LeaveRequestProcessResource($updated))->response();
    }

    public function indexOfEmployee(IndexLeaveRequestOfEmployeeReqeust $request)
    {
        $dto = IndexLeaveRequestOfEmployeeDTO::fromRequest($request->validated());
        $leaveRequests = $this->service->getLeaveRequestsOfEmployee($dto);
        return  LeaveRequestIndexResource::collection($leaveRequests)->response();
    }
}
