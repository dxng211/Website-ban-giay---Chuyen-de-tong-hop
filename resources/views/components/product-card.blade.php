@php
    $firstImage = $product->images->sortBy('sort_order')->first()?->image_url;
    $mainImage = $firstImage ?: 'images/product-11.jpg';
    $detailUrl = route('products.show', $product->id);

    $basePrice = (float) ($product->base_price ?? 0);
    $salePrice = ($product->sale_price !== null && (float) $product->sale_price > 0)
        ? (float) $product->sale_price
        : null;
@endphp

@if(($variant ?? 'default') === 'chatbot')
    <div class="chat-card">
        <img src="{{ asset(ltrim($mainImage, '/')) }}" alt="{{ $product->name }}">
        <div class="chat-card-info">
            <h5>{{ $product->name }}</h5>
            @php
                $displayPrice = ($salePrice !== null && $salePrice < $basePrice) ? $salePrice : $basePrice;
            @endphp
            <p>{{ number_format($displayPrice, 0, ',', '.') }}₫</p>
            <a href="{{ $detailUrl }}">Xem ngay</a>
        </div>
    </div>
@else
    <div class="col-4">
        <a href="{{ $detailUrl }}">
            <img src="{{ asset(ltrim($mainImage, '/')) }}" alt="{{ $product->name }}">
        </a>
        <a href="{{ $detailUrl }}">
            <h4>{{ $product->name }}</h4>
        </a>
        <x-rating-stars :rating="$product->rating_avg ?? 0" />
        <x-price-box :price="$basePrice" :sale-price="$salePrice" />
    </div>
@endif