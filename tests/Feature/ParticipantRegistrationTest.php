<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function publishedEvent(): Event
    {
        return Event::query()->create([
            'name' => 'Perjalanan Tes',
            'slug' => 'perjalanan-tes',
            'location' => 'Yogyakarta',
            'starts_at' => '2026-11-15 08:00:00',
            'ends_at' => '2026-11-16 17:00:00',
            'description' => 'Acara untuk pengujian otomatis.',
            'status' => 'published',
        ]);
    }

    public function test_participant_can_register_for_published_event(): void
    {
        $event = $this->publishedEvent();

        $response = $this->post(route('events.register', $event->slug), [
            'name' => 'Peserta Pengujian',
            'department' => 'IT',
            'phone' => '0812-3456-7890',
        ]);

        $response->assertRedirect(route('events.show', $event->slug));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('participants', [
            'name' => 'Peserta Pengujian',
            'department' => 'IT',
            'phone' => '081234567890',
        ]);

        $participant = Participant::query()
            ->where('phone', '081234567890')
            ->firstOrFail();

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'status' => 'registered',
        ]);

        $this->assertSame(
            $participant->id,
            session('participant_id')
        );
    }

    public function test_same_participant_does_not_get_duplicate_registration_for_same_event(): void
    {
        $event = $this->publishedEvent();

        $payload = [
            'name' => 'Peserta Pengujian',
            'department' => 'IT',
            'phone' => '0812-3456-7890',
        ];

        $this->post(route('events.register', $event->slug), $payload)
            ->assertRedirect(route('events.show', $event->slug));

        $this->post(route('events.register', $event->slug), [
            ...$payload,
            'phone' => '081234567890',
        ])->assertRedirect(route('events.show', $event->slug));

        $this->assertSame(1, Participant::query()->count());
        $this->assertSame(1, EventRegistration::query()->count());
    }

    public function test_invalid_registration_input_is_rejected(): void
    {
        $event = $this->publishedEvent();

        $response = $this->from(route('events.show', $event->slug))
            ->post(route('events.register', $event->slug), [
                'name' => '',
                'department' => '',
                'phone' => 'nomor-tidak-valid',
            ]);

        $response->assertRedirect(route('events.show', $event->slug));
        $response->assertSessionHasErrors(['name', 'department', 'phone']);

        $this->assertSame(0, Participant::query()->count());
        $this->assertSame(0, EventRegistration::query()->count());
    }

    public function test_registration_for_unpublished_event_returns_not_found(): void
    {
        $event = Event::query()->create([
            'name' => 'Acara Draft',
            'slug' => 'acara-draft',
            'status' => 'draft',
        ]);

        $response = $this->post(route('events.register', $event->slug), [
            'name' => 'Peserta Pengujian',
            'department' => 'IT',
            'phone' => '081234567890',
        ]);

        $response->assertNotFound();

        $this->assertSame(0, Participant::query()->count());
        $this->assertSame(0, EventRegistration::query()->count());
    }
}
