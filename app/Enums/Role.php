<?php

namespace App\Enums;

enum Role: string
{
    //
    case Customer = 'customer';
    case Admin = 'admin';

    /** Nhãn tiếng Việt hiển thị trên giao diện */
    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Khách hàng',
            self::Admin => 'Quản trị viên',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
