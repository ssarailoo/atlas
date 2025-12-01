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
        if ($this->filled('start_date') && !$this->isGregorian($this->start_date)) {
            $this->merge([
                'start_date' => Jalalian::fromFormat('Y-m-d', $this->start_date)->toCarbon()->format('Y-m-d')
            ]);
        }

        if ($this->filled('end_date') && !$this->isGregorian($this->end_date)) {
            $this->merge([
                'end_date' => Jalalian::fromFormat('Y-m-d', $this->end_date)->toCarbon()->format('Y-m-d')
            ]);
        }
    }

    private function isGregorian(string $date): bool
    {
        return intval(substr($date, 0, 4)) > 1600;
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        {
            $leaveType = $this->input('type');
            $rules = [
                'employee_id' => ['required', 'exists:employees,id'],
                'type'  => ['required', Rule::in(LeaveRequestTypeEnum::getValues())],
                'start_date'  => ['required', 'date', 'after_or_equal:today'],
                'reason'      => ['nullable', 'string', 'max:1000'],
            ];
            if ($leaveType === LeaveRequestTypeEnum::HOURLY->value) {
                $rules['end_date'] = ['nullable', 'date', 'after_or_equal:start_date'];
                $rules['start_time'] = ['required', 'date_format:H:i'];
                $rules['end_time'] = ['required', 'date_format:H:i', 'after:start_time'];
            }
            else {
                $rules['end_date'] = ['required', 'date', 'after:start_date'];
                $rules['start_time'] = ['nullable'];
                $rules['end_time'] = ['nullable'];
            }

            return $rules;
        }
    }



}
