<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#087f8c">
    <title>@yield('title', 'Info Perjalanan')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">IP</span>
            <span>Info Perjalanan</span>
        </a>
        <span class="topbar-note">Portal Peserta</span>
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
        <p>Informasi acara dan perjalanan peserta.</p>
    </footer>
</body>
</html>
