<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Participant;
use App\Models\Schedule;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function eventData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Perjalanan Pengujian',
            'slug' => 'perjalanan-pengujian',
            'location' => 'Yogyakarta',
            'starts_at' => '2026-11-15 08:00:00',
            'ends_at' => '2026-11-16 17:00:00',
            'description' => 'Acara untuk pengujian otomatis.',
            'status' => 'draft',
        ], $overrides);
    }

    public function test_admin_can_create_event(): void
    {
        $response = $this->actingAs($this->admin())
            ->post(route('admin.events.store'), $this->eventData());

        $response->assertRedirect(route('admin.events.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'name' => 'Perjalanan Pengujian',
            'slug' => 'perjalanan-pengujian',
            'status' => 'draft',
        ]);
    }

    public function test_admin_can_update_event(): void
    {
        $event = Event::query()->create($this->eventData());

        $response = $this->actingAs($this->admin())
            ->put(route('admin.events.update', $event), $this->eventData([
                'name' => 'Perjalanan Diperbarui',
                'slug' => 'perjalanan-diperbarui',
                'location' => 'Semarang',
                'status' => 'published',
            ]));

        $response->assertRedirect(route('admin.events.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => 'Perjalanan Diperbarui',
            'slug' => 'perjalanan-diperbarui',
            'location' => 'Semarang',
            'status' => 'published',
        ]);
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        Event::query()->create($this->eventData());

        $response = $this->actingAs($this->admin())
            ->from(route('admin.events.create'))
            ->post(route('admin.events.store'), $this->eventData([
                'name' => 'Acara Lain',
            ]));

        $response->assertRedirect(route('admin.events.create'));
        $response->assertSessionHasErrors('slug');

        $this->assertSame(1, Event::query()->count());
    }

    public function test_end_date_before_start_date_is_rejected(): void
    {
        $response = $this->actingAs($this->admin())
            ->from(route('admin.events.create'))
            ->post(route('admin.events.store'), $this->eventData([
                'starts_at' => '2026-11-16 17:00:00',
                'ends_at' => '2026-11-15 08:00:00',
            ]));

        $response->assertRedirect(route('admin.events.create'));
        $response->assertSessionHasErrors('ends_at');

        $this->assertSame(0, Event::query()->count());
    }

    public function test_admin_can_delete_event_without_related_records(): void
    {
        $event = Event::query()->create($this->eventData());

        $response = $this->actingAs($this->admin())
            ->delete(route('admin.events.destroy', $event));

        $response->assertRedirect(route('admin.events.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }

    public function test_event_with_schedule_cannot_be_deleted(): void
    {
        $event = Event::query()->create($this->eventData());

        Schedule::query()->create([
            'event_id' => $event->id,
            'title' => 'Jadwal Pengujian',
            'description' => 'Jadwal yang melindungi acara dari penghapusan.',
            'starts_at' => '2026-11-15 09:00:00',
            'location' => 'Yogyakarta',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->admin())
            ->delete(route('admin.events.destroy', $event));

        $response->assertRedirect(route('admin.events.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
        ]);
    }

    public function test_event_with_announcement_cannot_be_deleted(): void
    {
        $event = Event::query()->create($this->eventData());

        Announcement::query()->create([
            'event_id' => $event->id,
            'title' => 'Pengumuman Pengujian',
            'body' => 'Isi pengumuman.',
            'published_at' => null,
        ]);

        $response = $this->actingAs($this->admin())
            ->delete(route('admin.events.destroy', $event));

        $response->assertRedirect(route('admin.events.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
        ]);
    }

    public function test_event_with_registration_cannot_be_deleted(): void
    {
        $event = Event::query()->create($this->eventData());

        $participant = Participant::query()->create([
            'name' => 'Peserta Pengujian',
            'department' => 'IT',
            'phone' => '081234567890',
        ]);

        EventRegistration::query()->create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        $response = $this->actingAs($this->admin())
            ->delete(route('admin.events.destroy', $event));

        $response->assertRedirect(route('admin.events.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
        ]);
    }
}
