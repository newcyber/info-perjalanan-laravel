<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $term = trim($request->query('q', ''));

        $registrations = $event->registrations()
            ->with('participant')
            ->when($term !== '', function ($query) use ($term) {
                $query->whereHas('participant', function ($participantQuery) use ($term) {
                    $participantQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('department', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('registered_at')
            ->paginate(15)
            ->withQueryString();

        $totalParticipants = $event->registrations()->count();

        return view('admin.participants.index', compact(
            'event',
            'registrations',
            'totalParticipants',
            'term'
        ));
    }

    public function export(Request $request, Event $event): StreamedResponse
    {
        $term = trim($request->query('q', ''));

        $registrations = $event->registrations()
            ->with('participant')
            ->when($term !== '', function ($query) use ($term) {
                $query->whereHas('participant', function ($participantQuery) use ($term) {
                    $participantQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('department', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->orderBy('registered_at')
            ->get();

        $filename = 'peserta-acara-' . $event->id . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($registrations) {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                throw new \RuntimeException('Gagal membuka output CSV.');
            }

            // BOM UTF-8 membantu Excel membaca karakter Bahasa Indonesia.
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'No',
                'Nama',
                'Departemen',
                'Nomor Telepon',
                'Status Pendaftaran',
                'Waktu Pendaftaran',
            ], ';', '"', '');

            // Lindungi CSV dari formula spreadsheet yang tidak tepercaya.
            $safeCell = static function ($value): string {
                $value = (string) ($value ?? '');

                if (preg_match('/^[\x00-\x20]*[=+\-@]/', $value)) {
                    return "'" . $value;
                }

                return $value;
            };

            foreach ($registrations as $index => $registration) {
                fputcsv($output, [
                    $index + 1,
                    $safeCell($registration->participant->name),
                    $safeCell($registration->participant->department),
                    "\t" . $safeCell($registration->participant->phone),
                    $safeCell($registration->status),
                    $registration->registered_at?->format('Y-m-d H:i:s') ?? '',
                ], ';', '"', '');
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
