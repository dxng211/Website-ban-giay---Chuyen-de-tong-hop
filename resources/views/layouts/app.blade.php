<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hucestore | Cửa Hàng Thể Thao & Thời Trang')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style2.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    @stack('styles')
</head>
<body>
    {{-- Header cố định --}}
    @include('partials._header')

    {{-- Thông báo session (success / error / warning / info) --}}
    @include('partials._flash-messages')

    {{-- Nội dung chính của từng trang --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer dùng chung --}}
    @include('partials._footer')

    {{-- Pop-up vỏ rỗng bảng hướng dẫn chọn size (nạp nội dung qua AJAX) --}}
    @include('partials._size-guide-modal')

    {{-- Widget Chatbot AI (nếu trang truyền biến cấu hình hoặc @section('chatbot')) --}}
    @yield('chatbot')

    {{-- JS dùng chung toàn site (menu mobile, CSRF helper, popup size guide) --}}
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')

    @include('partials._size-guide-modal')

    <script>
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
    </script>
</body>
</html>