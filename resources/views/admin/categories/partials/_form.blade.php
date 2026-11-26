@php
    $cat = $category ?? null;
@endphp

<div>
    <label for="name">Tên danh mục</label>
    <input
        id="name"
        type="text"
        name="name"
        value="{{ old('name', $cat->name ?? '') }}"
        placeholder="VD: Giày chạy bộ, Tất & Dây giày..."
        required
    >
</div>

<div>
    <label for="chart_key">Chart Key bảng size (VD: shoe_vn)</label>
    <input
        id="chart_key"
        type="text"
        name="chart_key"
        value="{{ old('chart_key', $cat->chart_key ?? '') }}"
        placeholder="Để trống nếu không gắn bảng size"
    >
</div>