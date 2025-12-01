<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\StoreLeaveRequestDTO;
use App\Http\Requests\StoreLeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LeaveRequestController extends Controller
{
    public function __construct(readonly  private LeaveRequestService $service)
    {

    }

    public function store(StoreLeaveRequest $request)
    {
        try {
            $leaveRequest= $this->service->create(StoreLeaveRequestDTO::fromRequest($request->validated()));
        }catch (\Exception $exception){

        }

        return response()->json([
            'data'=>$leaveRequest
        ],Response::HTTP_CREATED);
    }
}
