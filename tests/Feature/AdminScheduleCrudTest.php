<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScheduleCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function event(string $slug): Event
    {
        return Event::query()->create([
            'name' => 'Acara ' . $slug,
            'slug' => $slug,
            'location' => 'Yogyakarta',
            'status' => 'published',
        ]);
    }

    private function scheduleData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Registrasi peserta',
            'description' => 'Peserta melakukan registrasi.',
            'starts_at' => '2026-11-15 08:00:00',
            'location' => 'Titik kumpul',
            'sort_order' => 1,
        ], $overrides);
    }

    public function test_admin_can_create_schedule(): void
    {
        $event = $this->event('acara-jadwal-baru');

        $response = $this->actingAs($this->admin())
            ->post(
                route('admin.events.schedules.store', $event),
                $this->scheduleData()
            );

        $response->assertRedirect(
            route('admin.events.schedules.index', $event)
        );
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('schedules', [
            'event_id' => $event->id,
            'title' => 'Registrasi peserta',
            'sort_order' => 1,
        ]);
    }

    public function test_admin_can_update_schedule(): void
    {
        $event = $this->event('acara-jadwal-edit');
        $schedule = $event->schedules()->create($this->scheduleData());

        $response = $this->actingAs($this->admin())
            ->put(
                route('admin.events.schedules.update', [$event, $schedule]),
                $this->scheduleData([
                    'title' => 'Keberangkatan peserta',
                    'sort_order' => 2,
                ])
            );

        $response->assertRedirect(
            route('admin.events.schedules.index', $event)
        );
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'title' => 'Keberangkatan peserta',
            'sort_order' => 2,
        ]);
    }

    public function test_duplicate_sort_order_is_rejected_within_same_event(): void
    {
        $event = $this->event('acara-urutan-sama');
        $event->schedules()->create($this->scheduleData());

        $response = $this->actingAs($this->admin())
            ->from(route('admin.events.schedules.create', $event))
            ->post(
                route('admin.events.schedules.store', $event),
                $this->scheduleData([
                    'title' => 'Agenda kedua',
                    'sort_order' => 1,
                ])
            );

        $response->assertRedirect(
            route('admin.events.schedules.create', $event)
        );
        $response->assertSessionHasErrors('sort_order');

        $this->assertSame(1, Schedule::query()->count());
    }

    public function test_same_sort_order_is_allowed_for_different_events(): void
    {
        $eventA = $this->event('acara-satu');
        $eventB = $this->event('acara-dua');

        $eventA->schedules()->create($this->scheduleData());

        $response = $this->actingAs($this->admin())
            ->post(
                route('admin.events.schedules.store', $eventB),
                $this->scheduleData([
                    'title' => 'Agenda acara kedua',
                    'sort_order' => 1,
                ])
            );

        $response->assertRedirect(
            route('admin.events.schedules.index', $eventB)
        );

        $this->assertSame(2, Schedule::query()->count());
        $this->assertDatabaseHas('schedules', [
            'event_id' => $eventB->id,
            'sort_order' => 1,
        ]);
    }

    public function test_invalid_schedule_input_is_rejected(): void
    {
        $event = $this->event('acara-validasi');

        $response = $this->from(
            route('admin.events.schedules.create', $event)
        )->actingAs($this->admin())
            ->post(
                route('admin.events.schedules.store', $event),
                $this->scheduleData([
                    'title' => '',
                    'sort_order' => -1,
                ])
            );

        $response->assertRedirect(
            route('admin.events.schedules.create', $event)
        );
        $response->assertSessionHasErrors(['title', 'sort_order']);

        $this->assertSame(0, Schedule::query()->count());
    }

    public function test_schedule_from_another_event_cannot_be_updated(): void
    {
        $eventA = $this->event('acara-pemilik-url');
        $eventB = $this->event('acara-pemilik-jadwal');

        $schedule = $eventB->schedules()->create($this->scheduleData());

        $response = $this->actingAs($this->admin())
            ->put(
                route('admin.events.schedules.update', [$eventA, $schedule]),
                $this->scheduleData([
                    'title' => 'Perubahan tidak sah',
                    'sort_order' => 2,
                ])
            );

        $response->assertNotFound();

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'event_id' => $eventB->id,
            'title' => 'Registrasi peserta',
            'sort_order' => 1,
        ]);
    }

    public function test_schedule_from_another_event_cannot_be_deleted(): void
    {
        $eventA = $this->event('acara-hapus-url');
        $eventB = $this->event('acara-hapus-jadwal');

        $schedule = $eventB->schedules()->create($this->scheduleData());

        $response = $this->actingAs($this->admin())
            ->delete(
                route('admin.events.schedules.destroy', [$eventA, $schedule])
            );

        $response->assertNotFound();

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'event_id' => $eventB->id,
        ]);
    }
}
