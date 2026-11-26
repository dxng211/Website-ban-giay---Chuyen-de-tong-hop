@php
    $isAccessory = $product->category?->group_type === \App\Enums\CategoryGroupType::Accessory;
    $moreRoute = $isAccessory ? route('accessories.index') : route('products.index');
@endphp

@if($product->relatedProducts->isNotEmpty())
    <div class="row row-2">
        <h2>Sản Phẩm Liên Quan</h2>
        <a href="{{ $moreRoute }}"><p>Xem thêm</p></a>
    </div>
    <div class="row">
        @foreach($product->relatedProducts as $related)
            <x-product-card :product="$related" variant="default" />
        @endforeach
    </div>
@endif