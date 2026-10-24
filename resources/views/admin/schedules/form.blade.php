@extends('layouts.app')

@section('title', $formTitle)

@section('content')
<style>
.schedule-form-wrap{max-width:760px;margin:32px auto;padding:0 18px}
.schedule-form-card{background:#fff;border:1px solid #dce8eb;border-radius:14px;padding:24px}
.schedule-field{margin:17px 0}
.schedule-field label{display:block;font-weight:700;margin-bottom:7px}
.schedule-field input,.schedule-field textarea{width:100%;box-sizing:border-box;border:1px solid #cbdde2;border-radius:8px;padding:12px;font:inherit}
.schedule-field textarea{min-height:120px;resize:vertical}
.schedule-submit{background:#087f8c;color:#fff;border:0;border-radius:8px;padding:12px 16px;font-weight:700;cursor:pointer}
.schedule-back{color:#087f8c;text-decoration:none}
.schedule-errors{background:#fff0ef;color:#a12a23;border-radius:8px;padding:12px 16px}
.schedule-hint{font-size:13px;color:#66808b}
</style>

<div class="schedule-form-wrap">
    <a class="schedule-back" href="{{ route('admin.events.schedules.index', $event) }}">← Kembali ke daftar jadwal</a>

    <h1>{{ $formTitle }}</h1>
    <p>Acara: <strong>{{ $event->name }}</strong></p>

    @if ($errors->any())
        <div class="schedule-errors">
            <strong>Periksa kembali formulir:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="schedule-form-card">
        <form method="POST" action="{{ $formAction }}">
            @csrf
            @if ($formMethod !== 'POST')
                @method($formMethod)
            @endif

            <div class="schedule-field">
                <label for="title">Judul kegiatan *</label>
                <input id="title" name="title" type="text" maxlength="255"
                       value="{{ old('title', $schedule->title) }}" required>
            </div>

            <div class="schedule-field">
                <label for="starts_at">Tanggal dan waktu</label>
                <input id="starts_at" name="starts_at" type="datetime-local"
                       value="{{ old('starts_at', $schedule->starts_at?->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="schedule-field">
                <label for="location">Lokasi</label>
                <input id="location" name="location" type="text" maxlength="255"
                       value="{{ old('location', $schedule->location) }}">
            </div>

            <div class="schedule-field">
                <label for="description">Deskripsi kegiatan</label>
                <textarea id="description" name="description" maxlength="10000">{{ old('description', $schedule->description) }}</textarea>
            </div>

            <div class="schedule-field">
                <label for="sort_order">Urutan kegiatan *</label>
                <input id="sort_order" name="sort_order" type="number" min="0" max="999999"
                       value="{{ old('sort_order', $schedule->exists ? $schedule->sort_order : (($event->schedules()->max('sort_order') ?? 0) + 1)) }}"
                       required>
                <p class="schedule-hint">Angka lebih kecil ditampilkan lebih dahulu. Anda dapat menggunakan 1, 2, 3, dan seterusnya.</p>
            </div>

            <button class="schedule-submit" type="submit">Simpan jadwal</button>
            <a class="schedule-back" style="margin-left:12px"
               href="{{ route('admin.events.schedules.index', $event) }}">Batal</a>
        </form>
    </div>
</div>
@endsection
