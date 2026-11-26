<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}">Admin</a>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.products.index') }}">Sản phẩm & Tồn kho</a></li>
            <li><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
            <li><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
            <li><a href="{{ route('admin.size-charts.index') }}">Bảng size</a></li>
            <li><a href="{{ route('admin.reviews.index') }}">Đánh giá</a></li>
            <li><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
        </ul>
    </nav>
</aside>