{{-- Pop-up yêu cầu đăng nhập (mở khi Guest gửi đánh giá và server trả về 401 JSON) --}}
<div class="modal" id="authModal">
    <div class="modal-content" style="text-align: center;">
        <span class="close-modal" onclick="closeAuthModal()">×</span>
        <i class="fa fa-lock" style="font-size: 40px; color: #ff523b; margin-bottom: 15px;"></i>
        <h3>Yêu Cầu Đăng Nhập</h3>
        <p style="margin: 15px 0;">Vui lòng đăng nhập để đánh giá sản phẩm.</p>
        <a href="{{ route('account.index') }}" class="btn">Chuyển Đến Trang Đăng Nhập</a>
    </div>
</div>