document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // 1. Gallery: bấm ảnh nhỏ hoán đổi với ảnh lớn
    const productImg = document.getElementById('productImg');
    document.querySelectorAll('.small-img').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            const tmp = productImg.src;
            productImg.src = thumb.src;
            thumb.src = tmp;
        });
    });

    // 2. Chọn size & validate số lượng tồn kho
    let selectedVariantId = document.getElementById('defaultVariantId')?.value || null;
    const qtyInput = document.getElementById('qtyInput');

    window.selectSize = function (btn) {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedVariantId = btn.dataset.variantId;
        const stock = parseInt(btn.dataset.stock, 10) || 1;
        document.getElementById('stockDisplay').innerText = 'Số lượng tồn kho của size này: ' + stock;
        qtyInput.max = stock;
        clampQty();
    };

    function clampQty() {
        if (!qtyInput) return;
        const max = parseInt(qtyInput.max, 10) || 1;
        let v = parseInt(qtyInput.value, 10);
        if (isNaN(v) || v < 1) v = 1;
        if (v > max) v = max;
        qtyInput.value = v;
    }

    if (qtyInput) {
        qtyInput.addEventListener('input', clampQty);
        qtyInput.addEventListener('keydown', function (e) {
            if (['-', '+', 'e', 'E', '.', ','].includes(e.key)) e.preventDefault();
        });
    }

    // 3. Thêm vào giỏ hàng qua AJAX
    window.addToCart = async function () {
        if (!selectedVariantId) {
            alert('Vui lòng chọn size trước khi thêm vào giỏ hàng.');
            return;
        }
        const btn = document.getElementById('addToCartBtn');
        try {
            const res = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    product_variant_id: Number(selectedVariantId),
                    quantity: Number(qtyInput.value || 1),
                }),
            });
            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Không thể thêm vào giỏ hàng.');
                return;
            }
            const badge = document.querySelector('.cart-badge');
            if (badge && data.cart_count !== undefined) badge.textContent = data.cart_count;
            alert(data.message || 'Đã thêm sản phẩm vào giỏ hàng!');
        } catch (err) {
            alert('Có lỗi kết nối, vui lòng thử lại.');
        }
    };

    // 4. Đánh giá: lọc sao, tải thêm 5 đánh giá, gửi đánh giá
    const reviewSection = document.getElementById('reviewSection');
    let currentStar = 0;

    async function fetchReviews(page, append) {
        const baseUrl = reviewSection.dataset.listUrl;
        const url = `${baseUrl}?rating=${currentStar}&page=${page}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const data = await res.json();

        const listEl = document.getElementById('reviewList');
        if (append) {
            listEl.insertAdjacentHTML('beforeend', data.html);
        } else {
            listEl.innerHTML = data.html || '<p style="color:#777; padding:10px 0;">Không có đánh giá phù hợp.</p>';
        }

        const moreBtn = document.getElementById('loadMoreReviewsBtn');
        moreBtn.style.display = data.has_more ? 'inline-block' : 'none';
        moreBtn.dataset.nextPage = data.next_page;
    }

    window.filterReviews = function (rating) {
        currentStar = Number(rating) || 0;
        fetchReviews(1, false);
    };

    window.showMoreReviews = function () {
        const moreBtn = document.getElementById('loadMoreReviewsBtn');
        const nextPage = Number(moreBtn.dataset.nextPage) || 2;
        fetchReviews(nextPage, true);
    };

    window.submitReview = async function () {
        const rating = Number(document.getElementById('reviewRatingInput').value);
        const comment = document.getElementById('reviewCommentInput').value.trim();
        const feedbackEl = document.getElementById('reviewFeedbackMsg');

        const res = await fetch(reviewSection.dataset.storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                product_id: Number(reviewSection.dataset.productId),
                rating: rating || null,
                comment: comment || null,
            }),
        });

        if (res.status === 401) {
            const loginUrl = new URL(reviewSection.dataset.loginUrl, window.location.origin);
            loginUrl.searchParams.set('redirect', 'review');
            window.location.href = loginUrl.toString();
            return;
        }

        const data = await res.json();
        if (!res.ok) {
            feedbackEl.style.color = '#d9534f';
            feedbackEl.textContent = data.message || 'Vui lòng chọn số sao và nhập nội dung nhận xét.';
            return;
        }

        feedbackEl.style.color = '#28a745';
        feedbackEl.textContent = data.message;
        document.getElementById('reviewRatingInput').value = '0';
        document.getElementById('reviewCommentInput').value = '';
    };
});