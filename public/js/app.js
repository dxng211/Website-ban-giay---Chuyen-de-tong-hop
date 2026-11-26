    window.openSizeGuide = async function (url) {
            const modal = document.getElementById('sizeGuideModal');
            const body = document.getElementById('sizeGuideBody');
            if (!modal || !body || !url) return;

            body.innerHTML = '<p style="text-align: center; color: #777; padding: 20px 0;">Đang tải bảng hướng dẫn chọn size...</p>';
            modal.style.display = 'block';

            try {
                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (!res.ok) {
                    body.innerHTML = '<p style="color: #d9534f; padding: 15px 0;">' + (data.message || 'Không tải được bảng hướng dẫn chọn size.') + '</p>';
                    return;
                }

                body.innerHTML = data.html;
            } catch (err) {
                body.innerHTML = '<p style="color: #d9534f; padding: 15px 0;">Có lỗi kết nối, vui lòng thử lại.</p>';
            }
        };

        window.closeSizeGuide = function () {
            const modal = document.getElementById('sizeGuideModal');
            if (modal) modal.style.display = 'none';
        };

        // Initialize product title marquee effect for long titles
        document.addEventListener('DOMContentLoaded', function () {
            function initProductTitleMarquee() {
                document.querySelectorAll('.col-4').forEach(function (card) {
                    const title = card.querySelector('h4');
                    if (!title) return;

                    const overflow = title.scrollWidth - title.clientWidth;
                    if (overflow > 2) {
                        card.classList.add('has-long-title');
                        title.style.setProperty('--marquee-offset', '-' + (overflow + 8) + 'px');
                        title.style.setProperty('--marquee-duration', Math.max(2.5, overflow / 35).toFixed(2) + 's');
                    } else {
                        card.classList.remove('has-long-title');
                    }
                });
            }

            initProductTitleMarquee();
            window.addEventListener('resize', initProductTitleMarquee);
        });