<?php

namespace App\Http\Requests;

use App\Rules\LeaveBalanceRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'position' => ['sometimes', 'string'],
            'leave_balance' => ['sometimes', 'integer',new LeaveBalanceRule()],
            'leave_balance.min' => ['sometimes', 'integer'],
            'leave_balance.max' => ['sometimes', 'integer'],
        ];
    }

}
