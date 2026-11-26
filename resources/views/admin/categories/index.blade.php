@extends('layouts.admin')

@section('title', 'Danh mục & Size - Admin')
@section('page-title', 'Danh Mục & Bảng Size')

@section('content')
@php
    $categoryItems = $categories ?? collect();
    $chartGroups = $sizeCharts ?? $groups ?? collect();
    $currentGroup = $selectedGroup ?? $group ?? (is_object($chartGroups) ? $chartGroups->first() : (is_array($chartGroups) ? reset($chartGroups) : null));
    $selectedKey = request('chart_key', data_get($currentGroup, 'chart_key', data_get($currentGroup, 'key', 'shoe_vn')));
    $chartTitle = data_get($currentGroup, 'chart_name', data_get($currentGroup, 'title', data_get($currentGroup, 'name', 'Bảng hướng dẫn chọn size giày')));
    $col1Label = data_get($currentGroup, 'size_column_label', data_get($currentGroup, 'col1_label', data_get($currentGroup, 'column_1', 'Size VN/EU')));
    $col2Label = data_get($currentGroup, 'reference_column_label', data_get($currentGroup, 'col2_label', data_get($currentGroup, 'column_2', 'Chiều dài chân (cm)')));
    $sizeRows = data_get($currentGroup, 'rows', data_get($currentGroup, 'items', [
        ['size' => '39', 'range' => '24.0 - 24.5 cm', 'sort' => 1],
        ['size' => '40', 'range' => '24.5 - 25.0 cm', 'sort' => 2],
        ['size' => '41', 'range' => '25.0 - 25.5 cm', 'sort' => 3],
        ['size' => '42', 'range' => '25.5 - 26.0 cm', 'sort' => 4],
        ['size' => '43', 'range' => '26.0 - 26.5 cm', 'sort' => 5],
    ]));
@endphp

<div class="grid2" style="align-items:start">
    <!-- categories: /api/admin/categories -->
    <div class="card">
        <div class="bar">
            <h3 style="margin:0">Danh mục</h3>
            <span class="sp"></span>
            @if (\Illuminate\Support\Facades\Route::has('admin.categories.create'))
                <a href="{{ route('admin.categories.create') }}" class="btn sm">+ Thêm</a>
            @else
                <button type="button" class="btn sm" onclick="openCatModal()">+ Thêm</button>
            @endif
        </div>
        <div class="wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Nhóm</th>
                        <th>Slug</th>
                        <th>Nhóm bảng size</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="ct">
                    @forelse ($categoryItems as $cat)
                        @php
                            $groupVal = $cat->type ?? $cat->group_type ?? $cat->group ?? 'shoe';
                            $groupText = $groupVal instanceof \BackedEnum ? $groupVal->value : (string) $groupVal;
                            $sizeChartKey = $cat->size_chart_key ?? $cat->chart_key ?? $cat->size_group ?? '';
                        @endphp
                        <tr>
                            <td>{{ $cat->name ?? $cat->title }}</td>
                            <td>{{ $groupText }}</td>
                            <td>{{ $cat->slug ?? '' }}</td>
                            <td>{{ $sizeChartKey ?: '—' }}</td>
                            <td>
                                @if (\Illuminate\Support\Facades\Route::has('admin.categories.edit'))
                                    <a href="{{ route('admin.categories.edit', $cat) }}" class="btn sm">Sửa</a>
                                @else
                                    <button type="button" class="btn sm" onclick="openCatModal({{ \Illuminate\Support\Js::from(['name' => $cat->name, 'group' => $groupText, 'slug' => $cat->slug, 'key' => $sizeChartKey]) }})">Sửa</button>
                                @endif
                                @if (\Illuminate\Support\Facades\Route::has('admin.categories.destroy'))
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline-form" onsubmit="return confirm('Xóa danh mục này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn sm gray">Xóa</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:#999">Chưa có danh mục nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="hint" style="margin-top:10px">Nhóm bảng size = trống: danh mục không cần bảng size, nút "Hướng dẫn chọn size" bị ẩn ở website.</p>
    </div>

    <!-- size_chart_groups + size_charts -->
    <div class="card">
        <div class="bar">
            <h3 style="margin:0">Bảng quy đổi size</h3>
            <span class="sp"></span>
            @if (\Illuminate\Support\Facades\Route::has('admin.size-charts.create'))
                <a href="{{ route('admin.size-charts.create') }}" class="btn sm">+ Nhóm mới</a>
            @else
                <button type="button" class="btn sm" onclick="openM('gm')">+ Nhóm mới</button>
            @endif
        </div>

        <form id="sizeChartForm" method="POST" action="{{ \Illuminate\Support\Facades\Route::has('admin.size-charts.update') ? route('admin.size-charts.update', $selectedKey) : '#' }}">
            @csrf
            @if (\Illuminate\Support\Facades\Route::has('admin.size-charts.update'))
                @method('PUT')
            @endif

            <div class="fg">
                <label>Nhóm bảng size (chart_key)</label>
                <select id="gs" name="chart_key" onchange="window.location.href='{{ url()->current() }}?chart_key='+encodeURIComponent(this.value)">
                    @if (!empty($chartGroups) && count($chartGroups) > 0)
                        @foreach ($chartGroups as $grp)
                            @php $k = data_get($grp, 'chart_key', data_get($grp, 'key')); @endphp
                            <option value="{{ $k }}" {{ (string) $selectedKey === (string) $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    @else
                        <option value="shoe_vn" {{ $selectedKey === 'shoe_vn' ? 'selected' : '' }}>shoe_vn</option>
                        <option value="sock_vn" {{ $selectedKey === 'sock_vn' ? 'selected' : '' }}>sock_vn</option>
                    @endif
                </select>
            </div>
            <div class="fg">
                <label>Tên bảng (hiển thị trên pop-up)</label>
                <input id="gn" name="chart_name" value="{{ $chartTitle }}">
            </div>
            <div class="row2">
                <div class="fg"><label>Tên cột 1</label><input id="gl1" name="size_column_label" value="{{ $col1Label }}"></div>
                <div class="fg"><label>Tên cột 2 (ý nghĩa quy đổi)</label><input id="gl2" name="reference_column_label" value="{{ $col2Label }}"></div>
            </div>
            <div class="wrap">
                <table>
                    <thead>
                        <tr><th id="h1">{{ $col1Label }}</th><th id="h2">{{ $col2Label }}</th><th style="width:70px">Thứ tự</th><th></th></tr>
                    </thead>
                    <tbody id="sc">
                        @foreach ($sizeRows as $i => $row)
                            <tr>
                                <td><input name="rows[{{ $i }}][size_label]" value="{{ data_get($row, 'size_label', data_get($row, 'size', data_get($row, 'label', ''))) }}" oninput="prev()"></td>
                                <td><input name="rows[{{ $i }}][reference_value]" value="{{ data_get($row, 'reference_value', data_get($row, 'range', data_get($row, 'value', ''))) }}" oninput="prev()"></td>
                                <td><input type="number" name="rows[{{ $i }}][sort_order]" value="{{ data_get($row, 'sort_order', data_get($row, 'sort', $i + 1)) }}" oninput="prev()"></td>
                                <td><button type="button" class="btn sm gray" onclick="this.closest('tr').remove();prev()">×</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <br>
            <button type="button" class="btn sm dark" onclick="addR()">+ Thêm dòng</button>
            <button type="{{ \Illuminate\Support\Facades\Route::has('admin.size-charts.update') ? 'submit' : 'button' }}" class="btn sm" onclick="{{ \Illuminate\Support\Facades\Route::has('admin.size-charts.update') ? '' : "toast('Đã lưu nhóm bảng size')" }}">Lưu nhóm &amp; bảng</button>
            <p class="hint" style="margin-top:10px">Cột 2 là chuỗi tự do: "24.0 - 24.5 cm", "42 - 43"... Sửa bảng áp dụng cho mọi danh mục dùng chung nhóm này.</p>
        </form>
    </div>
</div>

<!-- Preview -->
<div class="card">
    <h3>Xem trước pop-up trên website</h3>
    <table id="pv"></table>
</div>

@push('scripts')
<script>
    const el = id => document.getElementById(id);

    function esc(s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function rowH(i) {
        return `<tr>
            <td><input name="rows[${i}][size_label]" value="" oninput="prev()"></td>
            <td><input name="rows[${i}][reference_value]" value="" oninput="prev()"></td>
            <td><input type="number" name="rows[${i}][sort_order]" value="${i + 1}" oninput="prev()"></td>
            <td><button type="button" class="btn sm gray" onclick="this.closest('tr').remove();prev()">×</button></td>
        </tr>`;
    }

    function hd() {
        if (el('h1') && el('gl1')) el('h1').textContent = el('gl1').value;
        if (el('h2') && el('gl2')) el('h2').textContent = el('gl2').value;
    }

    function prev() {
        const sc = el('sc');
        const pv = el('pv');
        if (!sc || !pv) return;

        const title = esc(el('gn')?.value || '');
        const c1 = esc(el('gl1')?.value || '');
        const c2 = esc(el('gl2')?.value || '');

        const rs = [...sc.rows]
            .map(r => {
                const inputs = r.querySelectorAll('input');
                return {
                    s: (inputs[0]?.value || '').trim(),
                    v: (inputs[1]?.value || '').trim(),
                };
            })
            .filter(item => item.s !== '' || item.v !== '');

        const bodyHtml = rs.length
            ? rs.map(item => `<tr><td>${esc(item.s)}</td><td>${esc(item.v)}</td></tr>`).join('')
            : `<tr><td colspan="2" style="text-align:center;color:#999">Chưa có dòng size nào.</td></tr>`;

        pv.innerHTML = `<caption style="text-align:left;font-weight:600;color:#333;padding-bottom:8px">${title}</caption><thead><tr><th>${c1}</th><th>${c2}</th></tr></thead><tbody>${bodyHtml}</tbody>`;
    }

    function addR() {
        const sc = el('sc');
        if (!sc) return;
        sc.insertAdjacentHTML('beforeend', rowH(sc.rows.length));
        prev();
    }

    document.addEventListener('input', e => {
        if (!e.target) return;
        if (['gn', 'gl1', 'gl2'].includes(e.target.id)) hd();
        if (e.target.closest('#sc') || ['gn', 'gl1', 'gl2'].includes(e.target.id)) prev();
    });

    hd();
    prev();
</script>
@endpush
@endsection