@php
    $price = (float) ($price ?? 0);
    $salePrice = isset($salePrice) && $salePrice !== null ? (float) $salePrice : null;
    $hasDiscount = $salePrice !== null && $salePrice > 0 && $salePrice < $price;
    $discountPercent = $hasDiscount ? round((($price - $salePrice) / $price) * 100) : 0;
@endphp
<p class="price-box">
    @if($hasDiscount)
        <span class="sale-price">{{ number_format($salePrice, 0, ',', '.') }}₫</span>
        <span class="original-price">{{ number_format($price, 0, ',', '.') }}₫</span>
        <span class="discount-tag">-{{ $discountPercent }}%</span>
    @else
        <span class="sale-price">{{ number_format($price, 0, ',', '.') }}₫</span>
    @endif
</p>