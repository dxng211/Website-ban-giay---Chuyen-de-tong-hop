@php
    $currentImage = $product->image ?? null;
@endphp

<div class="product-image-uploader">
    <label for="image">Ảnh sản phẩm</label>
    <input id="image" type="file" name="image" accept="image/*">

    @if (!empty($currentImage))
        <div class="current-image">
            <img src="{{ str_starts_with($currentImage, 'http') ? $currentImage : asset($currentImage) }}" alt="Ảnh sản phẩm" style="max-width: 120px; height: auto;">
        </div>
    @endif
</div>