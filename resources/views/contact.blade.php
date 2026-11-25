@extends('layouts.app')

@section('title', 'Liên Hệ - Hucestore')

@section('content')
    <div class="small-container" style="margin-top: 40px; margin-bottom: 60px;">
        <h2>Gửi Thắc Mắc Cho Chúng Tôi</h2>
        <div class="row" style="margin-top: 20px; align-items: flex-start;">
            <div class="col-2 checkout-form">
                <form id="contactForm">
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="name" placeholder="Nhập tên của bạn" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Nhập email" required>
                    </div>
                    <div class="form-group">
                        <label>Lời nhắn</label>
                        <textarea name="message" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" placeholder="Nội dung..." required></textarea>
                    </div>
                    <p id="contactFeedback" style="display: none; padding: 10px 12px; margin-bottom: 12px; border-radius: 4px; background: #e8f5e9; color: #2e7d32; font-size: 13px;"></p>
                    <button type="submit" class="btn">Gửi Liên Hệ</button>
                </form>
            </div>
            <div class="col-2" style="padding-left: 30px;">
                <h3>Thông Tin Cửa Hàng</h3>
                <p style="margin-top: 15px;"><i class="fa fa-map-marker"></i> Địa chỉ: Hà Nội, Việt Nam</p>
                <p style="margin-top: 10px;"><i class="fa fa-phone"></i> Hotline: 0987.654.321</p>
                <p style="margin-top: 10px;"><i class="fa fa-envelope"></i> Email: support@Hucestore.com</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('contactForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const feedback = document.getElementById('contactFeedback');
            feedback.textContent = 'Cảm ơn bạn! Chúng tôi đã nhận được lời nhắn và sẽ phản hồi sớm.';
            feedback.style.display = 'block';
            e.target.reset();
        });
    </script>
@endpush