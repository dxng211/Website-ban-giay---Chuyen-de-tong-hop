@extends('layouts.admin')

@section('title', 'Đánh giá - Admin')
@section('page-title', 'Quản Lý Đánh Giá')

@section('content')
@php
    $reviewItems = $reviews ?? collect();
    $currentStatus = (string) request('status', '');
    $currentRating = (string) request('rating', '');
@endphp

<div class="card">
    <form method="GET" action="{{ url()->current() }}" class="bar">
        <select name="status" onchange="this.form.submit()">
            <option value="" {{ $currentStatus === '' ? 'selected' : '' }}>Tất cả trạng thái</option>
            <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="approved" {{ $currentStatus === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
            <option value="rejected" {{ $currentStatus === 'rejected' ? 'selected' : '' }}>Đã xóa/từ chối</option>
        </select>
        <select name="rating" onchange="this.form.submit()">
            <option value="" {{ $currentRating === '' ? 'selected' : '' }}>Tất cả số sao</option>
            <option value="5" {{ $currentRating === '5' ? 'selected' : '' }}>5 sao</option>
            <option value="4" {{ $currentRating === '4' ? 'selected' : '' }}>4 sao</option>
            <option value="3" {{ $currentRating === '3' ? 'selected' : '' }}>3 sao</option>
            <option value="1-2" {{ $currentRating === '1-2' ? 'selected' : '' }}>1 - 2 sao</option>
        </select>
    </form>

    <div class="wrap">
        <table>
            <tr>
                <th>Sản phẩm</th>
                <th>Khách hàng</th>
                <th>Đánh giá</th>
                <th>Nội dung</th>
                <th>Đơn hàng</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
            @forelse ($reviewItems as $review)
                @php
                    $productName = optional($review->product)->name ?? optional($review->product)->title ?? ('Sản phẩm #' . ($review->product_id ?? ''));
                    $customerName = $review->customer_name ?? optional($review->user)->full_name ?? optional($review->user)->name ?? 'Khách hàng';
                    $rating = max(0, min(5, (int) ($review->rating ?? $review->stars ?? 5)));
                    $starsStr = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                    $commentText = $review->comment ?? $review->content ?? $review->body ?? '';
                    $orderCode = optional($review->order)->order_code ?? optional($review->order)->code ?? ($review->order_id ? ('#' . $review->order_id) : '—');

                    $rawStatus = $review->status instanceof \BackedEnum
                        ? (string) $review->status->value
                        : (string) ($review->status ?? 'pending');
                    $stLower = strtolower(trim($rawStatus));
                @endphp
                <tr data-st="{{ $stLower }}">
                    <td>{{ $productName }}</td>
                    <td>{{ $customerName }}</td>
                    <td class="stars">{{ $starsStr }}</td>
                    <td>{{ $commentText }}</td>
                    <td>{{ $orderCode }}</td>
                    <td>
                        @if ($stLower === 'approved')
                            <span class="badge approved">Đã duyệt</span>
                        @elseif ($stLower === 'rejected')
                            <span class="badge rejected">Đã xóa</span>
                        @else
                            <span class="badge pending">Chờ duyệt</span>
                        @endif
                    </td>
                    <td class="ac">
                        @if ($stLower === 'rejected')
                            —
                        @else
                            @if ($stLower !== 'approved' && \Illuminate\Support\Facades\Route::has('admin.reviews.approve'))
                                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn sm">Duyệt</button>
                                </form>
                            @endif

                            @if (\Illuminate\Support\Facades\Route::has('admin.reviews.reject'))
                                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" class="inline-form" onsubmit="return confirm('Xóa/từ chối đánh giá này?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn sm gray">Xóa</button>
                                </form>
                            @elseif (\Illuminate\Support\Facades\Route::has('admin.reviews.destroy'))
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline-form" onsubmit="return confirm('Xóa đánh giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn sm gray">Xóa</button>
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:#999">Chưa có đánh giá nào.</td></tr>
            @endforelse
        </table>
    </div>

    @if (method_exists($reviewItems, 'links'))
        <div class="pagination-box">{{ $reviewItems->withQueryString()->links() }}</div>
    @endif

    <p class="hint" style="margin-top:10px">Chỉ review "Đã duyệt" hiển thị công khai và tính vào rating_avg / review_count. Xóa = status "rejected".</p>
</div>
@endsection