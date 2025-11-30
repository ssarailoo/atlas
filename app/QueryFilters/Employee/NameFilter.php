<?php

namespace App\QueryFilters\Employee;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class NameFilter extends Filter
{
    protected function apply(Builder $query, $value)
    {
        $query->where('full_name', $value);
    }

    protected function filterName(): string
    {
        return 'name';
    }
}
