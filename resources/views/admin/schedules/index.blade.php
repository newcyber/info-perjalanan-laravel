@extends('layouts.app')

@section('title', 'Manajemen Jadwal')

@section('content')
<style>
.schedule-wrap{max-width:1000px;margin:36px auto;padding:0 18px}
.schedule-panel{background:#fff;border:1px solid #dce8eb;border-radius:14px;padding:20px;overflow-x:auto}
.schedule-actions{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin:20px 0}
.schedule-button{display:inline-block;background:#087f8c;color:white;padding:10px 14px;border-radius:8px;text-decoration:none;border:0;cursor:pointer;font-weight:700}
.schedule-link{color:#087f8c;text-decoration:none}
.schedule-table{width:100%;border-collapse:collapse;min-width:650px}
.schedule-table th,.schedule-table td{text-align:left;padding:13px 10px;border-bottom:1px solid #e1eaed;vertical-align:top}
.schedule-table th{color:#58717b;font-size:13px}
.schedule-alert{padding:12px 14px;border-radius:8px;margin:14px 0;background:#e9f8ef;color:#17683a}
.schedule-alert.error{background:#fff0ef;color:#a12a23}
.schedule-muted{color:#66808b;font-size:13px}
.schedule-delete{background:none;border:0;color:#bf2828;cursor:pointer;padding:0;font:inherit}
</style>

<div class="schedule-wrap">
    <a class="schedule-link" href="{{ route('admin.events.index') }}">← Kembali ke manajemen acara</a>

    <h1>Manajemen jadwal</h1>
    <h2>{{ $event->name }}</h2>
    <p class="schedule-muted">Atur agenda perjalanan untuk acara ini saja.</p>

    @if (session('success'))
        <div class="schedule-alert">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="schedule-alert error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="schedule-alert error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="schedule-actions">
        <a class="schedule-button"
           href="{{ route('admin.events.schedules.create', $event) }}">+ Tambah jadwal</a>
        <a class="schedule-link"
           href="{{ route('admin.events.edit', $event) }}">Edit informasi acara</a>
        <a href="{{ route('admin.events.announcements.index', $event) }}">Kelola pengumuman</a>
    </div>

    <div class="schedule-panel">
        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Urutan</th>
                    <th>Kegiatan</th>
                    <th>Waktu</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->sort_order }}</td>
                        <td>
                            <strong>{{ $schedule->title }}</strong>
                            @if ($schedule->description)
                                <p class="schedule-muted">{{ $schedule->description }}</p>
                            @endif
                        </td>
                        <td>
                            {{ $schedule->starts_at?->format('d/m/Y H:i') ?? 'Belum ditentukan' }}
                        </td>
                        <td>{{ $schedule->location ?: '—' }}</td>
                        <td>
                            <a class="schedule-link"
                               href="{{ route('admin.events.schedules.edit', [$event, $schedule]) }}">Edit</a>
                            <span class="schedule-muted"> | </span>
                            <form method="POST"
                                  action="{{ route('admin.events.schedules.destroy', [$event, $schedule]) }}"
                                  style="display:inline"
                                  onsubmit="return confirm('Hapus jadwal ini? Tindakan ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button class="schedule-delete" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada jadwal untuk acara ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px">
            {{ $schedules->links() }}
        </div>
    </div>
</div>
@endsection
