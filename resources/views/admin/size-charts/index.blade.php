@extends('layouts.admin')

@section('title', 'Quản lý bảng size')
@section('page_title', 'Quản Trị Hệ Thống')

@push('styles')
<style>
    .size-index-wrap {
        max-width: 1040px;
        margin: 0 auto;
    }

    .size-index-card {
        background: #ffffff;
        border: 1px solid #eceef2;
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 6px 24px rgba(15, 23, 42, 0.04);
    }

    .size-index-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .size-index-title {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
    }

    .size-index-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: #64748b;
    }

    .btn-accent-sm {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        background: #ff523b;
        color: #ffffff;
        font-size: 13.5px;
        font-weight: 600;
        border: none;
        border-radius: 9px;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.18s ease;
    }

    .btn-accent-sm:hover {
        background: #e6422c;
        color: #ffffff;
    }

    .alert-success-box {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 18px;
        font-size: 13.5px;
        font-weight: 500;
    }

    .size-table-wrap {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .size-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        background: #ffffff;
    }

    .size-table thead th {
        background: #1e293b;
        color: #ffffff;
        font-weight: 600;
        text-align: left;
        padding: 13px 16px;
        border-bottom: 2px solid #ff523b;
    }

    .size-table tbody td {
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        vertical-align: middle;
    }

    .size-table tbody tr:last-child td {
        border-bottom: none;
    }

    .size-table tbody tr:hover {
        background: #fff8f7;
    }

    .key-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 7px;
        background: #f1f5f9;
        color: #0f172a;
        font-family: monospace;
        font-size: 13px;
        font-weight: 600;
    }

    .action-group {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-edit-soft {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        background: #fff5f3;
        color: #ff523b;
        border: 1px solid #ffd5ce;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-edit-soft:hover {
        background: #ff523b;
        color: #ffffff;
        border-color: #ff523b;
    }

    .btn-delete-soft {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        background: #fff5f5;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-delete-soft:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
    }

    .empty-row {
        text-align: center !important;
        color: #64748b !important;
        padding: 28px 16px !important;
    }
</style>
@endpush

@section('content')
<div class="size-index-wrap">
    <div class="size-index-card">
        <div class="size-index-header">
            <div>
                <h2 class="size-index-title">Danh sách nhóm bảng size</h2>
                <p class="size-index-subtitle">Quản lý các nhóm bảng size và quy đổi kích thước cho sản phẩm.</p>
            </div>
            <a href="{{ route('admin.size-charts.create') }}" class="btn-accent-sm">+ Thêm nhóm bảng size</a>
        </div>

        @if (session('success'))
            <div class="alert-success-box">
                {{ session('success') }}
            </div>
        @endif

        <div class="size-table-wrap">
            <table class="size-table">
                <thead>
                    <tr>
                        <th style="width: 22%;">Chart Key</th>
                        <th>Tên bảng size</th>
                        <th style="width: 190px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groups ?? [] as $group)
                        @php
                            $chartKey = $group->chart_key ?? ($group->key ?? '');
                            $chartName = $group->chart_name ?? ($group->name ?? ($group->title ?? ''));
                        @endphp
                        <tr>
                            <td><span class="key-badge">{{ $chartKey }}</span></td>
                            <td><strong>{{ $chartName }}</strong></td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('admin.size-charts.edit', ['key' => $chartKey]) }}" class="btn-edit-soft">Sửa</a>
                                    <form
                                        action="{{ route('admin.size-charts.destroy', ['key' => $chartKey]) }}"
                                        method="POST"
                                        style="display:inline"
                                        onsubmit="return confirm('Xóa bảng size {{ $chartKey }}?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete-soft">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty-row">Chưa có bảng size nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection