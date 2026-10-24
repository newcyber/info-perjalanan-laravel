<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index(Event $event)
    {
        $schedules = $event->schedules()
            ->orderBy('sort_order')
            ->orderBy('starts_at')
            ->paginate(15);

        return view('admin.schedules.index', compact('event', 'schedules'));
    }

    public function create(Event $event)
    {
        return view('admin.schedules.form', [
            'event' => $event,
            'schedule' => new Schedule(),
            'formTitle' => 'Tambah jadwal',
            'formAction' => route('admin.events.schedules.store', $event),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $data = $this->validatedData($request, $event);

        $event->schedules()->create($data);

        return redirect()
            ->route('admin.events.schedules.index', $event)
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Event $event, Schedule $schedule)
    {
        $this->ensureScheduleBelongsToEvent($event, $schedule);

        return view('admin.schedules.form', [
            'event' => $event,
            'schedule' => $schedule,
            'formTitle' => 'Edit jadwal',
            'formAction' => route(
                'admin.events.schedules.update',
                [$event, $schedule]
            ),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(
        Request $request,
        Event $event,
        Schedule $schedule
    ) {
        $this->ensureScheduleBelongsToEvent($event, $schedule);

        $schedule->update($this->validatedData($request, $event, $schedule));

        return redirect()
            ->route('admin.events.schedules.index', $event)
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Event $event, Schedule $schedule)
    {
        $this->ensureScheduleBelongsToEvent($event, $schedule);
        $schedule->delete();

        return redirect()
            ->route('admin.events.schedules.index', $event)
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    private function ensureScheduleBelongsToEvent(
        Event $event,
        Schedule $schedule
    ): void {
        abort_unless(
            (int) $schedule->event_id === (int) $event->id,
            404
        );
    }

    private function validatedData(Request $request, Event $event, ?Schedule $schedule = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'starts_at' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999', Rule::unique('schedules', 'sort_order')->where(fn ($query) => $query->where('event_id', $event->id))->ignore($schedule?->id)],
        ], [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'starts_at.date' => 'Format tanggal dan waktu tidak valid.',
            'sort_order.required' => 'Urutan kegiatan wajib diisi.',
            'sort_order.integer' => 'Urutan kegiatan harus berupa angka bulat.',
            'sort_order.unique' => 'Nomor urut sudah digunakan pada acara ini. Pilih nomor lain.',
            'sort_order.min' => 'Urutan kegiatan tidak boleh negatif.',
        ]);

        $data['description'] = $data['description'] ?: null;
        $data['starts_at'] = $data['starts_at'] ?: null;
        $data['location'] = $data['location'] ?: null;

        return $data;
    }
}
