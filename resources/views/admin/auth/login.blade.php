<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Đăng nhập Admin - Hucestore</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        :root{--c:#ff523b;--d:#563434;--bg:#f5f6fa}
        body{font-family:'Poppins',sans-serif;background:var(--bg);color:#555;font-size:14px}
        .btn{display:inline-block;background:var(--c);color:#fff;padding:7px 20px;border:0;border-radius:30px;cursor:pointer;font:inherit;transition:background .4s}
        .btn:hover{background:var(--d)}
        input{font:inherit;border:1px solid #ccc;border-radius:5px;padding:8px 10px;outline:none;width:100%}
        input:focus{border-color:var(--c)}
        .hint{font-size:12px;color:#999}
        .login{min-height:100vh;display:flex;align-items:center;justify-content:center;background:radial-gradient(#fff,#ffd6d6)}
        .login form{background:#fff;padding:36px;border-radius:10px;width:340px;box-shadow:0 0 20px rgba(0,0,0,.1);text-align:center}
        .login h2{color:#333;margin-bottom:20px}
        .login input{margin-bottom:12px}
    </style>
</head>
<body class="login">
    <form method="POST" action="{{ url()->current() }}">
        @csrf
        <h2><span style="color:#ff523b">Huce</span>store Admin</h2>
        @if ($errors->any())
            <p style="color:#c0392b;font-size:12px;margin-bottom:10px">{{ $errors->first() }}</p>
        @endif
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email quản trị" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit" class="btn" style="width:100%">Đăng Nhập</button>
        <p class="hint" style="margin-top:12px">Gọi POST /api/admin/auth/login (role = admin)</p>
    </form>
</body>
</html>