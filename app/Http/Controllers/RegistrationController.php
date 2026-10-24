<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $event = Event::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'department' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+().\-\s]+$/'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'department.required' => 'Departemen wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
        ]);

        $participantId = DB::transaction(function () use ($validated, $event) {
            $phone = preg_replace('/[\s().-]+/', '', $validated['phone']);

            $participant = Participant::query()->firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => trim($validated['name']),
                    'department' => trim($validated['department']),
                ]
            );

            $registration = EventRegistration::query()->firstOrCreate(
                [
                    'event_id' => $event->id,
                    'participant_id' => $participant->id,
                ],
                [
                    'status' => 'registered',
                    'registered_at' => now(),
                ]
            );

            return $participant->id;
        });

        $request->session()->regenerate();
        $request->session()->put('participant_id', $participantId);

        return redirect()
            ->route('events.show', $event->slug)
            ->with('success', 'Pendaftaran berhasil. Informasi perjalanan Anda sudah tersedia.');
    }
}
