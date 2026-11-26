@php
    $idx = $index ?? '__INDEX__';
    $rowSize = is_array($row ?? null) ? ($row['size_label'] ?? $row['label'] ?? '') : ($row->size_label ?? $row->label ?? '');
    $rowLength = is_array($row ?? null) ? ($row['foot_length_cm'] ?? $row['note'] ?? '') : ($row->foot_length_cm ?? $row->note ?? '');
@endphp

<div class="size-chart-row">
    <input type="text" name="rows[{{ $idx }}][size_label]" value="{{ old("rows.$idx.size_label", $rowSize) }}" placeholder="Size (vd: 39)">
    <input type="text" name="rows[{{ $idx }}][foot_length_cm]" value="{{ old("rows.$idx.foot_length_cm", $rowLength) }}" placeholder="Chiều dài / ghi chú (vd: 24.5 cm)">
    <button type="button" onclick="this.closest('.size-chart-row').remove()">Xóa dòng</button>
</div>