@extends('layouts.admin')

@section('title', 'Tạo nhóm bảng size mới')
@section('page_title', 'Quản Trị Hệ Thống')

@push('styles')
<style>
    .size-form-wrap {
        max-width: 920px;
        margin: 0 auto;
    }

    .size-card {
        background: #ffffff;
        border: 1px solid #eceef2;
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 6px 24px rgba(15, 23, 42, 0.04);
    }

    .size-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 16px;
        margin-bottom: 22px;
        border-bottom: 1px solid #f1f3f5;
    }

    .size-card-title {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
    }

    .size-card-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: #64748b;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    .form-hint {
        font-weight: 400;
        color: #94a3b8;
        font-size: 12px;
    }

    .form-control-custom {
        width: 100%;
        padding: 10px 13px;
        font-size: 14px;
        color: #0f172a;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        outline: none;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        box-sizing: border-box;
    }

    .form-control-custom:focus {
        background: #ffffff;
        border-color: #ff523b;
        box-shadow: 0 0 0 3px rgba(255, 82, 59, 0.14);
    }

    .size-rows-section {
        margin-top: 6px;
        padding: 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .size-rows-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        gap: 10px;
    }

    .size-rows-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .size-row-item {
        display: grid;
        grid-template-columns: minmax(140px, 1fr) minmax(200px, 1.5fr) auto;
        gap: 12px;
        align-items: center;
        margin-bottom: 12px;
    }

    .size-row-item:last-child {
        margin-bottom: 0;
    }

    .btn-accent {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 18px;
        background: #ff523b;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 9px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.18s ease, transform 0.12s ease;
    }

    .btn-accent:hover {
        background: #e6422c;
    }

    .btn-accent:active {
        transform: translateY(1px);
    }

    .btn-secondary-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 14px;
        background: #ffffff;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        border-radius: 9px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.18s ease;
    }

    .btn-secondary-soft:hover {
        border-color: #94a3b8;
        background: #f1f5f9;
    }

    .btn-remove-row {
        padding: 9px 12px;
        background: #fff5f5;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.18s ease;
    }

    .btn-remove-row:hover {
        background: #fee2e2;
        border-color: #fca5a5;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 24px;
        padding-top: 18px;
        border-top: 1px solid #f1f3f5;
    }

    .alert-danger-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 18px;
        font-size: 13.5px;
    }

    @media (max-width: 720px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }

        .size-row-item {
            grid-template-columns: 1fr;
        }

        .size-card {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="size-form-wrap">
    <div class="size-card">
        <div class="size-card-header">
            <div>
                <h2 class="size-card-title">Tạo nhóm bảng size mới</h2>
                <p class="size-card-subtitle">Thiết lập mã bảng size, tên hiển thị và danh sách các dòng size tương ứng.</p>
            </div>
            <a href="{{ route('admin.size-charts.index') }}" class="btn-secondary-soft">Quay lại</a>
        </div>

        @if ($errors->any())
            <div class="alert-danger-box">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.size-charts.store') }}" method="POST" id="sizeChartForm">
            @csrf

            <div class="form-grid-2">
                <div class="form-group">
                    <label for="chart_key" class="form-label">
                        Chart Key <span class="form-hint">(vd: shoe_vn)</span>
                    </label>
                    <input
                        type="text"
                        id="chart_key"
                        name="chart_key"
                        value="{{ old('chart_key') }}"
                        placeholder="vd: shoe_vn"
                        class="form-control-custom"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="chart_name" class="form-label">
                        Tên bảng size
                    </label>
                    <input
                        type="text"
                        id="chart_name"
                        name="chart_name"
                        value="{{ old('chart_name') }}"
                        placeholder="vd: Bảng size giày chuẩn VN"
                        class="form-control-custom"
                        required
                    >
                </div>
            </div>

            <div class="size-rows-section">
                <div class="size-rows-header">
                    <h3 class="size-rows-title">Các dòng size</h3>
                    <button type="button" id="addSizeRowBtn" class="btn-secondary-soft">
                        + Thêm dòng size
                    </button>
                </div>

                <div id="sizeRowsContainer">
                    @php
                        $oldItems = old('items', [['size' => '', 'note' => '']]);
                    @endphp
                    @foreach ($oldItems as $index => $item)
                        <div class="size-row-item" data-index="{{ $index }}">
                            <input
                                type="text"
                                name="items[{{ $index }}][size]"
                                value="{{ $item['size'] ?? '' }}"
                                placeholder="Size (vd: 39)"
                                class="form-control-custom"
                            >
                            <input
                                type="text"
                                name="items[{{ $index }}][note]"
                                value="{{ $item['note'] ?? '' }}"
                                placeholder="Chiều dài / ghi chú (vd: 24.5 cm)"
                                class="form-control-custom"
                            >
                            <button type="button" class="btn-remove-row" onclick="removeSizeRow(this)">
                                Xóa dòng
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.size-charts.index') }}" class="btn-secondary-soft">Quay lại</a>
                <button type="submit" class="btn-accent">Lưu bảng size</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let sizeRowIndex = document.querySelectorAll('#sizeRowsContainer .size-row-item').length;

    document.getElementById('addSizeRowBtn')?.addEventListener('click', function () {
        const container = document.getElementById('sizeRowsContainer');
        const row = document.createElement('div');
        row.className = 'size-row-item';
        row.dataset.index = sizeRowIndex;

        row.innerHTML = `
            <input
                type="text"
                name="items[${sizeRowIndex}][size]"
                placeholder="Size (vd: 39)"
                class="form-control-custom"
            >
            <input
                type="text"
                name="items[${sizeRowIndex}][note]"
                placeholder="Chiều dài / ghi chú (vd: 24.5 cm)"
                class="form-control-custom"
            >
            <button type="button" class="btn-remove-row" onclick="removeSizeRow(this)">
                Xóa dòng
            </button>
        `;

        container.appendChild(row);
        sizeRowIndex++;
    });

    function removeSizeRow(button) {
        const container = document.getElementById('sizeRowsContainer');
        const rows = container.querySelectorAll('.size-row-item');
        if (rows.length <= 1) {
            rows[0].querySelectorAll('input').forEach(input => input.value = '');
            return;
        }
        button.closest('.size-row-item')?.remove();
    }
</script>
@endpush