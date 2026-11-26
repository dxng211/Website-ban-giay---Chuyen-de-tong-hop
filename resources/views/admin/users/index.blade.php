@extends('layouts.admin')

@section('title', 'Người dùng - Admin')
@section('page-title', 'Quản Lý Người Dùng')

@section('content')
@php
    $userItems = $users ?? collect();
    $currentSearch = (string) request('q', request('search', ''));
    $currentStatus = (string) request('status', '');
@endphp

<div class="card">
    <form method="GET" action="{{ url()->current() }}" class="bar">
        <input type="text" name="q" value="{{ $currentSearch }}" placeholder="Tìm theo tên / email...">
        <select name="status" onchange="this.form.submit()">
            <option value="" {{ $currentStatus === '' ? 'selected' : '' }}>Tất cả tài khoản</option>
            <option value="active" {{ $currentStatus === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
            <option value="locked" {{ in_array($currentStatus, ['locked', 'blocked', 'inactive'], true) ? 'selected' : '' }}>Đã khóa</option>
        </select>
    </form>

    <div class="wrap">
        <table>
            <tr>
                <th>Họ tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Địa chỉ mặc định</th>
                <th>Ngày tạo</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
            @forelse ($userItems as $user)
                @php
                    $fullName = $user->full_name ?? $user->name ?? 'Người dùng';
                    $email = $user->email ?? '—';
                    $phone = $user->phone ?? $user->phone_number ?? '—';

                    $firstAddress = is_iterable($user->addresses ?? null)
                        ? collect($user->addresses)->first()
                        : null;

                    $defaultAddr = $user->default_address
                        ?? $user->address
                        ?? (optional($user->defaultAddress)->full_address ?? null)
                        ?? (optional($user->defaultAddress)->address ?? null)
                        ?? (optional($firstAddress)->full_address ?? null)
                        ?? (optional($firstAddress)->address ?? null)
                        ?? '—';

                    $createdText = $user->created_at
                        ? \Illuminate\Support\Carbon::parse($user->created_at)->format('d/m/Y')
                        : '—';

                    $isLocked = false;
                    if (isset($user->is_locked)) {
                        $isLocked = (bool) $user->is_locked;
                    } elseif (isset($user->is_active)) {
                        $isLocked = !((bool) $user->is_active);
                    } elseif (isset($user->status)) {
                        $st = strtolower((string) ($user->status instanceof \BackedEnum ? $user->status->value : $user->status));
                        $isLocked = in_array($st, ['locked', 'blocked', 'inactive', 'banned', '0'], true);
                    }

                    $toggleUrl = null;
                    if (\Illuminate\Support\Facades\Route::has('admin.users.toggle-lock')) {
                        $toggleUrl = route('admin.users.toggle-lock', $user);
                    } elseif (\Illuminate\Support\Facades\Route::has('admin.users.toggleLock')) {
                        $toggleUrl = route('admin.users.toggleLock', $user);
                    } elseif (\Illuminate\Support\Facades\Route::has('admin.users.lock')) {
                        $toggleUrl = route('admin.users.lock', $user);
                    }
                @endphp
                <tr>
                    <td>{{ $fullName }}</td>
                    <td>{{ $email }}</td>
                    <td>{{ $phone ?: '—' }}</td>
                    <td>{{ $defaultAddr ?: '—' }}</td>
                    <td>{{ $createdText }}</td>
                    <td>
                        @if ($isLocked)
                            <span class="badge locked">Đã khóa</span>
                        @else
                            <span class="badge on-s">Hoạt động</span>
                        @endif
                    </td>
                    <td>
                        @if ($toggleUrl)
                            <form method="POST" action="{{ $toggleUrl }}" class="inline-form" onsubmit="{{ !$isLocked ? "return confirm('Khóa tài khoản này?');" : '' }}">
                                @csrf
                                @method('PATCH')
                                @if ($isLocked)
                                    <button type="submit" class="btn sm">Mở khóa</button>
                                @else
                                    <button type="submit" class="btn sm gray">Khóa</button>
                                @endif
                            </form>
                        @else
                            <button type="button" class="btn sm {{ $isLocked ? '' : 'gray' }}">{{ $isLocked ? 'Mở khóa' : 'Khóa' }}</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:#999">Chưa có người dùng nào.</td></tr>
            @endforelse
        </table>
    </div>

    @if (method_exists($userItems, 'links'))
        <div class="pagination-box">{{ $userItems->withQueryString()->links() }}</div>
    @endif

    <p class="hint" style="margin-top:10px">PATCH /api/admin/users/{id}/toggle-lock — tài khoản bị khóa không đăng nhập được. Email không cho sửa.</p>
</div>
@endsection