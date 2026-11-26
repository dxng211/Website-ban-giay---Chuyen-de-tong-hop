@extends('layouts.admin')

@section('title', 'Thêm sản phẩm mới - Admin')
@section('page-title', 'Thêm Sản Phẩm Mới')

@section('content')
@php
    $categoryList = $categories ?? collect();
    $storeUrl = \Illuminate\Support\Facades\Route::has('admin.products.store')
        ? route('admin.products.store')
        : url('/admin/products');
    $backUrl = \Illuminate\Support\Facades\Route::has('admin.products.index')
        ? route('admin.products.index')
        : url('/admin/products');
@endphp

<div class="card" style="max-width:860px;margin:0 auto 24px">
    <div class="bar">
        <h3 style="margin:0">Thông tin sản phẩm</h3>
        <span class="sp"></span>
        <a href="{{ $backUrl }}" class="btn sm gray"><i class="fa fa-arrow-left"></i> Quay lại</a>
    </div>

    <form method="POST" action="{{ $storeUrl }}" enctype="multipart/form-data">
        @csrf

        <div class="row2">
            <div class="fg">
                <label>Tên sản phẩm *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Downshifter Sports Shoes" required>
            </div>
            <div class="fg">
                <label>Mã SKU *</label>
                <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Ví dụ: DS-2026-RED" required>
            </div>
        </div>

        <div class="row2">
            <div class="fg">
                <label>Danh mục *</label>
                @if ($categoryList->isNotEmpty())
                    <select name="category_id" id="cat" onchange="gd()" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categoryList as $cat)
                            @php
                                $catGroup = $cat->type ?? $cat->group_type ?? $cat->group ?? '';
                                $catGroupStr = $catGroup instanceof \BackedEnum ? $catGroup->value : (string) $catGroup;
                                $isShoe = strtolower($catGroupStr) === 'shoe' || str_contains(mb_strtolower($cat->name ?? ''), 'giày');
                            @endphp
                            <option value="{{ $cat->id }}" data-shoe="{{ $isShoe ? '1' : '0' }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                {{ $cat->name ?? $cat->title }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="number" name="category_id" value="{{ old('category_id') }}" placeholder="Nhập ID danh mục" required>
                @endif
            </div>

            <div class="fg" id="gdw">
                <label>Giới tính (chỉ giày)</label>
                <select name="gender">
                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="unisex" {{ old('gender', 'unisex') === 'unisex' ? 'selected' : '' }}>Unisex</option>
                </select>
            </div>
        </div>

        <div class="row2">
            <div class="fg">
                <label>Giá gốc (₫) *</label>
                <input type="number" name="price" min="0" value="{{ old('price') }}" placeholder="1200000" required>
            </div>
            <div class="fg">
                <label>Giá sau giảm (để trống = không giảm)</label>
                <input type="number" name="sale_price" min="0" value="{{ old('sale_price') }}" placeholder="990000">
            </div>
        </div>

        <div class="fg">
            <label>Mô tả</label>
            <textarea name="description" rows="4" placeholder="Nhập mô tả chi tiết về chất liệu, kiểu dáng, công nghệ đế...">{{ old('description') }}</textarea>
        </div>

        <div class="fg">
            <label>Hình ảnh sản phẩm (tối đa 5 ảnh)</label>
            <div class="ups" id="ups">
                @for ($i = 0; $i < 5; $i++)
                    <label class="up {{ $i === 0 ? 'big' : '' }}">
                        @if ($i === 0)
                            <i class="fa fa-cloud-upload"></i>Ảnh chính (Lớn nhất)
                        @else
                            +
                        @endif
                        <input type="file" name="{{ $i === 0 ? 'image' : 'images[]' }}" accept="image/*" onchange="pv(this)">
                        <span class="rm" onclick="rmI(event, this)">&times;</span>
                    </label>
                @endfor
            </div>
            <p class="hint" style="margin-top:6px">Ảnh chính (lớn nhất) có sort_order = 0; các ảnh nhỏ là ảnh phụ.</p>
        </div>

        <div class="fg">
            <div style="margin-bottom:6px;font-weight:500">
                Biến thể Size &amp; tồn kho
                <label class="sw">
                    <input type="checkbox" name="has_sizes" value="1" id="hs" checked onchange="tg()">
                    <span></span>
                </label>
                <span class="hint" id="hsl">Sản phẩm có size</span>
            </div>

            <div id="sz">
                <div id="vs">
                    <div class="vr">
                        <input type="text" name="variants[0][size]" placeholder="Size (39, M, 120cm…)" value="40">
                        <input type="text" name="variants[0][sku]" placeholder="Mã biến thể" value="">
                        <input type="number" name="variants[0][stock_quantity]" min="0" value="10" placeholder="Tồn kho">
                        <button type="button" class="btn sm gray" onclick="this.parentNode.remove()">×</button>
                    </div>
                </div>
                <button type="button" class="btn sm dark" onclick="addV()">+ Thêm size</button>
            </div>

            <div id="nsz" style="display:none">
                <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', 0) }}" id="nq" placeholder="Tồn kho">
                <p class="hint" style="margin-top:4px">Không có size: hệ thống lưu 1 biến thể size "Mặc định" với số lượng tồn kho ở trên.</p>
            </div>
        </div>

        <div class="fg">
            <label style="font-weight:normal;cursor:pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="width:auto;margin-right:6px">
                Đang hiển thị (is_active)
            </label>
        </div>

        <div style="display:flex;gap:10px;align-items:center;margin-top:18px">
            <button type="submit" class="btn"><i class="fa fa-check"></i> Lưu sản phẩm</button>
            <a href="{{ $backUrl }}" class="btn gray">Quay lại</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let vIdx = 1;

    function tg() {
        const o = $('hs').checked;
        $('sz').style.display = o ? 'block' : 'none';
        $('nsz').style.display = o ? 'none' : 'block';
        $('hsl').textContent = o ? 'Sản phẩm có size' : 'Không có size';
    }

    function pv(i) {
        const f = i.files[0];
        if (!f) return;
        const r = new FileReader(), l = i.parentNode;
        r.onload = e => {
            const o = l.querySelector('img');
            if (o) o.remove();
            l.insertAdjacentHTML('afterbegin', `<img src="${e.target.result}" alt="Preview">`);
            l.classList.add('has');
        };
        r.readAsDataURL(f);
    }

    function rmI(e, x) {
        e.preventDefault();
        e.stopPropagation();
        const l = x.parentNode;
        const img = l.querySelector('img');
        if (img) img.remove();
        l.querySelector('input').value = '';
        l.classList.remove('has');
    }

    function gd() {
        const sel = $('cat');
        if (!sel || sel.tagName !== 'SELECT') return;
        const opt = sel.options[sel.selectedIndex];
        const isShoe = opt ? opt.getAttribute('data-shoe') !== '0' : true;
        $('gdw').style.display = isShoe ? 'block' : 'none';
    }

    function addV(s = '', k = '', q = 1) {
        const idx = vIdx++;
        $('vs').insertAdjacentHTML('beforeend',
            `<div class="vr">` +
                `<input type="text" name="variants[${idx}][size]" placeholder="Size (39, M, 120cm…)" value="${s}">` +
                `<input type="text" name="variants[${idx}][sku]" placeholder="Mã biến thể" value="${k}">` +
                `<input type="number" name="variants[${idx}][stock_quantity]" min="0" value="${q}" placeholder="Tồn kho">` +
                `<button type="button" class="btn sm gray" onclick="this.parentNode.remove()">×</button>` +
            `</div>`
        );
    }

    gd();
</script>
@endpush
@endsection