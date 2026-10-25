<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173f32">
    <title>@yield('title', 'Info Perjalanan')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="{{ request()->routeIs('home') ? 'home-page' : (request()->routeIs('events.show') ? 'event-page' : '') }}">
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">IP</span>
            <span class="brand-name">Info Perjalanan<span class="brand-tagline">TEMUKAN • JELAJAHI • KENANGI</span></span>
        </a>
        <span class="topbar-note">Ruang perjalanan Anda <span aria-hidden="true">↗</span></span>
    </header>

    <main class="container">
        @if (session('success'))
            <div class="alert success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert error" role="alert">
                <strong>Periksa kembali data Anda.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <p><span class="footer-mark">IP</span> &nbsp; Info Perjalanan <span class="footer-divider">/</span> Informasi perjalanan, dirangkai dengan lebih baik.</p>
    </footer>
</body>
</html>
