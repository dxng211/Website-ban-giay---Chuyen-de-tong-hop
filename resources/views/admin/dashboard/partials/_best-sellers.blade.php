<div class="dashboard-best-sellers">
    <h3>Sản phẩm bán chạy</h3>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Sản phẩm</th>
                <th>Đã bán</th>
            </tr>
        </thead>
        <tbody>
            @forelse (($bestSellers ?? []) as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ is_array($item) ? ($item['name'] ?? '') : ($item->name ?? '') }}</td>
                    <td>{{ is_array($item) ? ($item['sold'] ?? $item['quantity'] ?? 0) : ($item->sold ?? $item->quantity ?? 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Chưa có dữ liệu sản phẩm bán chạy.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>