@php
    $vIndex = $index ?? '__VAR_INDEX__';
    $vSize = is_array($variant ?? null) ? ($variant['size'] ?? '') : ($variant->size ?? '');
    $vStock = is_array($variant ?? null) ? ($variant['stock'] ?? 0) : ($variant->stock ?? 0);
@endphp

<div class="variant-row">
    <input type="text" name="variants[{{ $vIndex }}][size]" value="{{ old("variants.$vIndex.size", $vSize) }}" placeholder="Size (vd: 40)">
    <input type="number" min="0" name="variants[{{ $vIndex }}][stock]" value="{{ old("variants.$vIndex.stock", $vStock) }}" placeholder="Tồn kho">
    <button type="button" onclick="this.closest('.variant-row').remove()">Xóa</button>
</div>