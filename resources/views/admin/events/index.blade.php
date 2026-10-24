<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Acara — Info Perjalanan</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f8f8; color: #183344; font-family: Arial, sans-serif; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 18px max(22px, calc((100% - 1080px)/2)); background: #fff; border-bottom: 1px solid #dce9eb; }
        a { color: #087f8c; text-decoration: none; }
        main { max-width: 1080px; margin: 32px auto; padding: 0 22px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .button { display: inline-block; padding: 11px 15px; border: 0; border-radius: 9px; background: #087f8c; color: #fff; font-weight: 700; cursor: pointer; }
        .secondary { background: #fff; border: 1px solid #dce9eb; color: #183344; }
        .panel { margin-top: 22px; padding: 20px; background: #fff; border: 1px solid #dce9eb; border-radius: 14px; }
        .search { display: flex; gap: 8px; flex-wrap: wrap; }
        input { min-width: 180px; flex: 1; padding: 11px; border: 1px solid #cedde1; border-radius: 8px; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 10px; text-align: left; border-bottom: 1px solid #e5edef; vertical-align: top; font-size: 14px; }
        th { color: #627b89; }
        .status { font-weight: 700; }
        .published { color: #087f8c; }
        .draft { color: #8a5a00; }
        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
        .inline { display: inline; }
        .danger { padding: 0; border: 0; background: none; color: #b42318; cursor: pointer; font: inherit; }
        .notice { margin: 14px 0; padding: 12px; border-radius: 9px; background: #eaf8ef; color: #176b3a; }
        .error { background: #fff0ef; color: #a52b27; }
        .empty { padding: 22px 0; color: #627b89; }
        .pagination { margin-top: 18px; }
        @media (max-width: 700px) {
            header { padding: 15px 20px; }
            main { margin-top: 22px; padding: 0 14px; }
            .panel { overflow-x: auto; padding: 12px; }
            table { min-width: 680px; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<header>
    <strong>Info Perjalanan · Admin</strong>
    <nav>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        &nbsp;·&nbsp;
        <form class="inline" method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="danger" type="submit">Keluar</button>
        </form>
    </nav>
</header>

<main>
    <div class="toolbar">
        <div>
            <h1>Manajemen acara</h1>
            <p class="muted">Tambah dan kelola informasi perjalanan.</p>
        </div>
        <a class="button" href="{{ route('admin.events.create') }}">+ Tambah acara</a>
    </div>

    @if (session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="notice error">{{ session('error') }}</div>
    @endif

    <section class="panel">
        <form class="search" method="GET" action="{{ route('admin.events.index') }}">
            <input name="q" value="{{ request('q') }}" placeholder="Cari nama atau lokasi acara..." aria-label="Cari acara">
            <button class="button" type="submit">Cari</button>
            @if (request('q'))
                <a class="button secondary" href="{{ route('admin.events.index') }}">Reset</a>
            @endif
        </form>

        @if ($events->isEmpty())
            <p class="empty">Belum ada acara yang cocok dengan pencarian.</p>
        @else
            <div class="table-scroll">
                <table>
                <thead>
                <tr>
                    <th>Nama acara</th>
                    <th>Lokasi</th>
                    <th>Mulai</th>
                    <th>Status</th>
                    <th>Pendaftar</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($events as $event)
                    <tr>
                        <td>
                            <strong>{{ $event->name }}</strong><br>
                            <small class="muted">{{ $event->slug }}</small>
                        </td>
                        <td>{{ $event->location ?: '—' }}</td>
                        <td>{{ $event->starts_at?->format('d M Y H:i') ?: '—' }}</td>
                        <td class="status {{ $event->status === 'published' ? 'published' : 'draft' }}">
                            {{ $event->status === 'published' ? 'Dipublikasikan' : 'Draft' }}
                        </td>
                        <td>{{ $event->registrations_count }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.events.edit', $event) }}">Edit</a>
                                <a href="{{ route('admin.events.schedules.index', $event) }}">Jadwal</a>
                                <a href="{{ route('admin.events.participants.index', $event) }}">Peserta</a>
                                @if ($event->registrations_count === 0)
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Yakin ingin menghapus acara ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger" type="submit">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                </table>
            </div>

            <div class="pagination">{{ $events->links() }}</div>
        @endif
    </section>
</main>
</body>
</html>
