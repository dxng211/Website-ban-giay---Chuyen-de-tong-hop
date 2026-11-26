<div class="dashboard-orders-status">
    <h3>Đơn hàng theo trạng thái</h3>
    <ul>
        @forelse (($ordersByStatus ?? []) as $statusName => $count)
            <li>{{ $statusName }}: <strong>{{ $count }}</strong></li>
        @empty
            <li>Chưa có dữ liệu trạng thái đơn hàng.</li>
        @endforelse
    </ul>
</div>