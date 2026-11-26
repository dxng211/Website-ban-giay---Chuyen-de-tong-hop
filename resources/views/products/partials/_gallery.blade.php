@php
    $images = $product->images;
    $mainImage = $images->first();
    $mainUrl = $mainImage ? asset($mainImage->image_url) : asset('images/product-11.jpg');
@endphp

<div class="col-2">
    <img src="{{ $mainUrl }}" width="100%" id="productImg" alt="{{ $product->name }}">
    @if($images->count() > 1)
        <div class="small-img-row">
            @foreach($images->skip(1) as $img)
                <div class="small-img-col">
                    <img src="{{ asset($img->image_url) }}" width="100%" class="small-img" alt="{{ $product->name }}">
                </div>
            @endforeach
        </div>
    @endif
</div>