<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Pengumuman — Info Perjalanan</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .announcement-wrap{max-width:1050px;margin:40px auto;padding:0 20px}
        .announcement-panel{background:#fff;border:1px solid #dce8eb;border-radius:14px;padding:20px;overflow-x:auto}
        .announcement-table{width:100%;border-collapse:collapse}
        .announcement-table th,.announcement-table td{text-align:left;padding:14px 10px;border-bottom:1px solid #e4ecef;vertical-align:top}
        .announcement-actions{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
        .announcement-actions a{color:#087f8c;text-decoration:none}
        .announcement-actions button{border:0;background:none;color:#c62828;cursor:pointer;font:inherit;padding:0}
        .announcement-button{display:inline-block;background:#087f8c;color:#fff;text-decoration:none;padding:11px 15px;border-radius:8px;font-weight:700}
        .announcement-alert{padding:12px 15px;background:#e8f7ef;color:#087443;border-radius:9px;margin:18px 0}
        .announcement-muted{color:#607d8b;font-size:13px}
        .announcement-status{font-weight:700}
    </style>
</head>
<body>
<header style="background:#fff;border-bottom:1px solid #dce8eb;padding:18px 20px">
    <div style="max-width:1050px;margin:auto;display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <strong>Info Perjalanan · Admin</strong>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            &nbsp;·&nbsp;
            <a href="{{ route('admin.events.index') }}">Manajemen acara</a>
        </nav>
    </div>
</header>

<main class="announcement-wrap">
    <p><a href="{{ route('admin.events.index') }}">← Kembali ke manajemen acara</a></p>
    <h1>Manajemen pengumuman</h1>
    <h2>{{ $event->name }}</h2>
    <p class="announcement-muted">Pengumuman hanya berlaku untuk acara ini.</p>

    @if (session('success'))
        <div class="announcement-alert">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="announcement-alert" style="background:#fff0f0;color:#a52222">
            {{ $errors->first() }}
        </div>
    @endif

    <p>
        <a class="announcement-button"
           href="{{ route('admin.events.announcements.create', $event) }}">
            + Tambah pengumuman
        </a>
    </p>

    <section class="announcement-panel">
        @if ($announcements->isEmpty())
            <p>Belum ada pengumuman untuk acara ini.</p>
        @else
            <table class="announcement-table">
                <thead>
                    <tr>
                        <th>Judul dan isi</th>
                        <th>Status</th>
                        <th>Waktu publikasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($announcements as $announcement)
                        <tr>
                            <td>
                                <strong>{{ $announcement->title }}</strong>
                                <p>{{ \Illuminate\Support\Str::limit($announcement->body, 160) }}</p>
                            </td>
                            <td class="announcement-status">
                                @if ($announcement->published_at && $announcement->published_at->lte(now()))
                                    <span style="color:#087f8c">Terbit</span>
                                @else
                                    <span style="color:#9a6700">Draft / terjadwal</span>
                                @endif
                            </td>
                            <td>
                                {{ $announcement->published_at?->format('d M Y H:i') ?? 'Belum diterbitkan' }}
                            </td>
                            <td>
                                <div class="announcement-actions">
                                    <a href="{{ route('admin.events.announcements.edit', [$event, $announcement]) }}">Edit</a>
                                    <form method="POST"
                                          action="{{ route('admin.events.announcements.destroy', [$event, $announcement]) }}"
                                          onsubmit="return confirm('Hapus pengumuman ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:20px">{{ $announcements->links() }}</div>
        @endif
    </section>
</main>
</body>
</html>
