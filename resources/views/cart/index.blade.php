@extends('layouts.app')

@section('title', 'Giỏ Hàng - Hucestore')

@section('content')
    @php
        $items = $cart?->items ?? collect();
        $total = (int) ($cart?->total ?? 0);
        $checkoutTarget = Auth::check()
            ? route('checkout')
            : route('account', ['redirect' => 'checkout']);
    @endphp

    <div class="small-container cart-page" style="margin-bottom: 60px;">
        @if($items->isEmpty())
            <div id="emptyCartBox" style="text-align: center; padding: 60px 0;">
                <h3>Giỏ hàng của bạn đang trống</h3>
                <p style="margin: 12px 0 20px; color: #777;">Hãy chọn những đôi giày hoặc phụ kiện yêu thích để thêm vào giỏ nhé.</p>
                <a href="{{ route('products.index') }}" class="btn">Tiếp Tục Mua Sắm</a>
            </div>
        @else
            <div id="cartContent">
                <table id="cartTable">
                    <tr>
                        <th>Sản phẩm (Product)</th>
                        <th>Số lượng (Quantity)</th>
                        <th>Tạm tính (Subtotal)</th>
                    </tr>
                    @foreach($items as $item)
                        @include('cart.partials._item-row', ['item' => $item])
                    @endforeach
                </table>

                <div class="total-price">
                    <table>
                        <tr>
                            <td><strong>Tổng thanh toán (Total)</strong></td>
                            <td><strong id="cartTotal">{{ number_format($total, 0, ',', '.') }}₫</strong></td>
                        </tr>
                    </table>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <a href="{{ $checkoutTarget }}" class="btn" id="checkoutBtn">Tiến Hành Thanh Toán →</a>
                </div>
            </div>

            <div id="emptyCartBox" style="display: none; text-align: center; padding: 60px 0;">
                <h3>Giỏ hàng của bạn đang trống</h3>
                <p style="margin: 12px 0 20px; color: #777;">Hãy chọn những đôi giày hoặc phụ kiện yêu thích để thêm vào giỏ nhé.</p>
                <a href="{{ route('products.index') }}" class="btn">Tiếp Tục Mua Sắm</a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/cart.js') }}"></script>
@endpush