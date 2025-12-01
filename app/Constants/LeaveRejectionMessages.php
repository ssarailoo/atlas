<?php

namespace App\Constants;

class LeaveRejectionMessages
{
    public const THREE_DAY_GAP = 'حداقل 3 روز از آخرین درخواست مرخصی نگذشته است.';

    public const INSUFFICIENT_BALANCE = 'موجودی مرخصی سالانه کافی نیست.';

    public const MAX_DURATION_EXCEEDED = 'مدت زمان مرخصی بیش از حد مجاز است (روزانه: 30 روز، ساعتی: 8 ساعت).';

    public const MONTHLY_LIMIT_EXCEEDED = 'محدودیت ماهانه مرخصی پر شده است (سالانه: 2.5 روز، استعلاجی: 5 روز، ساعتی: 20 ساعت).';
}
