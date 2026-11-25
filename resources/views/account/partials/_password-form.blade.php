<h3 style="margin-bottom: 15px;">Đổi Mật Khẩu</h3>
<form action="{{ route('account.password') }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="current_password">Mật khẩu hiện tại</label>
        <input id="current_password" type="password" name="current_password" required>
        @error('current_password')
            <small style="color:#ff523b; font-size:12px;">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="new_password">Mật khẩu mới (tối thiểu 6 ký tự)</label>
        <input id="new_password" type="password" name="password" minlength="6" required>
        @error('password')
            <small style="color:#ff523b; font-size:12px;">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="new_password_confirmation">Nhập lại mật khẩu mới</label>
        <input id="new_password_confirmation" type="password" name="password_confirmation" minlength="6" required>
    </div>

    <button type="submit" class="btn" style="margin-bottom: 15px;">Đổi Mật Khẩu</button>
</form>