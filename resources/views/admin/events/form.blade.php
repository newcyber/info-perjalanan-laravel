<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $formTitle }} — Info Perjalanan</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f8f8; color: #183344; font-family: Arial, sans-serif; }
        header { padding: 18px max(22px, calc((100% - 820px)/2)); background: #fff; border-bottom: 1px solid #dce9eb; }
        main { max-width: 820px; margin: 30px auto; padding: 0 20px; }
        .panel { padding: 26px; background: #fff; border: 1px solid #dce9eb; border-radius: 14px; }
        label { display: block; margin: 17px 0 7px; font-size: 14px; font-weight: 700; }
        input, textarea, select { width: 100%; padding: 12px; border: 1px solid #cedde1; border-radius: 8px; font: inherit; }
        textarea { min-height: 130px; resize: vertical; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .help { color: #627b89; font-size: 13px; margin-top: 6px; }
        .error { margin-top: 6px; color: #b42318; font-size: 13px; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 25px; }
        .button { display: inline-block; padding: 12px 16px; border: 0; border-radius: 9px; background: #087f8c; color: white; font-weight: 700; cursor: pointer; text-decoration: none; }
        .secondary { background: white; color: #183344; border: 1px solid #dce9eb; }
        @media (max-width: 600px) { .grid { grid-template-columns: 1fr; gap: 0; } .panel { padding: 18px; } }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<header><strong>Info Perjalanan · Admin</strong></header>
<main>
    <p><a href="{{ route('admin.events.index') }}">← Kembali ke manajemen acara</a></p>
    <section class="panel">
        <h1>{{ $formTitle }}</h1>

        @if ($errors->any())
            <div class="alert error" role="alert">
                <strong>Periksa kembali data berikut:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $formAction }}">
            @csrf
            @if ($formMethod !== 'POST')
                @method($formMethod)
            @endif

            <label for="name">Nama acara *</label>
            <input id="name" name="name" value="{{ old('name', $event->name) }}" maxlength="255" required>
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <label for="slug">Slug URL (opsional)</label>
            <input id="slug" name="slug" value="{{ old('slug', $event->slug) }}" placeholder="contoh: perjalanan-kebersamaan">
            <div class="help">Jika dikosongkan, slug dibuat otomatis dari nama acara. Slug yang sudah ada tidak akan dipakai ulang.</div>
            @error('slug') <div class="error">{{ $message }}</div> @enderror

            <label for="location">Lokasi</label>
            <input id="location" name="location" value="{{ old('location', $event->location) }}" maxlength="255">

            <div class="grid">
                <div>
                    <label for="starts_at">Tanggal dan waktu mulai</label>
                    <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}">
                    @error('starts_at') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="ends_at">Tanggal dan waktu selesai</label>
                    <input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}">
                    @error('ends_at') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <label for="description">Deskripsi acara</label>
            <textarea id="description" name="description" maxlength="10000">{{ old('description', $event->description) }}</textarea>
            @error('description') <div class="error">{{ $message }}</div> @enderror

            <label for="status">Status acara *</label>
            <select id="status" name="status" required>
                <option value="draft" @selected(old('status', $event->status ?: 'draft') === 'draft')>Draft — belum tampil ke peserta</option>
                <option value="published" @selected(old('status', $event->status ?: 'draft') === 'published')>Dipublikasikan — tampil ke peserta</option>
            </select>

            <div class="actions">
                <button class="button" type="submit">Simpan acara</button>
                <a class="button secondary" href="{{ route('admin.events.index') }}">Batal</a>
            </div>
        </form>
    </section>
</main>
</body>
</html>
