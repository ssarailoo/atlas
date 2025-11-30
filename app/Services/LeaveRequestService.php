<?php

namespace App\Services;

use App\DataTransferObjects\StoreLeaveRequestDTO;
use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Builder;

readonly class LeaveRequestService
{
    public function create(StoreLeaveRequestDTO $data):LeaveRequest
    {
       return $this->query()->create($data->toArray());
    }

    private function query(): Builder
    {
        return LeaveRequest::query();
    }

}
