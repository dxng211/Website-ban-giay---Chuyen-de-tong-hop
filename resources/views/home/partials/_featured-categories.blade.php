@php
    $fallbackImages = [
        'images/category-1.jpg',
        'images/category-2.jpg',
        'images/category-3.jpg',
    ];
@endphp

@if(!empty($featuredCategories) && count($featuredCategories) > 0)
    <div class="categories">
        <div class="small-container">
            <div class="row">
                @foreach($featuredCategories as $index => $category)
                    @php
                        $rawGroup = $category->group_type ?? null;
                        $groupValue = is_object($rawGroup) ? ($rawGroup->value ?? null) : $rawGroup;
                        $isAccessory = $groupValue
                            ? $groupValue === 'accessory'
                            : ($category->slug !== 'giay');

                        $catUrl = $isAccessory
                            ? route('accessories.index', ['category' => $category->slug])
                            : route('products.index', ['category' => $category->slug]);

                        $imgPath = $category->image_url
                            ?? $category->image
                            ?? ($fallbackImages[$index % count($fallbackImages)] ?? 'images/category-1.jpg');
                    @endphp
                    <div class="col-3">
                        <a href="{{ $catUrl }}">
                            <img src="{{ asset(ltrim($imgPath, '/')) }}" alt="{{ $category->name }}">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif