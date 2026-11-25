@extends('layouts.app')

@section('title', 'Tài Khoản - Hucestore')

@section('content')
    @guest
        @if(!empty($hasIntendedCheckout) || request('redirect') === 'checkout')
            <div class="small-container" id="redirectNotice" style="margin-top: 20px;">
                <p style="padding: 12px 15px; background: #fff3f1; border: 1px solid #ff523b; border-radius: 6px; color: #ff523b;">
                    Vui lòng đăng nhập hoặc đăng ký để tiếp tục thanh toán. Giỏ hàng của bạn được giữ lại.
                </p>
            </div>
        @endif

        @include('account.partials._auth-forms')
    @else
        <div class="small-container" style="margin-top: 40px;">
            <h2>Thông Tin Cá Nhân</h2>
            <div class="row" style="margin-top: 20px; align-items: flex-start;">
                <div class="col-2 checkout-form">
                    <form action="{{ route('account.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email (không thể thay đổi)</label>
                            <input type="email" value="{{ $user->email }}" readonly style="background:#eee;">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ mặc định (tự điền khi thanh toán)</label>
                            <input type="text" name="default_address" value="{{ old('default_address', $user->default_address) }}">
                        </div>
                        <button type="submit" class="btn" style="margin-bottom:0;">Lưu thay đổi</button>
                    </form>
                </div>
                <div class="col-2 checkout-form" style="padding-left: 30px;">
                    @include('account.partials._password-form')

                    <hr style="margin: 15px 0; border: none; border-top: 1px solid #eee;">

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn" style="background:#333;">Đăng Xuất</button>
                    </form>
                </div>
            </div>

            <h2 style="margin-top: 50px;">Đơn Hàng Của Tôi</h2>
            <div style="margin: 20px 0 40px; overflow-x: auto;">
                <table>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Sản phẩm</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>
                    </tr>
                    @forelse($orders as $order)
                        @php
                            $statusValue = is_object($order->order_status) ? $order->order_status->value : $order->order_status;
                            $paymentValue = is_object($order->payment_method) ? $order->payment_method->value : $order->payment_method;
                        @endphp
                        <tr>
                            <td>{{ $order->order_code }}<br><small>{{ optional($order->created_at)->format('d/m/Y') }}</small></td>
                            <td style="font-size:12px;">
                                @foreach($order->items as $item)
                                    {{ $item->product_name_snapshot }} ({{ $item->size_snapshot }}) x{{ $item->quantity }}@if(!$loop->last)<br>@endif
                                @endforeach
                            </td>
                            <td>{{ $paymentValue === 'bank_transfer' ? 'Chuyển khoản' : 'COD' }}</td>
                            <td class="order-status">{{ $statusValue }}</td>
                            <td>
                                {{ number_format((float) $order->total_amount, 0, ',', '.') }}₫
                                @if($statusValue === 'pending')
                                    <br>
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="background:none;border:none;padding:0;color:#ff523b;font-size:12px;cursor:pointer;text-decoration:underline;">Hủy đơn</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Bạn chưa có đơn hàng nào.</td>
                        </tr>
                    @endforelse
                </table>
            </div>
        </div>
    @endguest
@endsection

@push('scripts')
    @guest
        <script>
            var LoginForm = document.getElementById("LoginForm");
            var RegForm = document.getElementById("RegForm");
            var Indicator = document.getElementById("Indicator");
            var authPage = document.querySelector(".account-page");

            function register() {
                RegForm.style.transform = "translateX(0px)";
                LoginForm.style.transform = "translateX(0px)";
                Indicator.style.transform = "translateX(100px)";
            }
            function login() {
                RegForm.style.transform = "translateX(300px)";
                LoginForm.style.transform = "translateX(300px)";
                Indicator.style.transform = "translateX(0px)";
            }

            if (authPage && authPage.dataset.openRegister === "1") {
                register();
            } else {
                login();
            }
        </script>
    @endguest
@endpush