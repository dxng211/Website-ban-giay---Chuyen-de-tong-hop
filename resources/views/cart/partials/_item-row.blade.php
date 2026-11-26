@php
    $variant = $item->variant;
    $product = $variant?->product;
    $image = $product?->images->first();
    $imageUrl = $image ? asset($image->image_url) : asset('images/product-11.jpg');
    $stock = (int) ($variant?->stock_quantity ?? 0);
    $unitPrice = (int) ($item->unit_price ?? 0);
    $subtotal = (int) ($item->subtotal ?? ($unitPrice * $item->quantity));
@endphp

<tr data-item-id="{{ $item->id }}"
    data-price="{{ $unitPrice }}"
    data-update-url="{{ route('cart.update', $item->id) }}"
    data-remove-url="{{ route('cart.remove', $item->id) }}">
    <td>
        <div class="cart-info">
            <a href="{{ $product ? route('products.show', $product->id) : '#' }}">
                <img src="{{ $imageUrl }}" alt="{{ $product?->name ?? 'Sản phẩm' }}">
            </a>
            <div>
                <p>
                    <a href="{{ $product ? route('products.show', $product->id) : '#' }}" style="color: inherit;">
                        <strong>{{ $product?->name ?? 'Sản phẩm không còn tồn tại' }}</strong>
                    </a>
                </p>
                @if($variant && mb_strtolower(trim($variant->size)) !== 'mặc định')
                    <small>Kích cỡ (Size): {{ $variant->size }}</small><br>
                @endif
                <small>Đơn giá: {{ number_format($unitPrice, 0, ',', '.') }}₫</small><br>
                <a href="javascript:void(0)" onclick="removeCartItem(this)">Xóa (Remove)</a>
            </div>
        </div>
    </td>
    <td>
        <input type="number"
               value="{{ $item->quantity }}"
               min="1"
               max="{{ max(1, $stock) }}"
               data-prev-qty="{{ $item->quantity }}"
               onchange="updateCartItem(this)">
    </td>
    <td class="subtotal">{{ number_format($subtotal, 0, ',', '.') }}₫</td>
</tr>