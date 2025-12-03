<?php

namespace App\Http\Requests;

use App\Enums\LeaveRequestStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexLeaveRequestOfEmployeeReqeust extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "employee_id"=>["required","exists:employees,id"],
            "status"=>["nullable",Rule::in(LeaveRequestStatusEnum::getValues())]
        ];
    }
}
