@extends('layouts.admin')

@section('title', 'Đơn hàng - Admin')
@section('page-title', 'Quản Lý Đơn Hàng')

@section('content')
@php
    $orderItems = $orders ?? collect();
    $currentStatus = (string) request('status', request('order_status', ''));
@endphp

<div class="card">
    <form method="GET" action="{{ url()->current() }}" class="bar">
        <select name="status" id="fs" onchange="this.form.submit()">
            <option value="" {{ $currentStatus === '' ? 'selected' : '' }}>Tất cả trạng thái</option>
            <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
            <option value="shipping" {{ in_array($currentStatus, ['shipping', 'delivering'], true) ? 'selected' : '' }}>Đang giao</option>
            <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
            <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
        </select>
    </form>

    <div class="wrap">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Thanh toán</th>
                    <th class="r">Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orderItems as $order)
                    @php
                        $rawStatus = $order->order_status instanceof \BackedEnum
                            ? (string) $order->order_status->value
                            : (string) ($order->order_status ?? $order->status ?? 'pending');
                        $statusInfo = match (strtolower($rawStatus)) {
                            'completed' => ['label' => 'Đã hoàn thành', 'class' => 'completed'],
                            'shipping', 'delivering' => ['label' => 'Đang giao', 'class' => 'shipping'],
                            'cancelled' => ['label' => 'Đã hủy', 'class' => 'cancelled'],
                            default => ['label' => 'Chờ xử lý', 'class' => 'pending'],
                        };

                        $orderCode = $order->order_code ?? $order->code ?? ('#' . $order->id);
                        $customerName = $order->customer_name ?? $order->recipient_name ?? optional($order->user)->full_name ?? optional($order->user)->name ?? 'Khách lẻ';
                        $dateText = $order->created_at ? \Illuminate\Support\Carbon::parse($order->created_at)->format('d/m/Y') : '—';

                        $payRaw = $order->payment_method instanceof \BackedEnum
                            ? (string) $order->payment_method->value
                            : (string) ($order->payment_method ?? 'COD');
                        $payText = match (strtolower($payRaw)) {
                            'bank_transfer', 'transfer', 'vietqr', 'chuyen_khoan' => 'Chuyển khoản',
                            default => strtoupper($payRaw),
                        };
                        $totalAmount = (int) ($order->total_amount ?? $order->total ?? 0);
                    @endphp
                    <tr>
                        <td><b>{{ $orderCode }}</b></td>
                        <td>{{ $customerName }}</td>
                        <td>{{ $dateText }}</td>
                        <td>{{ $payText }}</td>
                        <td class="r">{{ number_format($totalAmount, 0, ',', '.') }}₫</td>
                        <td><span class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span></td>
                        <td>
                            @if (\Illuminate\Support\Facades\Route::has('admin.orders.show'))
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn sm">Chi tiết</a>
                            @else
                                <button type="button" class="btn sm">Chi tiết</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#999">Chưa có đơn hàng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($orderItems, 'links'))
        <div class="pagination-box">{{ $orderItems->withQueryString()->links() }}</div>
    @endif
</div>
@endsection