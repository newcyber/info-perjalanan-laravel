<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class DemoEventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'name' => 'Perjalanan Kebersamaan 2026',
                'slug' => 'perjalanan-kebersamaan-2026',
                'location' => 'Yogyakarta',
                'starts_at' => '2026-11-15 07:00:00',
                'ends_at' => '2026-11-16 18:00:00',
                'description' => 'Portal informasi perjalanan kebersamaan peserta, termasuk jadwal keberangkatan, agenda kegiatan, titik kumpul, dan pengumuman terbaru.',
                'status' => 'published',
            ],
            [
                'name' => 'Kunjungan Kerja dan Studi Lapangan',
                'slug' => 'kunjungan-kerja-studi-lapangan',
                'location' => 'Semarang',
                'starts_at' => '2026-12-05 08:00:00',
                'ends_at' => '2026-12-05 17:00:00',
                'description' => 'Informasi kunjungan kerja dan studi lapangan untuk peserta yang telah mendaftarkan diri.',
                'status' => 'published',
            ],
        ];

        foreach ($events as $data) {
            $event = Event::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            $schedules = $event->slug === 'perjalanan-kebersamaan-2026'
                ? [
                    [
                        'title' => 'Registrasi dan titik kumpul',
                        'description' => 'Peserta berkumpul dan melakukan pengecekan kehadiran.',
                        'starts_at' => '2026-11-15 07:00:00',
                        'location' => 'Titik kumpul utama',
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Keberangkatan',
                        'description' => 'Peserta bersiap dan mengikuti arahan koordinator perjalanan.',
                        'starts_at' => '2026-11-15 08:00:00',
                        'location' => 'Area keberangkatan',
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Agenda kegiatan',
                        'description' => 'Ikuti agenda kegiatan sesuai arahan panitia.',
                        'starts_at' => '2026-11-15 13:00:00',
                        'location' => 'Lokasi kegiatan',
                        'sort_order' => 3,
                    ],
                ]
                : [
                    [
                        'title' => 'Registrasi peserta',
                        'description' => 'Pengecekan kehadiran sebelum kegiatan dimulai.',
                        'starts_at' => '2026-12-05 08:00:00',
                        'location' => 'Lokasi pertemuan',
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Kunjungan dan studi lapangan',
                        'description' => 'Agenda kunjungan bersama peserta.',
                        'starts_at' => '2026-12-05 09:00:00',
                        'location' => 'Lokasi kunjungan',
                        'sort_order' => 2,
                    ],
                ];

            foreach ($schedules as $schedule) {
                Schedule::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'sort_order' => $schedule['sort_order'],
                    ],
                    $schedule
                );
            }

            Announcement::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'title' => 'Informasi awal untuk peserta',
                ],
                [
                    'body' => 'Silakan periksa jadwal perjalanan secara berkala. Informasi titik kumpul dan arahan tambahan akan diperbarui oleh panitia.',
                    'published_at' => now(),
                ]
            );
        }
    }
}
