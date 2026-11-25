@extends('layouts.app')

@section('title', 'Hucestore | Cửa Hàng Thể Thao & Thời Trang')

@section('content')
    {{-- Banner chính --}}
    @include('home.partials._banner')

    {{-- Danh mục nổi bật --}}
    @include('home.partials._featured-categories')

    <div class="small-container">
        {{-- Sản phẩm bán chạy (sắp theo products.sold_count giảm dần) --}}
        <h2 class="title">Sản Phẩm Bán Chạy</h2>
        <div class="row">
            @foreach($bestSellerProducts ?? [] as $product)
                <x-product-card :product="$product" variant="default" />
            @endforeach
        </div>

        {{-- Sản phẩm mới nhất (sắp theo products.created_at giảm dần) --}}
        <h2 class="title">Sản Phẩm Mới Nhất</h2>
        <div class="row">
            @foreach($newestProducts ?? [] as $product)
                <x-product-card :product="$product" variant="default" />
            @endforeach
        </div>
    </div>
@endsection

@section('chatbot')
    @include('chatbot.partials._widget', [
        'mode' => 'recommend',
        'greeting' => 'Hỏi AI tư vấn ngay!',
    ])
@endsection

@push('scripts')
    <script src="{{ asset('js/chatbot.js') }}"></script>
@endpush