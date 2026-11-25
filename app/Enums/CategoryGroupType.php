<?php

namespace App\Enums;

enum CategoryGroupType: string
{
    //
    case Shoe = 'shoe';
    case Accessory = 'accessory';

    /** Nhãn tiếng Việt hiển thị trên giao diện */
    public function label(): string
    {
        return match ($this) {
            self::Shoe => 'Giày',
            self::Accessory => 'Phụ kiện',
        };
    }
}
