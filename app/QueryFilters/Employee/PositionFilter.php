<?php

namespace App\QueryFilters\Employee;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PositionFilter extends Filter
{
    protected function apply(Builder $query, $value)
    {
        $query->where('position', $value);
    }

    protected function filterName(): string
    {
        return 'position';
    }
}
