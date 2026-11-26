@extends('layouts.admin')

@section('title', 'Sửa bảng size')
@section('page_title', 'Quản Trị Hệ Thống')

@push('styles')
<style>
    .size-edit-wrap {
        max-width: 920px;
        margin: 0 auto;
    }

    .size-edit-card {
        background: #ffffff;
        border: 1px solid #eceef2;
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 6px 24px rgba(15, 23, 42, 0.04);
    }

    .size-edit-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 16px;
        margin-bottom: 22px;
        border-bottom: 1px solid #f1f3f5;
    }

    .size-edit-title {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
    }

    .size-key-pill {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 6px;
        background: #fff5f3;
        color: #ff523b;
        font-family: monospace;
        font-size: 16px;
    }

    .btn-accent-edit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        background: #ff523b;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 9px;
        cursor: pointer;
        transition: background-color 0.18s ease;
    }

    .btn-accent-edit:hover {
        background: #e6422c;
    }

    .btn-back-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 14px;
        background: #ffffff;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        text-decoration: none;
        transition: all 0.18s ease;
    }

    .btn-back-soft:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .edit-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #f1f3f5;
    }
</style>
@endpush

@section('content')
@php
    $chartKey = $group->chart_key ?? ($key ?? '');
@endphp

<div class="size-edit-wrap">
    <div class="size-edit-card">
        <div class="size-edit-header">
            <h2 class="size-edit-title">
                Sửa bảng size: <span class="size-key-pill">{{ $chartKey }}</span>
            </h2>
            <a href="{{ route('admin.size-charts.index') }}" class="btn-back-soft">Quay lại</a>
        </div>

        <form action="{{ route('admin.size-charts.update', ['key' => $chartKey]) }}" method="POST">
            @csrf
            @method('PUT')

            @include('admin.size-charts.partials._form', ['group' => $group, 'key' => $chartKey])

            <div class="edit-actions">
                <a href="{{ route('admin.size-charts.index') }}" class="btn-back-soft">Quay lại</a>
                <button type="submit" class="btn-accent-edit">Cập nhật bảng size</button>
            </div>
        </form>
    </div>
</div>
@endsection