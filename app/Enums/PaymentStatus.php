<?php

namespace App\Enums;

enum PaymentStatus: string
{
    //
    case Unpaid = 'unpaid';
    case Paid = 'paid';

    /** Nhãn tiếng Việt hiển thị trên giao diện */
    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Chưa thanh toán',
            self::Paid => 'Đã thanh toán',
        };
    }
}
