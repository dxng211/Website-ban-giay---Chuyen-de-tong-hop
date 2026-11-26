@php
    $totalStock = $product->variants->sum('stock_quantity');
    $unitLabel = $product->category?->group_type === \App\Enums\CategoryGroupType::Shoe ? 'đôi' : 'sản phẩm';
    $hasDiscount = $product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->base_price;
    $discountPercent = $hasDiscount ? round((1 - $product->sale_price / $product->base_price) * 100) : 0;
@endphp

<div class="col-2">
    <p>{{ $product->category?->name ?? 'Sản phẩm' }}</p>
    <h1>{{ $product->name }}</h1>
    <p style="font-size: 12px; color: #777;">
        Mã sản phẩm: <strong>{{ $product->sku }}</strong> |
        Tổng tồn kho: <strong>{{ $totalStock }} {{ $unitLabel }}</strong>
    </p>

    <div class="rating" style="margin: 5px 0;">
        <x-rating-stars :rating="$product->rating_avg" />
        ({{ number_format((float) $product->rating_avg, 1) }}/5 - {{ $product->review_count }} đánh giá)
    </div>

    <div class="price-box">
        @if($hasDiscount)
            <span class="sale-price">{{ number_format($product->sale_price, 0, ',', '.') }}₫</span>
            <span class="original-price">{{ number_format($product->base_price, 0, ',', '.') }}₫</span>
            <span class="discount-tag">-{{ $discountPercent }}%</span>
        @else
            <span class="sale-price">{{ number_format($product->base_price, 0, ',', '.') }}₫</span>
        @endif
    </div>

    @if($product->category?->size_chart_key)
        <span class="size-guide-btn"
              data-url="{{ route('categories.size-chart', $product->category->size_chart_key) }}"
              onclick="openSizeGuide(this.dataset.url)">
            <i class="fa fa-table"></i> Hướng dẫn chọn kích cỡ
        </span>
    @endif

    @include('products.partials._size-selector')

    <h3>Mô Tả Sản Phẩm <i class="fa fa-indent"></i></h3>
    <br>
    <p>{!! nl2br(e($product->description)) !!}</p>
</div>