<?php

namespace App\Enums;

enum ReviewStatus: string
{
    //
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    /** Nhãn tiếng Việt hiển thị trên giao diện */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ duyệt',
            self::Approved => 'Đã duyệt',
            self::Rejected => 'Đã từ chối',
        };
    }
}
