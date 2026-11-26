<div class="dashboard-stat-cards">
    <div class="stat-card">
        <span>Doanh thu</span>
        <strong>{{ number_format((float) ($revenue ?? 0), 0, ',', '.') }} đ</strong>
    </div>
    <div class="stat-card">
        <span>Số đơn hàng</span>
        <strong>{{ $orderCount ?? 0 }}</strong>
    </div>
</div>