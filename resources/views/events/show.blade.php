@extends('layouts.app')

@section('title', $event->name)

@section('content')
    <a class="back-link" href="{{ route('home') }}">← Kembali ke daftar acara</a>

    <section class="event-hero">
        <span class="eyebrow">INFORMASI PERJALANAN</span>
        <h1>{{ $event->name }}</h1>
        @if ($event->location)
            <p class="location">⌖ {{ $event->location }}</p>
        @endif
        @if ($event->starts_at)
            <p class="location">◷ {{ $event->starts_at->translatedFormat('l, d F Y · H:i') }}</p>
        @endif
        @if ($event->description)
            <p class="event-description">{{ $event->description }}</p>
        @endif
    </section>

    @if (! $isRegistered)
        <section class="panel registration-panel">
            <span class="eyebrow">LANGKAH PERTAMA</span>
            <h2>Daftar untuk mengakses informasi</h2>
            <p>Isi data berikut untuk membuka jadwal, pengumuman, dan informasi perjalanan acara ini.</p>

            <form method="POST" action="{{ route('events.register', $event->slug) }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nama lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}"
                        maxlength="150" autocomplete="name" required>
                </div>

                <div class="form-group">
                    <label for="department">Bagian / departemen</label>
                    <input id="department" name="department" type="text"
                        value="{{ old('department') }}" maxlength="150" required>
                </div>

                <div class="form-group">
                    <label for="phone">Nomor telepon</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                        maxlength="30" autocomplete="tel" inputmode="tel" required>
                    <small>Gunakan nomor telepon yang dapat Anda akses.</small>
                </div>

                <button class="button primary full-width" type="submit">
                    Daftar dan lihat informasi →
                </button>
            </form>
        </section>
    @else
        <div class="alert success">
            Anda terdaftar pada acara ini. Informasi perjalanan tersedia di bawah.
        </div>

        <section class="section">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">AGENDA ACARA</span>
                    <h2>Jadwal perjalanan</h2>
                </div>
            </div>

            @forelse ($event->schedules as $schedule)
                <article class="timeline-card">
                    <div class="timeline-dot"></div>
                    <div>
                        @if ($schedule->starts_at)
                            <span class="schedule-time">
                                {{ $schedule->starts_at->translatedFormat('d M Y · H:i') }}
                            </span>
                        @endif
                        <h3>{{ $schedule->title }}</h3>
                        @if ($schedule->location)
                            <p class="muted">⌖ {{ $schedule->location }}</p>
                        @endif
                        @if ($schedule->description)
                            <p>{{ $schedule->description }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-state compact">
                    <h3>Jadwal belum tersedia</h3>
                    <p>Admin akan menambahkan jadwal perjalanan di sini.</p>
                </div>
            @endforelse
        </section>

        <section class="section">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">INFORMASI TERBARU</span>
                    <h2>Pengumuman</h2>
                </div>
            </div>

            @forelse ($event->announcements as $announcement)
                <article class="announcement-card">
                    <span class="schedule-time">
                        {{ $announcement->published_at->translatedFormat('d M Y · H:i') }}
                    </span>
                    <h3>{{ $announcement->title }}</h3>
                    <p class="announcement-body">{{ $announcement->body }}</p>
                </article>
            @empty
                <div class="empty-state compact">
                    <h3>Belum ada pengumuman</h3>
                    <p>Pengumuman terbaru akan tampil di halaman ini.</p>
                </div>
            @endforelse
        </section>
    @endif
@endsection
