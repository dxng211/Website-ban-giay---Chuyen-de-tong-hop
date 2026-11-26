@extends('layouts.admin')

@section('title', 'Cập nhật tồn kho - ' . $product->name)
@section('page-title', 'Cập Nhật Tồn Kho Sản Phẩm')

@section('content')
@php
    $variants = collect($product->variants ?? []);
    $stockAction = \Illuminate\Support\Facades\Route::has('admin.products.stock')
        ? route('admin.products.stock', $product->id)
        : url('/admin/products/' . $product->id . '/stock');
    $backUrl = \Illuminate\Support\Facades\Route::has('admin.products.index')
        ? route('admin.products.index')
        : url('/admin/products');
    $totalStock = (int) $variants->sum('stock_quantity');
@endphp

<div class="card" style="max-width:680px;margin:0 auto 24px">
    <div class="bar">
        <div>
            <h3 style="margin:0">{{ $product->name }}</h3>
            <span class="hint">SKU: <b>{{ $product->sku }}</b> · Tổng tồn hiện tại: <b id="totalStockLabel">{{ $totalStock }}</b></span>
        </div>
        <span class="sp"></span>
        <a href="{{ $backUrl }}" class="btn sm gray"><i class="fa fa-arrow-left"></i> Quay lại</a>
    </div>

    @if ($errors->any())
        <div style="background:#fff1f0;border:1px solid #ffa39e;color:#cf1322;padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:13px">
            <ul style="margin:0;padding-left:18px">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $stockAction }}">
        @csrf
        @method('PATCH')

        <div class="wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kích cỡ (Size)</th>
                        <th>Mã biến thể (SKU)</th>
                        <th style="width:160px">Số lượng tồn kho</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($variants as $variant)
                        @php
                            $qty = (int) old('stocks.' . $variant->id, $variant->stock_quantity);
                        @endphp
                        <tr>
                            <td><b>Size {{ $variant->size }}</b></td>
                            <td><span class="hint">{{ $variant->variant_sku ?? $variant->sku ?? '—' }}</span></td>
                            <td>
                                <input
                                    type="number"
                                    name="stocks[{{ $variant->id }}]"
                                    value="{{ $qty }}"
                                    min="0"
                                    class="stock-qty-input"
                                    style="width:100%;padding:7px 10px;border:1px solid #ddd;border-radius:6px"
                                    oninput="recalcTotalStock()"
                                    required
                                >
                            </td>
                            <td>
                                @if ($qty <= 0)
                                    <span class="badge locked">Hết hàng</span>
                                @elseif ($qty <= 5)
                                    <span class="badge pending">Sắp hết</span>
                                @else
                                    <span class="badge on-s">Còn hàng</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:#999">
                                Sản phẩm này chưa có biến thể size nào. Hãy vào trang <a href="{{ route('admin.products.edit', $product->id) }}">Sửa sản phẩm</a> để thêm size.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($variants->isNotEmpty())
            <div style="display:flex;gap:10px;align-items:center;margin-top:18px">
                <button type="submit" class="btn"><i class="fa fa-check"></i> Lưu tồn kho</button>
                <a href="{{ $backUrl }}" class="btn gray">Quay lại</a>
            </div>
        @endif
    </form>
</div>

<script>
    function recalcTotalStock() {
        let sum = 0;
        document.querySelectorAll('.stock-qty-input').forEach(inp => {
            sum += Math.max(0, parseInt(inp.value || '0', 10));
        });
        const label = document.getElementById('totalStockLabel');
        if (label) label.textContent = sum;
    }
</script>
@endsection