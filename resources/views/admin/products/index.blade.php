@extends('layouts.admin')

@section('title', 'Sản phẩm - Admin')
@section('page-title', 'Quản Lý Sản Phẩm & Tồn Kho')

@push('styles')
<style>
    .pagination-box {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 14px;
    }

    .pagination-box nav[role="navigation"] {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pagination-box nav[role="navigation"] > div {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pagination-box svg,
    .pagination svg,
    nav[role="navigation"] svg {
        width: 16px !important;
        height: 16px !important;
        max-width: 16px !important;
        max-height: 16px !important;
        display: inline-block;
        vertical-align: middle;
        flex-shrink: 0;
    }

    .pagination-box a,
    .pagination-box span {
        font-size: 13px;
        line-height: 1.4;
        text-decoration: none;
    }

    .bar select[name="category_id"] {
        min-width: 190px;
    }

    .btn-reset-filter {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        color: #666;
        text-decoration: none;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #ddd;
        background: #fff;
    }

    .btn-reset-filter:hover {
        border-color: #ff523b;
        color: #ff523b;
    }
</style>
@endpush

@section('content')
@php
    $items = $products ?? collect();
    $categoryList = (!empty($categories) && collect($categories)->isNotEmpty())
        ? collect($categories)
        : (class_exists(\App\Models\Category::class) ? \App\Models\Category::query()->orderBy('name')->get() : collect());

    $groupedCategories = $categoryList->groupBy(function ($cat) {
        $grp = $cat->group_type ?? $cat->type ?? $cat->group ?? '';
        $val = $grp instanceof \BackedEnum ? (string) $grp->value : (string) $grp;
        return match (strtolower(trim($val))) {
            'shoe', 'shoes', 'giay', 'giày' => 'Giày',
            'accessory', 'accessories', 'phu_kien', 'phụ kiện' => 'Phụ kiện (Dây giày, Tất...)',
            default => $val !== '' ? ucfirst($val) : 'Danh mục khác',
        };
    });

    $hasFilter = request()->filled('q')
        || request()->filled('search')
        || request()->filled('category_id')
        || request()->filled('status');
@endphp

<div class="card">
    <form method="GET" action="{{ url()->current() }}" class="bar">
        <input type="text" name="q" value="{{ request('q', request('search')) }}" placeholder="Tìm theo tên / mã SKU...">

        <select name="category_id" id="filterCategorySelect">
            <option value="">Tất cả danh mục ({{ $categoryList->count() }})</option>
            @if ($groupedCategories->count() > 1)
                @foreach ($groupedCategories as $groupLabel => $groupItems)
                    <optgroup label="{{ $groupLabel }}">
                        @foreach ($groupItems as $cat)
                            <option value="{{ $cat->id }}" {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                {{ $cat->name ?? $cat->title }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            @else
                @foreach ($categoryList as $cat)
                    <option value="{{ $cat->id }}" {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>
                        {{ $cat->name ?? $cat->title }}
                    </option>
                @endforeach
            @endif
        </select>

        <select name="status">
            <option value="" {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>Đang hiển thị</option>
            <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Đã ẩn (soft delete)</option>
        </select>

        <button type="submit" class="btn sm dark">
            <i class="fa fa-filter"></i> Lọc sản phẩm
        </button>

        @if ($hasFilter)
            <a href="{{ url()->current() }}" class="btn-reset-filter">
                <i class="fa fa-times"></i> Bỏ lọc
            </a>
        @endif

        <span class="sp"></span>

        @if (\Illuminate\Support\Facades\Route::has('admin.products.create'))
            <a href="{{ route('admin.products.create') }}" class="btn"><i class="fa fa-plus"></i> Thêm sản phẩm</a>
        @else
            <button type="button" class="btn" onclick="openForm()"><i class="fa fa-plus"></i> Thêm sản phẩm</button>
        @endif
    </form>

    <div class="wrap">
        <table id="tb">
            <tr>
                <th>Sản phẩm</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th class="r">Tổng tồn</th>
                <th class="r">Đã bán</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
            @forelse ($items as $product)
                @php
                    $firstImg = is_iterable($product->images ?? null) ? collect($product->images)->first() : null;
                    $thumb = $product->thumbnail_url
                        ?? $product->image_url
                        ?? optional($firstImg)->url
                        ?? (optional($firstImg)->path ? asset('storage/' . ltrim($firstImg->path, '/')) : asset('images/product-11.jpg'));

                    $sku = $product->sku ?? $product->code ?? ('SP-' . $product->id);
                    $rawGender = $product->gender_label ?? $product->gender ?? '';
                    $genderText = $rawGender instanceof \BackedEnum
                        ? (string) $rawGender->value
                        : ($rawGender instanceof \UnitEnum ? $rawGender->name : (string) $rawGender);
                    $genderLabel = match (strtolower(trim($genderText))) {
                        'male', 'nam' => 'Nam',
                        'female', 'nu', 'nữ' => 'Nữ',
                        'unisex' => 'Unisex',
                        default => $genderText !== '' ? ucfirst($genderText) : '',
                    };
                    $subLine = trim($sku . ($genderLabel !== '' ? ' · ' . $genderLabel : ''));

                    $firstCat = is_iterable($product->categories ?? null) ? collect($product->categories)->first() : null;
                    $categoryName = optional($product->category)->name ?? optional($firstCat)->name ?? '—';

                    $price = (int) ($product->sale_price ?? $product->price ?? 0);
                    $comparePrice = (int) ($product->compare_at_price ?? $product->original_price ?? $product->list_price ?? 0);
                    if ($comparePrice <= $price && (int) ($product->price ?? 0) > $price) {
                        $comparePrice = (int) $product->price;
                    }

                    $variantsCol = is_iterable($product->variants ?? null) ? collect($product->variants) : collect();
                    $totalStock = isset($product->total_stock)
                        ? (int) $product->total_stock
                        : ($variantsCol->isNotEmpty()
                            ? (int) $variantsCol->sum(fn ($v) => (int) ($v->stock_quantity ?? $v->stock ?? 0))
                            : (int) ($product->stock_quantity ?? $product->stock ?? 0));

                    $soldCount = (int) ($product->sold_count ?? 0);
                    $isActive = isset($product->is_active) ? (bool) $product->is_active : (($product->status ?? 'active') !== 'hidden');
                @endphp
                <tr>
                    <td>
                        <img src="{{ $thumb }}" alt="{{ $product->name ?? '' }}">
                        <b>{{ $product->name ?? $product->title }}</b><br>
                        <span class="hint">{{ $subLine }}</span>
                    </td>
                    <td>{{ $categoryName }}</td>
                    <td>
                        <span class="price">{{ number_format($price, 0, ',', '.') }}₫</span>
                        @if ($comparePrice > $price)
                            <span class="old">{{ number_format($comparePrice, 0, ',', '.') }}₫</span>
                        @endif
                    </td>
                    <td class="r">{{ $totalStock }}</td>
                    <td class="r">{{ $soldCount }}</td>
                    <td>
                        @if (!$isActive)
                            <span class="badge cancelled">Đã ẩn</span>
                        @elseif ($totalStock <= 0)
                            <span class="badge locked">Hết hàng</span>
                        @else
                            <span class="badge on-s">Hiển thị</span>
                        @endif
                    </td>
                    <td>
                        @if (\Illuminate\Support\Facades\Route::has('admin.products.edit'))
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn sm">Sửa</a>
                        @else
                            <button type="button" class="btn sm" onclick="openForm(1)">Sửa</button>
                        @endif

                        @if (\Illuminate\Support\Facades\Route::has('admin.products.stock'))
                            <a href="{{ route('admin.products.stock', $product) }}" class="btn sm dark">Tồn kho</a>
                        @elseif (\Illuminate\Support\Facades\Route::has('admin.products.variants.index'))
                            <a href="{{ route('admin.products.variants.index', $product) }}" class="btn sm dark">Tồn kho</a>
                        @else
                            <button type="button" class="btn sm dark" onclick="openStock({{ \Illuminate\Support\Js::from($product->name ?? 'Sản phẩm') }})">Tồn kho</button>
                        @endif

                        @if (\Illuminate\Support\Facades\Route::has('admin.products.destroy'))
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline-form" onsubmit="return confirm('Bạn có chắc chắn muốn ẩn/xóa sản phẩm này?\n(Sản phẩm đã có đơn hàng chỉ bị ẩn, không xóa vĩnh viễn)');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn sm gray">Xóa</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:#999">Chưa có sản phẩm nào.</td></tr>
            @endforelse
        </table>
    </div>

    @if (method_exists($items, 'links'))
        <div class="pagination-box">{{ $items->withQueryString()->links() }}</div>
    @endif

    <p class="hint" style="margin-top:10px">Tổng tồn = SUM(product_variants.stock_quantity). Xóa = soft delete (deleted_at, is_active=false).</p>
</div>
@endsection
