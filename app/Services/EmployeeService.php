<?php

namespace App\Services;

use App\Models\Employee;
use App\QueryFilters\Employee\LeaveBalanceFilter;
use App\QueryFilters\Employee\NameFilter;
use App\QueryFilters\Employee\PositionFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

readonly class EmployeeService
{
    public function getEmployees(array $filters)
    {
        $query = $this->query();
        $pipelineFilters = $this->getPipelineFilters();

        return app(Pipeline::class)
            ->send($query)
            ->through($pipelineFilters)
            ->thenReturn()
            ->paginate($filters['per_page'] ?? 15)
            ->appends(request()->query());
    }

    private function query(): Builder
    {
        return Employee::query();
    }

    private function getPipelineFilters(): array
    {
        return [
            NameFilter::class,
            PositionFilter::class,
            LeaveBalanceFilter::class
        ];
    }
}
