@extends('layouts.app')

@section('title', 'Jelajahi Perjalanan')

@section('content')
    <section class="home-hero" aria-labelledby="home-title">
        <div class="home-hero-shade"></div>
        <div class="home-hero-content">
            <span class="home-eyebrow"><span class="home-eyebrow-dot"></span> RUANG UNTUK PERJALANAN BERMAKNA</span>
            <h1 id="home-title">Temukan perjalanan,<br><em>ciptakan cerita.</em></h1>
            <p>Semua informasi acara, jadwal, dan pengumuman perjalanan dalam satu tempat yang mudah diakses.</p>
            <a class="home-hero-cta" href="#acara">Jelajahi acara <span aria-hidden="true">↘</span></a>
        </div>
        <div class="home-hero-caption">
            <span class="home-caption-line"></span>
            <span>LEBIH DEKAT DENGAN PENGALAMAN BARU</span>
        </div>
        <div class="home-hero-index" aria-hidden="true">01 <span>/</span> 03</div>
    </section>

    <section class="home-intro" id="acara" aria-labelledby="events-title">
        <div class="home-intro-copy">
            <span class="home-section-kicker">MULAI DARI SINI</span>
            <h2 id="events-title">Perjalanan berikutnya<br>dimulai di sini<span>.</span></h2>
            <p>Pilih acara Anda untuk melihat detail, mendaftar, dan mendapatkan informasi perjalanan terbaru.</p>
        </div>
        <div class="home-event-count" aria-label="Jumlah acara tersedia">
            <span class="home-count-number">{{ $events->total() }}</span>
            <span class="home-count-label">ACARA<br>TERSEDIA</span>
        </div>
    </section>

    <section class="home-events" aria-label="Daftar acara tersedia">
        @forelse ($events as $event)
            <article class="home-event-card">
                <div class="home-event-art" aria-hidden="true">
                    <span class="home-event-art-number">{{ str_pad((string) (($events->firstItem() ?? 1) + $loop->index), 2, '0', STR_PAD_LEFT) }}</span>
                    <svg class="home-event-art-mark" viewBox="0 0 120 100" fill="none" aria-hidden="true">
                        <path d="M8 88 43 29l18 29 13-20 38 50H8Z" stroke="currentColor" stroke-width="1.5" />
                        <path d="m32 88 27-39 33 39M43 29l9 15 9-15" stroke="currentColor" stroke-width="1.5" />
                        <circle cx="91" cy="20" r="8" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    <span class="home-art-label">INFO PERJALANAN</span>
                </div>
                <div class="home-event-body">
                    <span class="home-event-status"><span></span> PENDAFTARAN TERSEDIA</span>
                    <h3>{{ $event->name }}</h3>
                    <p class="home-event-description">{{ $event->description ?: 'Buka detail acara untuk melihat informasi lengkap perjalanan ini.' }}</p>

                    <div class="home-event-meta">
                        @if ($event->starts_at)
                            <span class="home-meta-item">
                                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="3.25" y="5" width="13.5" height="12" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M6.5 3.5v3M13.5 3.5v3M3.5 8.5h13" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                                {{ $event->starts_at->translatedFormat('d M Y, H:i') }}
                            </span>
                        @endif
                        @if ($event->location)
                            <span class="home-meta-item">
                                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M16 8.3c0 4.2-6 8.7-6 8.7S4 12.5 4 8.3a6 6 0 1 1 12 0Z" stroke="currentColor" stroke-width="1.4"/><circle cx="10" cy="8" r="2" stroke="currentColor" stroke-width="1.4"/></svg>
                                {{ $event->location }}
                            </span>
                        @endif
                    </div>

                    <a class="home-event-link" href="{{ route('events.show', $event->slug) }}">
                        <span>Lihat detail acara</span><span class="home-link-arrow" aria-hidden="true">↗</span>
                    </a>
                </div>
            </article>
        @empty
            <div class="home-empty-state">
                <span class="home-empty-icon" aria-hidden="true">✳</span>
                <span class="home-section-kicker">AGENDA BARU AKAN HADIR</span>
                <h3>Belum ada acara yang tersedia</h3>
                <p>Acara akan tampil di sini setelah admin menerbitkannya. Silakan kembali lagi nanti.</p>
            </div>
        @endforelse
    </section>

    <div class="home-pagination">{{ $events->links() }}</div>

    <section class="home-bottom-note" aria-label="Informasi layanan">
        <span class="home-bottom-leaf" aria-hidden="true">✳</span>
        <p><strong>Siap untuk perjalanan Anda.</strong><br>Jadwal dan informasi penting selalu mudah ditemukan di satu tempat.</p>
    </section>
@endsection
