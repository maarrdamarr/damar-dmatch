<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\StadiumSeat;
use App\Models\EventSeatPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * =======================
     *  PUBLIC PAGES
     * =======================
     */

    // Daftar event publik
    public function index()
    {
        $events = Event::where('status', 'published')
            ->orderBy('start_at')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    // Detail event + daftar kursi
    public function show(Event $event)
    {
        $event->load(['seatPricings.stadiumSeat']);
        return view('events.show', compact('event'));
    }

    /**
     * =======================
     *  ADMIN PAGES
     * =======================
     */

    // Form create (Kelola Event)
    public function create()
    {
        return view('admin.events.create');
    }

    // Simpan event baru + (opsional) generate kursi
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'start_at'      => 'required|date',
            'end_at'        => 'nullable|date|after_or_equal:start_at',
            'venue'         => 'nullable|string|max:255',
            'status'        => 'nullable|in:draft,published,finished,cancelled',

            // generator kursi (opsional)
            'sections'      => 'nullable|string',          // contoh: "A,B"
            'rows'          => 'nullable|integer|min:1',
            'seats_per_row' => 'nullable|integer|min:1',
            'base_price'    => 'nullable|integer|min:0',
        ]);

        $event = Event::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'start_at'    => $data['start_at'],
            'end_at'      => $data['end_at'] ?? null,
            'venue'       => $data['venue'] ?? 'Main Stadium',
            'capacity'    => 0,
            'status'      => $data['status'] ?? 'published',
        ]);

        // Generate kursi jika parameter diisi
        if (!empty($data['sections']) && !empty($data['rows']) && !empty($data['seats_per_row'])) {
            $sections = array_filter(array_map('trim', explode(',', $data['sections'])));
            $rows = (int) $data['rows'];
            $seatsPerRow = (int) $data['seats_per_row'];
            $base = (int) ($data['base_price'] ?? 75000);

            $added = $this->generateSeatsForEvent($event, $sections, $rows, $seatsPerRow, $base);

            return redirect()
                ->route('admin.dashboard')
                ->with('success', "Event dibuat & kursi digenerate (+$added kursi).");
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Event dibuat.');
    }

    // Form edit event
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    // Update event + (opsional) tambah kursi baru
    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'start_at'      => 'required|date',
            'end_at'        => 'nullable|date|after_or_equal:start_at',
            'venue'         => 'nullable|string|max:255',
            'status'        => 'nullable|in:draft,published,finished,cancelled',

            // tambahan kursi (opsional, akan menambahkan yg belum ada)
            'sections'      => 'nullable|string',          // "A,B"
            'rows'          => 'nullable|integer|min:1',
            'seats_per_row' => 'nullable|integer|min:1',
            'base_price'    => 'nullable|integer|min:0',
        ]);

        $event->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'start_at'    => $data['start_at'],
            'end_at'      => $data['end_at'] ?? null,
            'venue'       => $data['venue'] ?? $event->venue,
            'status'      => $data['status'] ?? $event->status,
        ]);

        $added = 0;
        if (!empty($data['sections']) && !empty($data['rows']) && !empty($data['seats_per_row'])) {
            $sections = array_filter(array_map('trim', explode(',', $data['sections'])));
            $rows = (int) $data['rows'];
            $seatsPerRow = (int) $data['seats_per_row'];
            $base = (int) ($data['base_price'] ?? 75000);

            $added = $this->generateSeatsForEvent($event, $sections, $rows, $seatsPerRow, $base);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', $added > 0 ? "Event diperbarui (+$added kursi baru)." : 'Event diperbarui.');
    }

    // Hapus event (cascading akan ikut hapus pricing & item sesuai FK)
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Event dihapus.');
        // Catatan: Jika ada order aktif, pertimbangkan business rule sebelum menghapus.
    }

    /**
     * Generate kursi untuk sebuah event.
     * Menambahkan hanya kombinasi seat yang belum ada (idempotent).
     * Mengembalikan jumlah kursi (pricing) yang ditambahkan.
     */
    private function generateSeatsForEvent(Event $event, array $sections, int $rows, int $seatsPerRow, int $basePrice): int
    {
        $added = 0;

        DB::transaction(function () use ($event, $sections, $rows, $seatsPerRow, $basePrice, &$added) {
            foreach ($sections as $sec) {
                for ($r = 1; $r <= $rows; $r++) {
                    for ($s = 1; $s <= $seatsPerRow; $s++) {
                        $seat = StadiumSeat::firstOrCreate([
                            'section'     => $sec,
                            'row_label'   => (string) $r,
                            'seat_number' => (string) $s,
                        ]);

                        $esp = EventSeatPricing::firstOrCreate(
                            [
                                'event_id'         => $event->id,
                                'stadium_seat_id'  => $seat->id,
                            ],
                            [
                                'price'  => $basePrice + ($r * 1000), // contoh pricing bertingkat
                                'status' => 'available',
                            ]
                        );

                        if ($esp->wasRecentlyCreated) {
                            $added++;
                        }
                    }
                }
            }

            // update kapasitas sesuai total pricing yang ada
            $capacity = EventSeatPricing::where('event_id', $event->id)->count();
            $event->update(['capacity' => $capacity]);
        });

        return $added;
    }
}
