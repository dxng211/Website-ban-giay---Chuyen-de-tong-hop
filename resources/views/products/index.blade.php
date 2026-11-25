@extends('layouts.app')

@section('title', 'Sản Phẩm Giày - Hucestore')

@section('content')
    <div class="small-container">
        <div class="row row-2">
            <h2>Tất Cả Giày</h2>
        </div>

        <!-- ===== SECTION: 3 bộ lọc ===== -->
        @include('products.partials._filter-shoes')

        <!-- ===== SECTION: danh sách giày (chỉ category group_type = 'shoe') ===== -->
        <div class="row">
            @forelse($products as $product)
                <x-product-card :product="$product" variant="default" />
            @empty
                <p style="padding: 20px 0; color: #777;">Không tìm thấy mẫu giày phù hợp với bộ lọc hiện tại.</p>
            @endforelse
        </div>

        @if($products->hasPages())
            <div style="margin: 20px 0 40px;">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    @includeIf('chatbot.partials._widget', [
        'mode' => 'recommend',
        'greeting' => 'Hỏi AI tư vấn ngay!',
    ])
@endsection