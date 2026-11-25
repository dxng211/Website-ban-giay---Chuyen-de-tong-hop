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
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Trang chủ</a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Sản phẩm</a>
                    </li>
                    <li>
                        <a href="{{ route('accessories.index') }}" class="{{ request()->routeIs('accessories.*') ? 'active' : '' }}">Phụ kiện</a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">Giới thiệu</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Liên hệ</a>
                    </li>
                    <li>
                        <a href="{{ route('account') }}" class="{{ request()->routeIs('account*') ? 'active' : '' }}">Tài khoản</a>
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