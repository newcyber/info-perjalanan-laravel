<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->where('status', 'published')
            ->orderBy('starts_at')
            ->paginate(9);

        return view('events.index', compact('events'));
    }

    public function show(Request $request, string $slug)
    {
        $event = Event::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $participantId = $request->session()->get('participant_id');

        $isRegistered = $participantId
            && $event->registrations()
                ->where('participant_id', $participantId)
                ->exists();

        if (! $isRegistered) {
            return view('events.show', [
                'event' => $event,
                'isRegistered' => false,
            ]);
        }

        $event->load([
            'schedules',
            'announcements' => fn ($query) => $query
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('published_at'),
        ]);

        return view('events.show', [
            'event' => $event,
            'isRegistered' => true,
        ]);
    }
}
