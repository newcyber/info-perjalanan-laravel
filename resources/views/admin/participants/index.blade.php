<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Peserta — Info Perjalanan</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .wrap{max-width:1100px;margin:36px auto;padding:0 18px}
        .panel{background:#fff;border:1px solid #dce8eb;border-radius:14px;padding:20px;overflow-x:auto}
        .toolbar{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin:20px 0}
        .toolbar input{padding:11px;border:1px solid #cfdee3;border-radius:8px;min-width:220px}
        .btn{display:inline-block;padding:11px 15px;border-radius:8px;background:#087f8c;color:#fff;text-decoration:none;border:0;cursor:pointer;font-weight:700}
        .btn.secondary{background:#e8f5f6;color:#086b75}
        .table{width:100%;border-collapse:collapse;min-width:700px}
        .table th,.table td{text-align:left;padding:13px 10px;border-bottom:1px solid #e3ecef;vertical-align:top}
        .table th{font-size:13px;color:#607d8b}
        .muted{font-size:13px;color:#607d8b}
        .count{display:inline-block;background:#e8f5f6;padding:8px 12px;border-radius:8px;color:#086b75;font-weight:700}
    </style>
</head>
<body>
<header style="background:#fff;border-bottom:1px solid #dce8eb;padding:18px 20px">
    <div style="max-width:1100px;margin:auto;display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <strong>Info Perjalanan · Admin</strong>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            &nbsp;·&nbsp;
            <a href="{{ route('admin.events.index') }}">Manajemen acara</a>
        </nav>
    </div>
</header>

<main class="wrap">
    <p><a href="{{ route('admin.events.index') }}">← Kembali ke manajemen acara</a></p>
    <h1>Daftar peserta</h1>
    <h2>{{ $event->name }}</h2>

    <p><span class="count">{{ number_format($totalParticipants, 0, ',', '.') }} peserta terdaftar</span></p>

    <form class="toolbar" method="GET" action="{{ route('admin.events.participants.index', $event) }}">
        <input type="search" name="q" value="{{ $term }}"
               placeholder="Cari nama, departemen, telepon...">
        <button class="btn" type="submit">Cari</button>
        @if ($term !== '')
            <a class="btn secondary" href="{{ route('admin.events.participants.index', $event) }}">Reset</a>
        @endif
        <a class="btn secondary"
           href="{{ route('admin.events.participants.export', array_merge(['event' => $event->id], request()->only('q'))) }}">
            Ekspor CSV
        </a>
    </form>

    <section class="panel">
        @if ($registrations->isEmpty())
            <p>Tidak ada peserta yang cocok dengan pencarian.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama peserta</th>
                        <th>Departemen</th>
                        <th>Nomor telepon</th>
                        <th>Status</th>
                        <th>Waktu pendaftaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registrations as $registration)
                        <tr>
                            <td>{{ $registrations->firstItem() + $loop->index }}</td>
                            <td><strong>{{ $registration->participant->name }}</strong></td>
                            <td>{{ $registration->participant->department ?: '—' }}</td>
                            <td>{{ $registration->participant->phone }}</td>
                            <td>{{ $registration->status }}</td>
                            <td>{{ $registration->registered_at?->format('d M Y H:i') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:20px">{{ $registrations->links() }}</div>
        @endif
    </section>
</main>
</body>
</html>
