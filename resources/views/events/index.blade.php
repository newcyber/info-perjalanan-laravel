@extends('layouts.app')

@section('title', 'Daftar Perjalanan')

@section('content')
    <section class="hero">
        <span class="eyebrow">PORTAL INFORMASI PESERTA</span>
        <h1>Perjalanan Anda,<br>lebih terorganisir.</h1>
        <p>Temukan acara, daftar sebagai peserta, dan akses jadwal serta pengumuman perjalanan Anda.</p>
    </section>

    <section class="section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">AGENDA</span>
                <h2>Acara tersedia</h2>
            </div>
            <span class="count">{{ $events->total() }} acara</span>
        </div>

        @forelse ($events as $event)
            <article class="event-card">
                <div class="event-icon">✦</div>
                <div class="event-content">
                    <span class="status">Pendaftaran tersedia</span>
                    <h3>{{ $event->name }}</h3>
                    <p>{{ $event->description ?: 'Informasi kegiatan tersedia setelah Anda membuka detail acara.' }}</p>

                    <div class="event-meta">
                        @if ($event->starts_at)
                            <span>◷ {{ $event->starts_at->translatedFormat('d M Y, H:i') }}</span>
                        @endif
                        @if ($event->location)
                            <span>⌖ {{ $event->location }}</span>
                        @endif
                    </div>

                    <a class="button primary" href="{{ route('events.show', $event->slug) }}">
                        Lihat acara <span aria-hidden="true">→</span>
                    </a>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <div class="empty-icon">✦</div>
                <h3>Belum ada acara yang tersedia</h3>
                <p>Acara akan muncul di sini setelah admin menerbitkannya.</p>
            </div>
        @endforelse

        <div class="pagination">{{ $events->links() }}</div>
    </section>
@endsection
