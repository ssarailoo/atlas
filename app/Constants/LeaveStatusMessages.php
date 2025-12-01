<?php

namespace App\Constants;

class LeaveStatusMessages
{

    public const PENDING_HR_FA = 'درخواست مرخصی با موفقیت ایجاد شد و برای بررسی به واحد منابع انسانی ارسال گردید.';
    public const PENDING_MANAGER_FA = 'درخواست مرخصی در انتظار تایید مدیر می‌باشد.';
    public const PENDING_CEO_FA = 'درخواست مرخصی در انتظار تایید مدیرعامل می‌باشد.';
    public const APPROVED_FA = 'درخواست مرخصی تایید شد.';
    public const REJECTED_FA = 'درخواست مرخصی رد شد.';
    public const DRAFT_FA = 'درخواست مرخصی به عنوان پیش‌نویس ذخیره شد و نیاز به بررسی و تایید دارد.';
    public const DUE_DATE_FA = 'مهلت درخواست مرخصی به پایان رسیده است.';


    public const PENDING_HR_EN = 'Leave request created successfully and sent to HR for review.';
    public const PENDING_MANAGER_EN = 'Leave request is pending manager approval.';
    public const PENDING_CEO_EN = 'Leave request is pending CEO approval.';
    public const APPROVED_EN = 'Leave request has been approved.';
    public const REJECTED_EN = 'Leave request has been rejected.';
    public const DRAFT_EN = 'Leave request saved as draft and requires review and approval.';
    public const DUE_DATE_EN = 'Leave request deadline has expired.';


    public static function get(string $status, string $locale = 'en'): string
    {
        $suffix = strtoupper($locale);
        $constant = strtoupper($status) . '_' . $suffix;

        return defined("self::$constant") ? constant("self::$constant") : 'Leave request has been submitted.';
    }
}
