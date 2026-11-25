{{-- Vỏ rỗng pop-up bảng hướng dẫn chọn size (đặt trong layouts/app.blade.php).
     Khi bấm nút "Hướng dẫn chọn kích cỡ" (có data-url="{{ route('categories.size-chart', $key) }}"),
     JS gọi AJAX lấy HTML từ partials/size-chart-table và đổ vào #sizeGuideBody --}}
<div class="modal" id="sizeGuideModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeSizeGuide()">×</span>
        <div id="sizeGuideBody">
            <p style="font-size: 13px; color: #777;">Đang tải bảng kích cỡ...</p>
        </div>
    </div>
</div>