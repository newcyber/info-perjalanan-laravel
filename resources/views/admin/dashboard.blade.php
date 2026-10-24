<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#087f8c">
    <title>Dashboard Admin — Info Perjalanan</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f8f8; color: #183344; font-family: Arial, sans-serif; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 18px max(22px, calc((100% - 1080px)/2)); background: white; border-bottom: 1px solid #dce9eb; }
        .brand { font-weight: 700; }
        .logout { border: 1px solid #d2e0e3; background: white; border-radius: 8px; padding: 9px 13px; cursor: pointer; }
        main { max-width: 1080px; margin: 34px auto; padding: 0 22px; }
        h1 { margin-bottom: 8px; }
        .muted { color: #627b89; }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 15px; margin: 26px 0; }
        .stat, .panel { padding: 22px; background: white; border: 1px solid #dce9eb; border-radius: 14px; }
        .stat span { display: block; color: #627b89; font-size: 14px; }
        .stat strong { display: block; margin-top: 12px; font-size: 30px; }
        h2 { font-size: 19px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 10px; text-align: left; border-bottom: 1px solid #e5edef; font-size: 14px; }
        th { color: #627b89; }
        .status { color: #087f8c; font-weight: 700; }
        .empty { padding: 20px 0; color: #627b89; }
        @media (max-width: 700px) {
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .stat { padding: 16px; }
            .stat strong { font-size: 25px; }
            .panel { overflow-x: auto; padding: 15px; }
            header { padding: 16px 20px; }
        }
    </style>
</head>
<body>
<header>
    <div class="brand">Info Perjalanan · Admin</div>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="logout" type="submit">Keluar</button>
    </form>
</header>

<main>
    <h1>Dashboard administrator</h1>
    <p class="muted">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan data perjalanan.</p>

    <p><a href="{{ route('admin.events.index') }}" style="display:inline-block;padding:12px 16px;background:#087f8c;color:#fff;border-radius:9px;text-decoration:none;font-weight:700">Kelola Acara</a></p>

    <section class="stats">
        <div class="stat"><span>Total acara</span><strong>{{ $eventCount }}</strong></div>
        <div class="stat"><span>Acara dipublikasikan</span><strong>{{ $publishedEventCount }}</strong></div>
        <div class="stat"><span>Total peserta</span><strong>{{ $participantCount }}</strong></div>
        <div class="stat"><span>Total pendaftaran</span><strong>{{ $registrationCount }}</strong></div>
    </section>

    <section class="panel">
        <h2>Acara terbaru</h2>
        @if ($recentEvents->isEmpty())
            <p class="empty">Belum ada acara.</p>
        @else
            <table>
                <thead>
                    <tr><th>Nama acara</th><th>Status</th><th>Jumlah pendaftar</th></tr>
                </thead>
                <tbody>
                @foreach ($recentEvents as $event)
                    <tr>
                        <td>{{ $event->name }}</td>
                        <td class="status">{{ $event->status }}</td>
                        <td>{{ $event->registrations_count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </section>
</main>
</body>
</html>
