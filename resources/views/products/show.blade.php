@extends('layouts.app')

@section('title', $product->name . ' - Hucestore')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/product-detail.css') }}">
@endpush

@section('content')
    <div class="small-container single-product">
        <div class="row">
            @include('products.partials._gallery')
            @include('products.partials._info')
        </div>

        @include('products.partials._reviews')
        @include('products.partials._related')
    </div>

    @include('chatbot.partials._widget', [
        'mode' => 'size',
        'productId' => $product->id,
        'greeting' => 'Bạn đang xem sản phẩm <strong>' . e($product->name) . '</strong>, có cần tôi giúp gì không?',
    ])
@endsection

@push('scripts')
    <script src="{{ asset('js/product-detail.js') }}"></script>
@endpush