@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . ($order->id ?? ''))

@section('content')
    <h1>Chi tiết đơn hàng #{{ $order->id ?? '' }}</h1>
    <p><strong>Trạng thái:</strong> {{ is_object($order->status ?? null) ? ($order->status->value ?? '') : ($order->status ?? '') }}</p>
    <p><strong>Tổng tiền:</strong> {{ number_format((float) ($order->total_amount ?? 0), 0, ',', '.') }} đ</p>

    <a href="{{ route('admin.orders.index') }}">Quay lại danh sách đơn</a>
@endsection