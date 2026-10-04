<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home') | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>document.documentElement.classList.add('js');</script>
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    @php $is = fn (string $pattern) => request()->routeIs($pattern); @endphp

    <a href="#main-content" class="skip-link">Skip to main content</a>

    <header class="site-header">
        <nav class="navbar" aria-label="Main">
            <a href="{{ route('auctions.index') }}" class="brand">
                <span class="brand-mark" aria-hidden="true">A</span>
                {{ config('app.name') }}
            </a>

            <button type="button" class="nav-toggle" aria-expanded="false"
                    aria-controls="nav-menu" aria-label="Open navigation menu">
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
            </button>

            <div class="nav-menu" id="nav-menu">
                <ul class="nav-links">
                    <li>
                        <a href="{{ route('auctions.index') }}"
                           class="nav-link {{ $is('auctions.*') ? 'is-active' : '' }}"
                           @if ($is('auctions.*')) aria-current="page" @endif>Auctions</a>
                    </li>

                    @auth
                        @if (auth()->user()->isSeller())
                            <li>
                                <a href="{{ route('seller.dashboard') }}"
                                   class="nav-link {{ $is('seller.dashboard') ? 'is-active' : '' }}"
                                   @if ($is('seller.dashboard')) aria-current="page" @endif>Seller Dashboard</a>
                            </li>
                            <li>
                                <a href="{{ route('seller.auctions.index') }}"
                                   class="nav-link {{ $is('seller.auctions.*') ? 'is-active' : '' }}"
                                   @if ($is('seller.auctions.*')) aria-current="page" @endif>Manage Auctions</a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('buyer.dashboard') }}"
                                   class="nav-link {{ $is('buyer.dashboard') ? 'is-active' : '' }}"
                                   @if ($is('buyer.dashboard')) aria-current="page" @endif>Buyer Dashboard</a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <div class="nav-user">
                    @auth
                        <span class="nav-username">{{ auth()->user()->name }}</span>
                        <x-badge :role="auth()->user()->role" />
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-link">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="nav-link {{ $is('login') ? 'is-active' : '' }}"
                           @if ($is('login')) aria-current="page" @endif>Login</a>
                        <a href="{{ route('register') }}"
                           class="nav-link {{ $is('register') ? 'is-active' : '' }}"
                           @if ($is('register')) aria-current="page" @endif>Register</a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <main id="main-content" class="container" tabindex="-1">
        @if (session('status'))
            <x-alert>{{ session('status') }}</x-alert>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div>
                <span class="footer-brand">{{ config('app.name') }}</span>
                <p class="footer-copy">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>

            <nav aria-label="Footer">
                <ul class="footer-links">
                    <li><a href="{{ route('auctions.index') }}">Auctions</a></li>
                    @guest
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @endguest
                </ul>
            </nav>
        </div>
    </footer>
</body>
</html>