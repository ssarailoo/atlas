<?php

namespace App\QueryFilters\LeaveReqeust;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class StatusFilter extends Filter
{
    protected function apply(Builder $query, $value)
    {
        $query->where('status', $value);
    }

    protected function filterName(): string
    {
        return 'status';
    }
}
