<div class="header-nav">
    <div class="container">
        <div class="navbar">
            <div class="logo">
                @if(request()->routeIs('home'))
                    <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">
                        <img src="{{ asset('images/logo.png') }}" width="125px" alt="Hucestore Logo">
                    </a>
                @else
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo.png') }}" width="125px" alt="Hucestore Logo">
                    </a>
                @endif
            </div>

            <nav>
                <ul id="MenuItems">
                    <li>
                        @if(request()->routeIs('home'))
                            <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">Trang chủ</a>
                        @else
                            <a href="{{ route('home') }}">Trang chủ</a>
                        @endif
                    </li>
                    <li>
                        @if(request()->routeIs('products.index'))
                            <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">Sản phẩm</a>
                        @else
                            <a href="{{ route('products.index') }}">Sản phẩm</a>
                        @endif
                    </li>
                    <li>
                        @if(request()->routeIs('accessories.index'))
                            <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">Phụ kiện</a>
                        @else
                            <a href="{{ route('accessories.index') }}">Phụ kiện</a>
                        @endif
                    </li>
                    <li>
                        @if(request()->routeIs('about'))
                            <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">Giới thiệu</a>
                        @else
                            <a href="{{ route('about') }}">Giới thiệu</a>
                        @endif
                    </li>
                    <li>
                        @if(request()->routeIs('contact'))
                            <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">Liên hệ</a>
                        @else
                            <a href="{{ route('contact') }}">Liên hệ</a>
                        @endif
                    </li>
                    <li>
                        @if(request()->routeIs('account'))
                            <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">Tài khoản</a>
                        @else
                            <a href="{{ route('account') }}">Tài khoản</a>
                        @endif
                    </li>
                </ul>
            </nav>

            <div class="cart-icon-container">
                @if(request()->routeIs('cart.index'))
                    <a href="javascript:void(0)" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">
                        <img src="{{ asset('images/cart.png') }}" width="30px" height="30px" alt="Giỏ hàng">
                    </a>
                @else
                    <a href="{{ route('cart.index') }}">
                        <img src="{{ asset('images/cart.png') }}" width="30px" height="30px" alt="Giỏ hàng">
                    </a>
                @endif
                {{-- Số lượng sản phẩm trong giỏ (Guest lẫn Customer, truyền qua View Composer) --}}
                <span class="cart-badge" id="headerCartBadge">{{ $cartCount ?? 0 }}</span>
            </div>

            <img src="{{ asset('images/menu.png') }}" class="menu-icon" onclick="menutoggle()" alt="Menu">
        </div>
    </div>
</div>