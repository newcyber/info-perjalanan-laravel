<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Event $event)
    {
        $announcements = $event->announcements()
            ->orderByRaw('CASE WHEN published_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.announcements.index', compact('event', 'announcements'));
    }

    public function create(Event $event)
    {
        return view('admin.announcements.form', [
            'event' => $event,
            'announcement' => new Announcement(),
            'formTitle' => 'Tambah pengumuman',
            'formAction' => route('admin.events.announcements.store', $event),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $data = $this->validatedData($request);
        $this->normalizePublication($data);

        $event->announcements()->create($data);

        return redirect()
            ->route('admin.events.announcements.index', $event)
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Event $event, Announcement $announcement)
    {
        $this->ensureAnnouncementBelongsToEvent($event, $announcement);

        return view('admin.announcements.form', [
            'event' => $event,
            'announcement' => $announcement,
            'formTitle' => 'Edit pengumuman',
            'formAction' => route(
                'admin.events.announcements.update',
                [$event, $announcement]
            ),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(
        Request $request,
        Event $event,
        Announcement $announcement
    ) {
        $this->ensureAnnouncementBelongsToEvent($event, $announcement);

        $data = $this->validatedData($request);
        $this->normalizePublication($data);

        $announcement->update($data);

        return redirect()
            ->route('admin.events.announcements.index', $event)
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Event $event, Announcement $announcement)
    {
        $this->ensureAnnouncementBelongsToEvent($event, $announcement);
        $announcement->delete();

        return redirect()
            ->route('admin.events.announcements.index', $event)
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function ensureAnnouncementBelongsToEvent(
        Event $event,
        Announcement $announcement
    ): void {
        abort_unless(
            (int) $announcement->event_id === (int) $event->id,
            404
        );
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'publication' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'body.required' => 'Isi pengumuman wajib diisi.',
            'publication.required' => 'Pilih status pengumuman.',
            'publication.in' => 'Status pengumuman tidak valid.',
            'published_at.date' => 'Format waktu publikasi tidak valid.',
        ]);
    }

    private function normalizePublication(array &$data): void
    {
        $publication = $data['publication'];
        unset($data['publication']);

        if ($publication === 'draft') {
            $data['published_at'] = null;
            return;
        }

        $data['published_at'] = $data['published_at'] ?: now();
    }
}
