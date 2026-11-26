<div class="review-section"
     id="reviewSection"
     data-list-url="{{ route('products.reviews', $product->id) }}"
     data-store-url="{{ route('reviews.store') }}"
     data-login-url="{{ route('account') }}"
     data-product-id="{{ $product->id }}">
    <h2>Đánh Giá Sản Phẩm</h2>

    <div class="filter-bar" style="margin-top: 15px;">
        <select id="reviewRatingFilter" onchange="filterReviews(this.value)">
            <option value="0">Tất cả đánh giá ({{ $ratingCounts[0] ?? 0 }})</option>
            <option value="5">5 sao ({{ $ratingCounts[5] ?? 0 }})</option>
            <option value="4">4 sao ({{ $ratingCounts[4] ?? 0 }})</option>
            <option value="3">3 sao ({{ $ratingCounts[3] ?? 0 }})</option>
            <option value="2">2 sao ({{ $ratingCounts[2] ?? 0 }})</option>
            <option value="1">1 sao ({{ $ratingCounts[1] ?? 0 }})</option>
        </select>
    </div>

    @include('products.partials._review-form')

    <div class="review-list" id="reviewList">
        @include('products.partials._review-list', ['reviews' => $initialReviews])
    </div>

    <button type="button"
            class="btn"
            @style(['background: #333', 'display: none' => ! $initialReviews->hasMorePages()])
            id="loadMoreReviewsBtn"
            data-next-page="2"
            onclick="showMoreReviews()">
        Hiển thị thêm đánh giá
    </button>
</div>