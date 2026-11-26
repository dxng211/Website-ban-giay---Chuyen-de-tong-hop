<div class="dashboard-recent-orders">
    <h3>Đơn hàng gần đây</h3>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Trạng thái</th>
                <th>Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            @forelse (($recentOrders ?? []) as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->status ?? 'pending' }}</td>
                    <td>{{ number_format((float) ($order->total_amount ?? $order->total ?? 0), 0, ',', '.') }} đ</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Chưa có đơn hàng gần đây.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>