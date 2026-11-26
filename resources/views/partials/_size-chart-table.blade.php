<h3>{{ $group->chart_name }}</h3>
<table style="width: 100%; margin-top: 15px; border-collapse: collapse; font-size: 13px;" border="1" cellpadding="8">
    <thead>
        <tr style="background: #f1f1f1;">
            <th>{{ $group->size_column_label }}</th>
            <th>{{ $group->reference_column_label }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($group->sizeCharts as $row)
            <tr>
                <td>{{ $row->size_label }}</td>
                <td>
                    @if($row->reference_value)
                        {{ $row->reference_value }}
                    @elseif($row->reference_min == $row->reference_max)
                        {{ rtrim(rtrim(number_format($row->reference_min, 2, '.', ''), '0'), '.') }}{{ $group->unit ? ' ' . $group->unit : '' }}
                    @else
                        {{ rtrim(rtrim(number_format($row->reference_min, 2, '.', ''), '0'), '.') }} - {{ rtrim(rtrim(number_format($row->reference_max, 2, '.', ''), '0'), '.') }}{{ $group->unit ? ' ' . $group->unit : '' }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>