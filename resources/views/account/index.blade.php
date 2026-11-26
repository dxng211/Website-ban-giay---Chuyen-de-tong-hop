@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account.css') }}">
@endpush

@section('title', 'Tài Khoản - Hucestore')

@section('content')
    @guest
        @include('account.partials._redirect-notice')

        @include('account.partials._auth-forms')
    @else
        <div class="small-container account-dashboard" style="margin-top: 40px; margin-bottom: 60px;">
            <!-- Thanh tiêu đề + nút Đăng xuất gọn góc phải -->
            <div class="account-header-bar">
                <div>
                    <h2 style="margin: 0;">Tài Khoản Của Tôi</h2>
                    <p style="font-size: 13px; color: #777; margin-top: 4px;">
                        Xin chào, <strong>{{ $user->full_name }}</strong> ({{ $user->email }})
                    </p>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout-outline">
                        <i class="fa fa-sign-out"></i> Đăng xuất
                    </button>
                </form>
            </div>

            <!-- 2 thẻ cân chiều cao: Thông tin cá nhân & Đổi mật khẩu -->
            <div class="account-grid">
                <div class="account-card checkout-form">
                    <h3>Thông Tin Cá Nhân</h3>
                    <form action="{{ route('account.update') }}" method="POST" class="account-card-form">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email (không thể thay đổi)</label>
                            <input type="email" value="{{ $user->email }}" readonly style="background: #f1f1f1; color: #666; cursor: not-allowed;">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ mặc định (tự điền khi thanh toán)</label>
                            <input type="text" name="default_address" value="{{ old('default_address', $user->default_address) }}" placeholder="Số nhà, đường, quận/huyện, tỉnh/thành">
                        </div>
                        <div class="account-card-actions">
                            <button type="submit" class="btn" style="margin: 0;">Lưu Thay Đổi</button>
                        </div>
                    </form>
                </div>

                <div class="account-card checkout-form">
                    @include('account.partials._password-form')
                </div>
            </div>

            <!-- Danh sách đơn hàng -->
            <h2 style="margin-top: 45px;">Đơn Hàng Của Tôi</h2>
            <div style="margin: 18px 0 40px; overflow-x: auto;">
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
                            <td colspan="5" style="text-align: center; padding: 28px 12px; color: #777;">
                                Bạn chưa có đơn hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </table>
            </div>
        </div>
    @endguest
@endsection

@push('scripts')
    @guest
        <script src="{{ asset('js/account.js') }}"></script>
    @endguest
@endpush