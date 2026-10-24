<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $formTitle }} — Info Perjalanan</title>
    <style>
        .form-wrap{max-width:760px;margin:35px auto;padding:0 20px}
        .form-panel{background:#fff;border:1px solid #dce8eb;border-radius:14px;padding:24px}
        .field{margin-bottom:20px}
        .field label{display:block;font-weight:700;margin-bottom:8px}
        .field input,.field textarea,.field select{box-sizing:border-box;width:100%;padding:12px;border:1px solid #cfdee3;border-radius:8px;font:inherit}
        .field textarea{min-height:180px;resize:vertical}
        .form-button{border:0;border-radius:8px;padding:12px 16px;background:#087f8c;color:#fff;font-weight:700;cursor:pointer}
        .form-cancel{display:inline-block;margin-left:12px;color:#087f8c}
        .form-errors{background:#fff0f0;color:#a52222;border-radius:9px;padding:12px 18px;margin-bottom:18px}
        .form-muted{font-size:13px;color:#607d8b}
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<header>
    <div class="admin-header-inner"><strong>Info Perjalanan · Admin</strong></div>
</header>

<main class="form-wrap">
    <p>
        <a href="{{ route('admin.events.announcements.index', $event) }}">← Kembali ke pengumuman</a>
    </p>
    <h1>{{ $formTitle }}</h1>
    <p>Acara: <strong>{{ $event->name }}</strong></p>

    @if ($errors->any())
        <div class="form-errors alert error" role="alert">
            <strong>Periksa kembali formulir:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="form-panel" method="POST" action="{{ $formAction }}">
        @csrf
        @if ($formMethod !== 'POST')
            @method($formMethod)
        @endif

        <div class="field">
            <label for="title">Judul pengumuman *</label>
            <input id="title" name="title" required maxlength="255"
                   value="{{ old('title', $announcement->title) }}">
        </div>

        <div class="field">
            <label for="body">Isi pengumuman *</label>
            <textarea id="body" name="body" required maxlength="10000">{{ old('body', $announcement->body) }}</textarea>
        </div>

        <div class="field">
            <label for="publication">Status publikasi *</label>
            <select id="publication" name="publication" required>
                <option value="draft"
                    @selected(old('publication', $announcement->published_at ? 'published' : 'draft') === 'draft')>
                    Draft — tidak ditampilkan ke peserta
                </option>
                <option value="published"
                    @selected(old('publication', $announcement->published_at ? 'published' : 'draft') === 'published')>
                    Terbitkan
                </option>
            </select>
        </div>

        <div class="field">
            <label for="published_at">Waktu publikasi (opsional)</label>
            <input id="published_at" type="datetime-local" name="published_at"
                   value="{{ old('published_at', $announcement->published_at?->format('Y-m-d\TH:i')) }}">
            <p class="form-muted">Kosongkan untuk menerbitkan sekarang jika statusnya Terbitkan. Waktu mendatang tidak akan terlihat sebelum waktunya tiba.</p>
        </div>

        <button class="form-button button" type="submit">Simpan pengumuman</button>
        <a class="form-cancel" href="{{ route('admin.events.announcements.index', $event) }}">Batal</a>
    </form>
</main>
</body>
</html>
