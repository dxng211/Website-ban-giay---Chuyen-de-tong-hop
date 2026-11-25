<form method="GET" action="{{ route('accessories.index') }}" class="filter-bar" id="accessoryFilterForm">
    <!-- Lọc 1: giá / giảm giá -->
    <select name="sort" onchange="this.form.submit()">
        <option value="default" @selected(($filters['sort'] ?? 'default') === 'default')>Mặc định (Bán chạy nhất)</option>
        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Giá: Thấp đến Cao</option>
        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Giá: Cao đến Thấp</option>
        <option value="sale" @selected(($filters['sort'] ?? '') === 'sale')>Chỉ sản phẩm giảm giá</option>
    </select>

    <!-- Lọc 2: loại phụ kiện (value = categories.id, chỉ các danh mục group_type = 'accessory') -->
    <select name="category" onchange="this.form.submit()">
        <option value="" @selected(empty($filters['category']))>Tất cả loại phụ kiện</option>
        @foreach($accessoryCategories as $category)
            <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</form>