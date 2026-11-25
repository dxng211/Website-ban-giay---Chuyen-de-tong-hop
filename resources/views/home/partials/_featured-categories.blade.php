<div class="categories">
    <div class="small-container">
        <div class="row">
            @forelse($featuredCategories ?? [] as $category)
                <div class="col-3">
                    <a href="{{ $category->group_type === 'accessory' ? route('accessories.index', ['category' => $category->id]) : route('products.index', ['category' => $category->id]) }}">
                        <img src="{{ asset('images/' . ltrim($category->image, '/')) }}" alt="{{ $category->name }}">
                    </a>
                </div>
            @empty
                <div class="col-3"><img src="{{ asset('images/category-1.jpg') }}" alt="Cat 1"></div>
                <div class="col-3"><img src="{{ asset('images/category-2.jpg') }}" alt="Cat 2"></div>
                <div class="col-3"><img src="{{ asset('images/category-3 (2).jpg') }}" alt="Cat 3"></div>
            @endforelse
        </div>
    </div>
</div>