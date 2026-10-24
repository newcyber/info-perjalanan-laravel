<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#087f8c">
    <title>Login Admin — Info Perjalanan</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f3f8f8; color: #183344; font-family: Arial, sans-serif; }
        .card { width: 100%; max-width: 420px; padding: 32px; border: 1px solid #dce9eb; border-radius: 20px; background: #fff; box-shadow: 0 12px 36px #173b4b0d; }
        .brand { display: flex; align-items: center; gap: 12px; font-weight: 700; margin-bottom: 28px; }
        .logo { display: grid; place-items: center; width: 42px; height: 42px; border-radius: 12px; color: white; background: #087f8c; }
        h1 { margin: 0 0 8px; font-size: 27px; }
        p { color: #627b89; line-height: 1.6; }
        label { display: block; margin: 18px 0 7px; font-size: 14px; font-weight: 700; }
        input { width: 100%; padding: 12px 13px; border: 1px solid #cedde1; border-radius: 9px; font-size: 16px; }
        input:focus { outline: 2px solid #a5e1e4; border-color: #087f8c; }
        button { width: 100%; margin-top: 24px; padding: 13px; border: 0; border-radius: 10px; background: #087f8c; color: white; font-size: 15px; font-weight: 700; cursor: pointer; }
        .error { padding: 11px; border-radius: 8px; background: #fff0ef; color: #a52b27; font-size: 14px; }
        .success { padding: 11px; border-radius: 8px; background: #eaf8ef; color: #176b3a; font-size: 14px; }
        .back { display: block; margin-top: 22px; text-align: center; color: #087f8c; text-decoration: none; font-size: 14px; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<main class="card">
    <div class="brand"><span class="logo">IP</span> Info Perjalanan</div>
    <h1>Login administrator</h1>
    <p>Masuk untuk mengelola acara dan peserta.</p>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <div class="error" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.store') }}">
        @csrf
        <label for="email">Email administrator</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" autocomplete="current-password" required>

        <button class="button" type="submit">Masuk ke dashboard</button>
    </form>

    <a class="back" href="{{ route('home') }}">Kembali ke halaman acara</a>
</main>
</body>
</html>
