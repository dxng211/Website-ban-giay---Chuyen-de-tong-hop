@extends('layouts.admin')

@section('title', 'Tổng quan - Admin')
@section('page-title', 'Báo Cáo Thống Kê')

@section('content')
@php
    $revenueValue = (int) ($revenue ?? $totalRevenue ?? 0);
    $orderCountValue = (int) ($orderCount ?? $totalOrders ?? 0);
    $statusMap = is_array($orderCountByStatus ?? null)
        ? $orderCountByStatus
        : (is_array($statusCounts ?? null) ? $statusCounts : []);

    $pendingCount = (int) ($pendingOrderCount ?? ($statusMap['pending'] ?? 0));
    $shippingCount = (int) ($statusMap['shipping'] ?? ($statusMap['delivering'] ?? 0));
    $completedCount = (int) ($statusMap['completed'] ?? 0);
    $cancelledCount = (int) ($statusMap['cancelled'] ?? 0);

    $lowStockCount = isset($lowStockProducts)
        ? (is_countable($lowStockProducts) ? count($lowStockProducts) : 0)
        : (int) ($lowStockCount ?? 0);

    $maxStatus = max(1, $pendingCount, $shippingCount, $completedCount, $cancelledCount);
    $hPending = $pendingCount > 0 ? max(16, (int) round(($pendingCount / $maxStatus) * 140)) : 8;
    $hShipping = $shippingCount > 0 ? max(16, (int) round(($shippingCount / $maxStatus) * 140)) : 8;
    $hCompleted = $completedCount > 0 ? max(16, (int) round(($completedCount / $maxStatus) * 140)) : 8;
    $hCancelled = $cancelledCount > 0 ? max(16, (int) round(($cancelledCount / $maxStatus) * 140)) : 8;

    $topProducts = $bestSellers ?? collect();
    $ordersList = $recentOrders ?? collect();
@endphp

<!-- GET /api/admin/reports/summary -->
<div class="stats">
    <div class="stat"><small>Tổng doanh thu (đơn hoàn thành)</small><b class="price">{{ number_format($revenueValue, 0, ',', '.') }}₫</b></div>
    <div class="stat"><small>Tổng số đơn hàng</small><b>{{ number_format($orderCountValue, 0, ',', '.') }}</b></div>
    <div class="stat"><small>Chờ xử lý</small><b style="color:#f5a623">{{ number_format($pendingCount, 0, ',', '.') }}</b></div>
    <div class="stat"><small>Sản phẩm sắp hết (tổng tồn &lt; 10)</small><b>{{ number_format($lowStockCount, 0, ',', '.') }}</b></div>
</div>
<br>

<div class="grid2">
    <div class="card">
        <h3>Số đơn theo trạng thái</h3>
        <div class="chart">
            <div><i style="height:{{ $hPending }}px;background:#f5a623"></i>{{ $pendingCount }}<br>Chờ xử lý</div>
            <div><i style="height:{{ $hShipping }}px;background:#3498db"></i>{{ $shippingCount }}<br>Đang giao</div>
            <div><i style="height:{{ $hCompleted }}px;background:#27ae60"></i>{{ $completedCount }}<br>Hoàn thành</div>
            <div><i style="height:{{ $hCancelled }}px;background:#999"></i>{{ $cancelledCount }}<br>Đã hủy</div>
        </div>
    </div>

    <div class="card">
        <h3>Sản phẩm bán chạy</h3>
        <!-- GET /api/admin/reports/best-sellers -->
        <table>
            <tr><th>#</th><th>Sản phẩm</th><th class="r">Đã bán</th></tr>
            @forelse ($topProducts as $idx => $item)
                @php
                    $firstImg = is_iterable($item->images ?? null) ? collect($item->images)->first() : null;
                    $thumb = $item->thumbnail_url
                        ?? $item->image_url
                        ?? optional($firstImg)->url
                        ?? (optional($firstImg)->path ? asset('storage/' . ltrim($firstImg->path, '/')) : asset('images/product-11.jpg'));
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><img src="{{ $thumb }}" alt="{{ $item->name ?? '' }}">{{ $item->name ?? $item->title ?? 'Sản phẩm' }}</td>
                    <td class="r">{{ (int) ($item->sold_count ?? 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center;color:#999">Chưa có dữ liệu sản phẩm bán chạy.</td></tr>
            @endforelse
        </table>
    </div>
</div>

<div class="card">
    <h3>Đơn hàng mới nhất</h3>
    <div class="wrap">
        <table>
            <tr><th>Mã đơn</th><th>Khách hàng</th><th>Ngày đặt</th><th>Thanh toán</th><th class="r">Tổng tiền</th><th>Trạng thái</th></tr>
            @forelse ($ordersList as $order)
                @php
                    $rawStatus = $order->order_status instanceof \BackedEnum
                        ? (string) $order->order_status->value
                        : (string) ($order->order_status ?? $order->status ?? 'pending');
                    $statusInfo = match (strtolower($rawStatus)) {
                        'completed' => ['label' => 'Hoàn thành', 'class' => 'completed'],
                        'shipping', 'delivering' => ['label' => 'Đang giao', 'class' => 'shipping'],
                        'cancelled' => ['label' => 'Đã hủy', 'class' => 'cancelled'],
                        default => ['label' => 'Chờ xử lý', 'class' => 'pending'],
                    };
                    $payRaw = $order->payment_method instanceof \BackedEnum
                        ? (string) $order->payment_method->value
                        : (string) ($order->payment_method ?? 'COD');
                    $payText = match (strtolower($payRaw)) {
                        'bank_transfer', 'transfer', 'vietqr', 'chuyen_khoan' => 'Chuyển khoản',
                        default => strtoupper($payRaw),
                    };
                    $amount = (int) ($order->total_amount ?? $order->total ?? 0);
                @endphp
                <tr>
                    <td>{{ $order->order_code ?? $order->code ?? ('#' . $order->id) }}</td>
                    <td>{{ $order->customer_name ?? $order->recipient_name ?? optional($order->user)->full_name ?? optional($order->user)->name ?? 'Khách lẻ' }}</td>
                    <td>{{ $order->created_at ? \Illuminate\Support\Carbon::parse($order->created_at)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $payText }}</td>
                    <td class="r">{{ number_format($amount, 0, ',', '.') }}₫</td>
                    <td><span class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;color:#999">Chưa có đơn hàng gần đây.</td></tr>
            @endforelse
        </table>
    </div>
    <p style="margin-top:12px"><a href="{{ route('admin.orders.index') }}" class="btn sm">Xem tất cả đơn hàng</a></p>
</div>
@endsection