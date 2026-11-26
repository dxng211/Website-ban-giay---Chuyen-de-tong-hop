@php
    $isEdit = isset($group) && !empty($group);
    $chartKey = $group->chart_key ?? ($key ?? '');
    $rowsList = old('rows');

    if (is_null($rowsList)) {
        if (!empty($items) && count($items) > 0) {
            $rowsList = collect($items)->map(fn($item) => (array) $item)->all();
        } else {
            $rowsList = [['size_label' => '', 'foot_length_cm' => '']];
        }
    }
@endphp

@if (!$isEdit)
    <div>
        <label for="chart_key">Chart Key (vd: shoe_vn)</label>
        <input id="chart_key" type="text" name="chart_key" value="{{ old('chart_key') }}" required>
    </div>
@else
    <div>
        <label>Chart Key</label>
        <input type="text" value="{{ $chartKey }}" disabled>
    </div>
@endif

<div>
    <label for="name">Tên bảng size</label>
    <input id="name" type="text" name="name" value="{{ old('name', $group->name ?? '') }}" required>
</div>

<div>
    <label>Các dòng size</label>
    <div id="size-chart-rows">
        @foreach ($rowsList as $i => $row)
            @include('admin.size-charts.partials._chart-row', ['index' => $i, 'row' => $row])
        @endforeach
    </div>
    <button type="button" id="btn-add-size-row">+ Thêm dòng size</button>
</div>

<template id="size-chart-row-template">
    @include('admin.size-charts.partials._chart-row', ['index' => '__INDEX__', 'row' => []])
</template>

<script>
    (function () {
        const container = document.getElementById('size-chart-rows');
        const template = document.getElementById('size-chart-row-template');
        const addBtn = document.getElementById('btn-add-size-row');
        if (!container || !template || !addBtn) return;

        let nextIndex = container.querySelectorAll('.size-chart-row').length;
        addBtn.addEventListener('click', function () {
            container.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(nextIndex++)));
        });
    })();
</script>