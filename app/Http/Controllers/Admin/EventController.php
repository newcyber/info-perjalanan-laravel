<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::withCount('registrations')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim($request->string('q')->toString());

                $query->where(function ($builder) use ($term) {
                    $builder->where('name', 'like', "%{$term}%")
                        ->orWhere('location', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.form', [
            'event' => new Event(),
            'formTitle' => 'Tambah acara',
            'formAction' => route('admin.events.store'),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['name']);

        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Acara berhasil dibuat.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.form', [
            'event' => $event,
            'formTitle' => 'Edit acara',
            'formAction' => route('admin.events.update', $event),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validatedData($request, $event);
        $data['slug'] = $this->uniqueSlug(
            $data['slug'] ?: $data['name'],
            $event->id
        );

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Acara berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        if (
            $event->registrations()->exists() ||
            $event->schedules()->exists() ||
            $event->announcements()->exists()
        ) {
            return redirect()->route('admin.events.index')
                ->with('error', 'Acara tidak dapat dihapus karena masih memiliki pendaftaran, jadwal, atau pengumuman. Ubah statusnya menjadi draft jika acara tidak lagi digunakan.');
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Acara berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Event $event = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('events', 'slug')->ignore($event?->id),
            ],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ], [
            'name.required' => 'Nama acara wajib diisi.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'slug.unique' => 'Slug sudah digunakan oleh acara lain.',
            'ends_at.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $data['location'] = $data['location'] ?? null;
        $data['starts_at'] = $data['starts_at'] ?: null;
        $data['ends_at'] = $data['ends_at'] ?: null;
        $data['description'] = $data['description'] ?? null;

        return $data;
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);

        if ($base === '') {
            $base = 'acara';
        }

        $slug = $base;
        $suffix = 2;

        while (
            Event::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
