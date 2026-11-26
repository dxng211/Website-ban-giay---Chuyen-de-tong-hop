<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Hucestore')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        :root{--c:#ff523b;--d:#563434;--bg:#f5f6fa}
        body{font-family:'Poppins',sans-serif;background:var(--bg);color:#555;font-size:14px}
        a{text-decoration:none;color:inherit}
        .sidebar{position:fixed;top:0;bottom:0;left:0;width:230px;background:#222;padding:22px 0;overflow:auto;z-index:50;transition:transform .3s}
        .brand{color:#fff;font-size:20px;font-weight:600;padding:0 22px 22px}.brand b{color:var(--c)}
        .sidebar a{display:flex;gap:12px;align-items:center;color:#bbb;padding:12px 22px;border-left:4px solid transparent}
        .sidebar a:hover,.sidebar a.on{background:#333;color:#fff;border-color:var(--c)}
        .main{margin-left:230px}
        .topbar{position:sticky;top:0;z-index:40;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.08);padding:14px 28px;display:flex;justify-content:space-between;align-items:center}
        .topbar h1{font-size:20px;color:#333}.burger{display:none;border:0;background:none;font-size:22px;cursor:pointer}
        .content{padding:28px}
        .card,.stat{background:#fff;border-radius:10px;box-shadow:0 0 20px rgba(0,0,0,.05);padding:20px;margin-bottom:24px}
        .card h3{color:#333;margin-bottom:14px}
        .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:20px}
        .stat{border-top:4px solid var(--c);margin:0}.stat b{display:block;font-size:24px;color:#333;margin-top:4px}
        .grid2{display:grid;grid-template-columns:1fr 1fr;gap:24px}
        .btn{display:inline-block;background:var(--c);color:#fff;padding:7px 20px;border:0;border-radius:30px;cursor:pointer;font:inherit;transition:background .4s}
        .btn:hover{background:var(--d)}.btn.sm{padding:4px 12px;font-size:12px}.btn.gray{background:#888}.btn.dark{background:#333}
        .bar{display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:16px}.bar .sp{flex:1}
        input,select,textarea{font:inherit;border:1px solid #ccc;border-radius:5px;padding:8px 10px;outline:none;width:100%}
        .bar input,.bar select{width:auto}input:focus,select:focus,textarea:focus{border-color:var(--c)}
        .wrap{overflow-x:auto}table{width:100%;border-collapse:collapse}
        th{background:var(--c);color:#fff;text-align:left;padding:10px;font-weight:normal;white-space:nowrap}
        td{padding:10px;border-bottom:1px solid #eee;vertical-align:middle}td.r,th.r{text-align:right}
        td img{width:48px;height:48px;object-fit:cover;border-radius:6px;vertical-align:middle;margin-right:8px}
        .badge{padding:3px 10px;border-radius:20px;font-size:12px;color:#fff;white-space:nowrap;display:inline-block}
        .pending{background:#f5a623}.shipping{background:#3498db}.completed,.approved,.on-s{background:#27ae60}.cancelled,.rejected{background:#999}.locked{background:#c0392b}
        .old{text-decoration:line-through;color:#999;font-size:12px}.price{color:var(--c);font-weight:bold}
        .stars{color:var(--c)}.hint{font-size:12px;color:#999}
        .chart{display:flex;align-items:flex-end;gap:18px;height:170px;padding-top:10px}
        .chart div{flex:1;text-align:center;font-size:12px}.chart i{display:block;background:var(--c);border-radius:6px 6px 0 0;margin-bottom:6px}
        .modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:2000;overflow:auto}
        .mc{background:#fff;margin:5% auto;padding:26px;border-radius:10px;width:92%;max-width:640px;position:relative}
        .mc h3{color:#333;margin-bottom:14px}.x{position:absolute;right:16px;top:8px;font-size:26px;cursor:pointer}
        .fg{margin-bottom:12px}.fg label{display:block;margin-bottom:4px;font-weight:500}.row2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        .vr{display:grid;grid-template-columns:1fr 1.3fr 90px 34px;gap:8px;margin-bottom:8px}
        .toast{position:fixed;bottom:25px;right:25px;background:#333;color:#fff;padding:12px 20px;border-radius:8px;z-index:3000;display:none}
        @media(max-width:800px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:none}.main{margin-left:0}.burger{display:block}.grid2,.row2{grid-template-columns:1fr}.content{padding:16px}}
        .sw{position:relative;display:inline-block;width:42px;height:22px;vertical-align:middle;margin-left:8px}.sw input{opacity:0;width:0;height:0}
        .sw span{position:absolute;inset:0;background:#ccc;border-radius:22px;cursor:pointer;transition:.3s}.sw span:before{content:'';position:absolute;width:16px;height:16px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.3s}
        .sw input:checked+span{background:var(--c)}.sw input:checked+span:before{transform:translateX(20px)}
        .ups{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
        .up{position:relative;border:2px dashed #ddd;border-radius:10px;background:#f9f9f9;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#777;cursor:pointer;min-height:70px;overflow:hidden;font-size:24px}
        .up:hover{border-color:var(--c);color:var(--c)}.up.big{grid-column:1/-1;min-height:160px;font-size:15px}.up.big i{font-size:28px;margin-bottom:6px}
        .up img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}.up input{display:none}
        .up .rm{position:absolute;top:6px;right:6px;width:22px;height:22px;line-height:20px;text-align:center;border-radius:50%;background:rgba(0,0,0,.6);color:#fff;font-size:15px;z-index:2;display:none}.up.has .rm{display:block}
        .fg label.sw{display:inline-block;margin:0 8px 0 8px;font-weight:normal}
        .inline-form{display:inline}
        .pagination-box{margin-top:14px;display:flex;justify-content:flex-end}
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="brand"><b>Huce</b>store Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">
            <i class="fa fa-tachometer"></i> Tổng quan
        </a>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'on' : '' }}">
            <i class="fa fa-shopping-bag"></i> Sản phẩm &amp; Tồn kho
        </a>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*', 'admin.size-charts.*') ? 'on' : '' }}">
            <i class="fa fa-tags"></i> Danh mục &amp; Size
        </a>
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'on' : '' }}">
            <i class="fa fa-file-text-o"></i> Đơn hàng
        </a>
        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'on' : '' }}">
            <i class="fa fa-star"></i> Đánh giá
        </a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'on' : '' }}">
            <i class="fa fa-users"></i> Người dùng
        </a>
        <a href="{{ url('/') }}" style="margin-top:20px">
            <i class="fa fa-external-link"></i> Xem website
        </a>
    </aside>

    <div class="toast" id="toast"></div>

    <div class="main">
        <div class="topbar">
            <div>
                <button type="button" class="burger" onclick="document.getElementById('sidebar').classList.toggle('open')">&#9776;</button>
                <h1 style="display:inline">@yield('page-title', 'Quản Trị Hệ Thống')</h1>
            </div>
            <div>
                <i class="fa fa-user-circle"></i> {{ auth()->user()->email ?? 'admin@hucestore.com' }} &nbsp;
                @if (\Illuminate\Support\Facades\Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" class="inline-form">
                        @csrf
                        <button type="submit" class="btn sm dark">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ url('/admin/login') }}" class="btn sm dark">Đăng xuất</a>
                @endif
            </div>
        </div>

        <div class="content">
            @if (session('success'))
                <div class="card" style="padding:12px 18px;border-left:4px solid #27ae60;color:#27ae60;font-weight:500;">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="card" style="padding:12px 18px;border-left:4px solid #c0392b;color:#c0392b;">
                    @foreach ($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        const $ = id => document.getElementById(id);
        function openM(id) { const el = $(id); if (el) el.style.display = 'block'; }
        function closeM(id) { const el = $(id); if (el) el.style.display = 'none'; }
        function toast(m) {
            const t = $('toast');
            if (!t) return;
            t.textContent = m;
            t.style.display = 'block';
            setTimeout(() => t.style.display = 'none', 2600);
        }
        const money = n => Number(n || 0).toLocaleString('vi-VN') + '₫';
    </script>
    @stack('scripts')
</body>
</html>