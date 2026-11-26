@forelse($reviews as $review)
    <div class="review-item" data-rating="{{ $review->rating }}">
        <strong>{{ $review->user?->full_name ?? 'Khách hàng' }}</strong>
        <span style="font-size: 12px; color: #888;">- {{ $review->created_at?->format('d/m/Y') }}</span>
        <div class="rating">
            @for($i = 1; $i <= 5; $i++)
                <i class="fa {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o' }}"></i>
            @endfor
        </div>
        <p>{{ $review->comment }}</p>
    </div>
@empty
    @if(!($isAppend ?? false))
        <p id="noReviewText" style="color: #777; padding: 10px 0;">Chưa có đánh giá nào cho sản phẩm này.</p>
    @endif
@endforelse