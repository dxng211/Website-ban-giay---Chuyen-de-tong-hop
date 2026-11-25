@extends('layouts.app')

@section('title', 'Giới Thiệu - Hucestore')

@section('content')
    <div class="small-container" style="margin-top: 40px; margin-bottom: 60px;">
        <div class="row">
            <div class="col-2">
                <h1>Về Chúng Tôi - Hucestore</h1>
                <p>Hucestore được thành lập nhằm mang tới cho cộng đồng yêu thích thể thao và thời trang những đôi giày chất lượng cao nhất với giá thành hợp lý.</p>
                <p style="margin-top: 15px;">Chúng tôi luôn áp dụng công nghệ mới nhất cùng hệ thống tư vấn AI hiện đại để mang lại trải nghiệm mua sắm tuyệt vời cho bạn.</p>
            </div>
            <div class="col-2">
                <img src="{{ asset('images/image1.png') }}" alt="About Hucestore">
            </div>
        </div>
    </div>
@endsection