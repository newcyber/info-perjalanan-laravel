<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Participant;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'eventCount' => Event::count(),
            'publishedEventCount' => Event::where('status', 'published')->count(),
            'participantCount' => Participant::count(),
            'registrationCount' => EventRegistration::count(),
            'recentEvents' => Event::withCount('registrations')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
