@php
    $rating = (float) ($rating ?? 0);
@endphp
<div class="rating">
    @for($i = 1; $i <= 5; $i++)
        @if($rating >= $i)
            <i class="fa fa-star"></i>
        @elseif($rating >= $i - 0.5)
            <i class="fa fa-star-half-o"></i>
        @else
            <i class="fa fa-star-o"></i>
        @endif
    @endfor
    @if(!empty($showText))
        <span>({{ number_format($rating, 1) }}/5 - {{ $count ?? 0 }} đánh giá)</span>
    @endif
</div>