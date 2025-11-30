<?php

namespace App\Http\Requests;

use App\Enums\LeaveRequestTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;

class StoreLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return  true;
    }
    protected function prepareForValidation(): void
    {
        if ($this->filled('start_date')) {
            $this->merge([
                'start_date' => Jalalian::fromFormat('Y-m-d', $this->start_date)->toCarbon()
            ]);
        }
        if ($this->filled('end_date')) {
            $this->merge([
                'end_date' => Jalalian::fromFormat('Y-m-d', $this->end_date)->toCarbon()
            ]);
        }
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type'  => ['required', Rule::in(LeaveRequestTypeEnum::getValues())],
            'start_date'  => ['required', 'date', 'after_or_equal:today'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time'  => ['nullable', 'date_format:H:i'],
            'end_time'    => ['nullable', 'date_format:H:i'],
            'reason'      => ['nullable', 'string', 'max:1000'],
        ];
    }



}
