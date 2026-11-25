<?php

namespace App\Enums;

enum OrderStatus: string
{
    //
    case Pending = 'pending';
    case Shipping = 'shipping';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /** Nhãn tiếng Việt hiển thị trên giao diện */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ xử lý',
            self::Shipping => 'Đang giao',
            self::Completed => 'Đã hoàn thành',
            self::Cancelled => 'Đã hủy',
        };
    }

    /** Các trạng thái được chuyển tới từ trạng thái hiện tại (BR1-UC10) */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending   => [self::Shipping, self::Cancelled],
            self::Shipping  => [self::Completed, self::Cancelled],
            self::Completed, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }
}
