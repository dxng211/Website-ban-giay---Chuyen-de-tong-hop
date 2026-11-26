@extends('layouts.admin')

@section('title', ($category->exists ?? false) ? 'Sửa danh mục - Admin' : 'Thêm danh mục - Admin')
@section('page-title', ($category->exists ?? false) ? 'Sửa Danh Mục' : 'Thêm Danh Mục Mới')

@section('content')
@php
    $isEdit = (bool) ($category->exists ?? false);
    $backUrl = \Illuminate\Support\Facades\Route::has('admin.categories.index')
        ? route('admin.categories.index')
        : url('/admin/categories');
    $storeUrl = \Illuminate\Support\Facades\Route::has('admin.categories.store')
        ? route('admin.categories.store')
        : url('/admin/categories');
    $updateUrl = $isEdit
        ? (\Illuminate\Support\Facades\Route::has('admin.categories.update')
            ? route('admin.categories.update', ['id' => $category->id])
            : url('/admin/categories/' . $category->id))
        : $storeUrl;

    $rawGroup = $category->group_type ?? $category->type ?? 'shoe';
    $groupVal = $rawGroup instanceof \BackedEnum ? (string) $rawGroup->value : (string) $rawGroup;
    $groups = $sizeChartGroups ?? collect();
@endphp

<div class="card" style="max-width:720px;margin:0 auto 24px">
    <div class="bar">
        <h3 style="margin:0">{{ $isEdit ? 'Cập nhật danh mục #' . $category->id : 'Tạo danh mục mới' }}</h3>
        <span class="sp"></span>
        <a href="{{ $backUrl }}" class="btn sm gray"><i class="fa fa-arrow-left"></i> Quay lại</a>
    </div>

    @if ($errors->any())
        <div style="background:#fff1f0;border:1px solid #ffa39e;color:#cf1322;padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:13px">
            <ul style="margin:0;padding-left:18px">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $updateUrl }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="row2">
            <div class="fg">
                <label>Tên danh mục *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required>
            </div>
            <div class="fg">
                <label>Nhóm danh mục</label>
                <select name="group_type">
                    <option value="shoe" {{ old('group_type', $groupVal) === 'shoe' ? 'selected' : '' }}>Giày (shoe)</option>
                    <option value="accessory" {{ old('group_type', $groupVal) === 'accessory' ? 'selected' : '' }}>Phụ kiện (accessory)</option>
                </select>
            </div>
        </div>

        <div class="row2">
            <div class="fg">
                <label>Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="tu-dong-neu-de-trong">
            </div>
            <div class="fg">
                <label>Bảng size áp dụng</label>
                <select name="size_chart_key">
                    <option value="">-- Không dùng bảng size --</option>
                    @foreach ($groups as $g)
                        @php
                            $k = $g->chart_key ?? $g->key ?? '';
                            $label = $g->name ?? $k;
                        @endphp
                        <option value="{{ $k }}" {{ (string) old('size_chart_key', $category->size_chart_key ?? '') === (string) $k ? 'selected' : '' }}>
                            {{ $label }} ({{ $k }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="fg">
            <label>Mô tả</label>
            <textarea name="description" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
        </div>

        <div class="fg">
            <label style="font-weight:normal;cursor:pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }} style="width:auto;margin-right:6px">
                Đang hiển thị
            </label>
        </div>

        <div style="display:flex;gap:10px;align-items:center;margin-top:18px">
            <button type="submit" class="btn"><i class="fa fa-check"></i> {{ $isEdit ? 'Lưu thay đổi' : 'Tạo danh mục' }}</button>
            <a href="{{ $backUrl }}" class="btn gray">Quay lại</a>
        </div>
    </form>
</div>
@endsection