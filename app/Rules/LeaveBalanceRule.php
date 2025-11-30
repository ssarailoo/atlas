<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class LeaveBalanceRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $data = request()->input($attribute, $value);

        $exact = isset($data) && !is_array($data);
        $min = isset($data['min']);
        $max = isset($data['max']);

        if ($exact && ($min || $max)) {
            $fail('Provide either an exact value OR a min/max range, not both.');
            return;
        }

        if ($min && $max && $data['min'] > $data['max']) {
            $fail('The minimum leave balance cannot be greater than the maximum.');
        }
    }
}
