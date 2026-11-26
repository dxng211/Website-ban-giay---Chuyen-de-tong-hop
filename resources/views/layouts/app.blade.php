<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hucestore | Cửa Hàng Thể Thao & Thời Trang')</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

</body>
</html>