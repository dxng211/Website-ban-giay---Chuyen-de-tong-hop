<?php

namespace App\Services\Clients;

/**
 * Dựng URL ảnh mã QR VietQR (Quick Link). Cấu hình ngân hàng trong config('services.vietqr').
 */
class VietQrClient
{
    /**
     * URL ảnh QR có sẵn số tiền và nội dung chuyển khoản là mã đơn (BR2-UC06).
     */
    public function generateQrUrl(int $amount, string $orderCode): string
    {
        // TODO: ghép URL từ bank_id, account_no, template, amount, addInfo = $orderCode; lỗi cấu hình -> throw PaymentException
    }
}
