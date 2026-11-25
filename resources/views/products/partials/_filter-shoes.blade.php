<form method="GET" action="{{ route('products.index') }}" class="filter-bar" id="shoeFilterForm">
    @if(!empty($filters['category']))
        <input type="hidden" name="category" value="{{ $filters['category'] }}">
    @endif

    <!-- Menu lọc 1: giá / giảm giá (sale_price != NULL) -->
    <select name="sort" onchange="this.form.submit()">
        <option value="default" @selected(($filters['sort'] ?? 'default') === 'default')>Sắp xếp mặc định (Bán chạy nhất)</option>
        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Giá: Thấp đến Cao</option>
        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Giá: Cao đến Thấp</option>
        <option value="sale" @selected(($filters['sort'] ?? '') === 'sale')>Chỉ sản phẩm đang giảm giá</option>
    </select>

    <!-- Menu lọc 2: giới tính (products.gender) -->
    <select name="gender" onchange="this.form.submit()">
        <option value="" @selected(empty($filters['gender']))>Tất cả giới tính</option>
        <option value="male" @selected(($filters['gender'] ?? '') === 'male')>Nam</option>
        <option value="female" @selected(($filters['gender'] ?? '') === 'female')>Nữ</option>
        <option value="unisex" @selected(($filters['gender'] ?? '') === 'unisex')>Unisex</option>
    </select>

    <!-- Menu lọc 3: chỉ hiện mẫu còn size đã chọn (product_variants.size, stock_quantity > 0) -->
    <select name="size" onchange="this.form.submit()">
        <option value="" @selected(empty($filters['size']))>Lọc theo Size (Còn hàng)</option>
        @foreach($sizes as $size)
            <option value="{{ $size }}" @selected((string) ($filters['size'] ?? '') === (string) $size)>
                Size {{ $size }}
            </option>
        @endforeach
    </select>
</form>