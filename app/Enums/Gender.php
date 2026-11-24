<?php

namespace App\Enums;

enum Gender: string
{
    //
    case Male = 'male';
    case Female = 'female';
    case Unisex = 'unisex';

    /** Nhãn tiếng Việt hiển thị trên giao diện */
    public function label(): string
    {
        return match ($this) {
            self::Male => 'Nam',
            self::Female => 'Nữ',
            self::Unisex => 'Unisex',
        };
    }
}
