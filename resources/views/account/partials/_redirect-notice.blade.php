@php
    $redirectReason = request('redirect');
    $noticeMessage = match (true) {
        !empty($hasIntendedCheckout) || $redirectReason === 'checkout' => 'Vui lòng đăng nhập hoặc đăng ký để tiếp tục thanh toán. Giỏ hàng của bạn được giữ lại.',
        $redirectReason === 'review' => 'Vui lòng đăng nhập hoặc đăng ký để gửi đánh giá sản phẩm.',
        default => null,
    };
@endphp

@if($noticeMessage)
    <div class="small-container" id="redirectNotice" style="margin-top: 20px;">
        <p style="padding: 12px 15px; background: #fff3f1; border: 1px solid #ff523b; border-radius: 6px; color: #ff523b;">
            {{ $noticeMessage }}
        </p>
    </div>
@endif