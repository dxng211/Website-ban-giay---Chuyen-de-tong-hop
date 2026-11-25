<div class="account-page">
    <div class="container">
        <div class="row">
            <div class="col-2">
                <img src="{{ asset('images/image1.png') }}" width="100%" alt="Account Hero">
            </div>
            <div class="col-2">
                <div class="form-container">
                    <div class="form-btn">
                        <span onclick="login()">Đăng Nhập</span>
                        <span onclick="register()">Đăng Ký</span>
                        <hr id="Indicator">
                    </div>

                    @if($errors->any())
                        <div style="text-align: left; padding: 0 20px; margin-bottom: 10px; font-size: 12px; color: #ff523b;">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- Đăng nhập bằng users.email + password -->
                    <form id="LoginForm" action="{{ route('login') }}" method="POST">
                        @csrf
                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                        <input type="password" name="password" placeholder="Mật khẩu" required>
                        <button type="submit" class="btn">Đăng Nhập</button>
                        <a href="javascript:void(0)">Quên mật khẩu?</a>
                    </form>

                    <!-- Đăng ký: users.full_name, email (duy nhất), password -->
                    <form id="RegForm" action="{{ route('register') }}" method="POST">
                        @csrf
                        <input type="text" name="full_name" placeholder="Họ và tên" value="{{ old('full_name') }}" required>
                        <input type="email" name="email" placeholder="Địa chỉ Email" value="{{ old('email') }}" required>
                        <input type="password" name="password" placeholder="Mật khẩu (tối thiểu 6 ký tự)" minlength="6" required>
                        <button type="submit" class="btn">Đăng Ký</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>