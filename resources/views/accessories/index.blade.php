@extends('layouts.app')

@section('title', 'Phụ Kiện Giày - Hucestore')

@section('content')
    <div class="small-container">
        <div class="row row-2">
            <h2>Phụ Kiện Giày Chăm Sóc & Bảo Vệ</h2>
        </div>

        <!-- ===== SECTION: bộ lọc phụ kiện ===== -->
        @include('accessories.partials._filter-accessories')

        <!-- ===== SECTION: danh sách phụ kiện ===== -->
        <div class="row">
            @forelse($products as $product)
                <x-product-card :product="$product" variant="default" />
            @empty
                <p style="padding: 20px 0; color: #777;">Không tìm thấy phụ kiện phù hợp với bộ lọc hiện tại.</p>
            @endforelse
        </div>

        @if($products->hasPages())
            <div style="margin: 20px 0 40px;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection