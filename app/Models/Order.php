<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([OrderObserver::class])] // cập nhật sold_count khi đơn chuyển sang completed
class Order extends Model
{
    //
    protected $fillable = [
        'order_code',
        'user_id',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'payment_method',
        'payment_status',
        'order_status',
        'total_amount',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'order_status' => OrderStatus::class,
            'total_amount' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** Khách chỉ tự hủy được khi đơn còn "Chờ xử lý" (UC07) */
    public function canBeCancelledByCustomer(): bool
    {
        return $this->order_status === OrderStatus::Pending;
    }
}
