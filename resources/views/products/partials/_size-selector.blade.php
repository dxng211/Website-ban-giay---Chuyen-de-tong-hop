@php
    $variants = $product->variants;
    $isDefaultOnly = $variants->count() === 1 && mb_strtolower(trim($variants->first()->size)) === 'mặc định';
    $defaultVariant = $isDefaultOnly ? $variants->first() : null;
@endphp

@if($isDefaultOnly && $defaultVariant)
    <input type="hidden" id="defaultVariantId" value="{{ $defaultVariant->id }}" data-stock="{{ $defaultVariant->stock_quantity }}">
    <p class="stock-info" id="stockDisplay">
        {{ $defaultVariant->stock_quantity > 0 ? "Còn {$defaultVariant->stock_quantity} sản phẩm trong kho." : 'Sản phẩm tạm hết hàng.' }}
    </p>
@else
    <label style="display:block; font-weight:bold; margin-top:5px;">Kích cỡ (Size):</label>
    <div class="size-selector">
        @foreach($variants as $variant)
            @if($variant->stock_quantity > 0)
                <button type="button"
                        class="size-btn"
                        data-variant-id="{{ $variant->id }}"
                        data-stock="{{ $variant->stock_quantity }}"
                        onclick="selectSize(this)">
                    {{ $variant->size }}
                </button>
            @else
                <button type="button" class="size-btn disabled" disabled title="Hết hàng">
                    {{ $variant->size }}
                </button>
            @endif
        @endforeach
    </div>
    <p class="stock-info" id="stockDisplay">Vui lòng chọn size để xem số lượng còn trong kho.</p>
@endif

<div style="display:flex; gap:10px; align-items:center;">
    <input type="number"
           id="qtyInput"
           value="1"
           min="1"
           max="{{ $defaultVariant ? max(1, $defaultVariant->stock_quantity) : 1 }}"
           style="width: 60px; height: 40px; padding: 5px;"
           @disabled($defaultVariant && $defaultVariant->stock_quantity <= 0)>
    <button type="button"
            class="btn"
            id="addToCartBtn"
            data-url="{{ route('cart.add') }}"
            onclick="addToCart()"
            @disabled($defaultVariant && $defaultVariant->stock_quantity <= 0)>
        Thêm Vào Giỏ Hàng
    </button>
</div>