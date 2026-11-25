{{-- Partial render HTML bảng size (dùng cho route AJAX categories.size-chart lẫn xem trước ở Admin) --}}
<h3>{{ $sizeChartGroup->name ?? 'Bảng hướng dẫn chọn size' }}</h3>
@if(!empty($sizeChartGroup->description))
    <p style="margin-top: 10px; font-size: 13px;">{{ $sizeChartGroup->description }}</p>
@else
    <p style="margin-top: 10px; font-size: 13px;">Đo chiều dài bàn chân từ gót đến ngón dài nhất:</p>
@endif

<table style="width: 100%; margin-top: 15px; border-collapse: collapse; font-size: 13px;" border="1" cellpadding="8">
    <thead>
        <tr style="background: #f1f1f1;">
            <th>{{ $sizeChartGroup->size_column_label ?? 'Size' }}</th>
            <th>{{ $sizeChartGroup->reference_column_label ?? 'Chiều dài chân (cm)' }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sizeChartGroup->charts as $row)
            <tr>
                <td>{{ $row->size_label }}</td>
                <td>
                    {{ number_format((float) $row->reference_min, 1) }}
                    -
                    {{ number_format((float) $row->reference_max, 1) }}
                    {{ $sizeChartGroup->unit ?? 'cm' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="text-align: center; color: #777;">Chưa có dữ liệu bảng kích cỡ.</td>
            </tr>
        @endforelse
    </tbody>
</table>