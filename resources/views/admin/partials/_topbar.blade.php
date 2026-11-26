<header class="admin-topbar">
    <div class="topbar-left">
        <span>Xin chào, {{ auth()->user()->name ?? 'Admin' }}</span>
    </div>
    <div class="topbar-right">
        <a href="{{ url('/') }}">Trang chủ</a>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit">Đăng xuất</button>
        </form>
    </div>
</header>