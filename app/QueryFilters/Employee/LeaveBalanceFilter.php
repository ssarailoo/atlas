<?php

namespace App\QueryFilters\Employee;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class LeaveBalanceFilter extends Filter
{
    protected function apply(Builder $query, $value)
    {

        if (is_array($value)) {
            if (isset($value['min'])) {
                $query->where('leave_balance', '>=', $value['min']);
            }
            if (isset($value['max'])) {
                $query->where('leave_balance', '<=', $value['max']);
            }
        } else {
            $query->where('leave_balance', $value);
        }
    }

    protected function filterName(): string
    {
        return 'leave_balance';
    }
}
