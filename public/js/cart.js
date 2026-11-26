document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function formatVND(amount) {
        return Number(amount || 0).toLocaleString('vi-VN') + '₫';
    }

    function updateBadge(count) {
        const badge = document.querySelector('.cart-badge');
        if (badge && count !== undefined) {
            badge.textContent = count;
        }
    }

    function checkEmptyState() {
        const rows = document.querySelectorAll('#cartTable tr[data-item-id]');
        if (rows.length === 0) {
            const content = document.getElementById('cartContent');
            const emptyBox = document.getElementById('emptyCartBox');
            if (content) content.style.display = 'none';
            if (emptyBox) emptyBox.style.display = 'block';
        }
    }

    window.updateCartItem = async function (input) {
        const row = input.closest('tr');
        const max = parseInt(input.max, 10) || 1;
        let qty = parseInt(input.value, 10);

        if (isNaN(qty) || qty < 1) qty = 1;
        if (qty > max) {
            alert('Số lượng vượt quá tồn kho hiện có (' + max + ').');
            qty = max;
        }
        input.value = qty;

        try {
            const res = await fetch(row.dataset.updateUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ quantity: qty }),
            });

            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Không thể cập nhật số lượng.');
                input.value = input.dataset.prevQty || 1;
                return;
            }

            input.dataset.prevQty = qty;
            row.querySelector('.subtotal').textContent = formatVND(data.subtotal);
            document.getElementById('cartTotal').textContent = formatVND(data.total);
            updateBadge(data.cart_count);
        } catch (err) {
            alert('Có lỗi kết nối, vui lòng thử lại.');
            input.value = input.dataset.prevQty || 1;
        }
    };

    window.removeCartItem = async function (link) {
        const row = link.closest('tr');

        try {
            const res = await fetch(row.dataset.removeUrl, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Không thể xóa sản phẩm khỏi giỏ.');
                return;
            }

            row.remove();
            document.getElementById('cartTotal').textContent = formatVND(data.total);
            updateBadge(data.cart_count);
            checkEmptyState();
        } catch (err) {
            alert('Có lỗi kết nối, vui lòng thử lại.');
        }
    };
});