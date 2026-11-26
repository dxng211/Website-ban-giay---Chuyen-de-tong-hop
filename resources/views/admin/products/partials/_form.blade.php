@php
    $prod = $product ?? null;
@endphp

<div>
    <label for="name">Tên sản phẩm</label>
    <input id="name" type="text" name="name" value="{{ old('name', $prod->name ?? '') }}" required>
</div>

<div>
    <label for="price">Giá bán</label>
    <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $prod->price ?? '') }}" required>
</div>

<div>
    <label for="category_id">Danh mục ID</label>
    <input id="category_id" type="number" name="category_id" value="{{ old('category_id', $prod->category_id ?? '') }}">
</div>

<div>
    <label for="description">Mô tả</label>
    <textarea id="description" name="description" rows="3">{{ old('description', $prod->description ?? '') }}</textarea>
</div>

@include('admin.products.partials._image-uploader', ['product' => $prod])