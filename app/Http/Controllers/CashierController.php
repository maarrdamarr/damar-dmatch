<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSeatPricing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CashierController extends Controller
{
    /**
     * Dashboard kasir:
     * - Form penjualan offline
     * - Ringkasan pendapatan hari ini
     * - Order terbaru
     * - Daftar menunggu ACC
     * - Riwayat transaksi terbaru
     */
    public function dashboard()
    {
        $events = Event::orderBy('start_at', 'desc')->get();

        $todayIncome = Transaction::whereDate('created_at', today())->sum('amount');

        $orders = Order::with('event')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $pending = Order::with(['event', 'user'])
            ->whereIn('status', ['waiting_approval', 'awaiting_cash'])
            ->orderByDesc('id')
            ->paginate(10);

        $recentTx = Transaction::with('order.event')
            ->latest()
            ->limit(10)
            ->get();

        return view('kasir.dashboard', compact(
            'events',
            'todayIncome',
            'orders',
            'pending',
            'recentTx'
        ));
    }

    /**
     * Penjualan offline oleh kasir.
     * - Kursi dikunci & ditandai SOLD
     * - Order status langsung PAID
     * - Transaksi kas dicatat
     */
    public function offlineSale(Request $request)
    {
        $request->validate([
            'event_id'     => 'required|exists:events,id',
            'seat_ids'     => 'required|array|min:1',
            'seat_ids.*'   => 'required|integer|exists:event_seat_pricings,id',
            'method'       => 'required|in:cash,transfer,qris',
        ]);

        return DB::transaction(function () use ($request) {
            // Lock kursi yang dipilih
            $seats = EventSeatPricing::lockForUpdate()
                ->whereIn('id', $request->seat_ids)
                ->where('status', 'available')
                ->get();

            if ($seats->count() !== count($request->seat_ids)) {
                return back()->withErrors('Sebagian kursi tidak tersedia.');
            }

            $subtotal = $seats->sum('price');
            $fee      = 0; // offline tanpa fee platform
            $total    = $subtotal;

            // Buat order offline (tanpa user, langsung paid)
            $order = Order::create([
                'user_id'        => null,
                'event_id'       => (int) $request->event_id,
                'channel'        => 'offline',
                'status'         => 'paid',
                'subtotal'       => $subtotal,
                'fee'            => $fee,
                'total'          => $total,
                'payment_method' => $request->method,
                'reference'      => 'OFF-' . Str::upper(Str::random(8)),
            ]);

            foreach ($seats as $seat) {
                OrderItem::create([
                    'order_id'              => $order->id,
                    'event_seat_pricing_id' => $seat->id,
                    'price'                 => $seat->price,
                ]);
                $seat->update(['status' => 'sold']);
            }

            Transaction::create([
                'order_id'   => $order->id,
                'type'       => 'in',
                'amount'     => $total,
                'method'     => $request->method,
                'recorded_by'=> Auth::user()->email,
                'paid_at'    => now(),
            ]);

            return redirect()
                ->route('kasir.dashboard')
                ->with('success', 'Transaksi offline berhasil disimpan.');
        });
    }

    /**
     * ACC pembayaran order online (non-cash / awaiting_cash).
     * - Ubah kursi RESERVED -> SOLD
     * - Tandai order PAID + verified_by/at
     * - Catat transaksi kas
     */
    public function confirmPayment(Order $order)
    {
        if ($order->status === 'paid') {
            return back()->with('success', 'Order sudah lunas.');
        }

        DB::transaction(function () use ($order) {
            // Pastikan item dimuat
            $order->loadMissing('items.eventSeatPricing');

            foreach ($order->items as $item) {
                $esp = $item->eventSeatPricing()->lockForUpdate()->first();
                if ($esp && $esp->status !== 'sold') {
                    $esp->update(['status' => 'sold']);
                }
            }

            $order->update([
                'status'      => 'paid',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);

            Transaction::create([
                'order_id'   => $order->id,
                'type'       => 'in',
                'amount'     => $order->total,
                'method'     => $order->payment_method,
                'recorded_by'=> Auth::user()->email,
                'paid_at'    => now(),
            ]);
        });

        return back()->with('success', 'Pembayaran di-ACC & dicatat.');
    }

    /**
     * Halaman Riwayat kasir + filter.
     */
    public function history(Request $request)
    {
        $q      = (string) $request->query('q', '');
        $status = (string) $request->query('status', '');
        $method = (string) $request->query('method', '');
        $from   = $request->query('from');
        $to     = $request->query('to');

        $query = Order::with(['event', 'user'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('reference', 'like', "%$q%")
                      ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%$q%")
                                                       ->orWhere('name', 'like', "%$q%"))
                      ->orWhereHas('event', fn($e) => $e->where('title', 'like', "%$q%"));
                });
            })
            ->when($status !== '', fn($qq) => $qq->where('status', $status))
            ->when($method !== '', fn($qq) => $qq->where('payment_method', $method))
            ->when($from, fn($qq) => $qq->whereDate('created_at', '>=', $from))
            ->when($to, fn($qq) => $qq->whereDate('created_at', '<=', $to))
            ->orderByDesc('id');

        $orders = $query->paginate(20)->withQueryString();
        $sum    = (clone $query)->selectRaw('SUM(total) as t')->value('t') ?? 0;

        return view('kasir.history', compact('orders', 'sum'));
    }

    /**
     * Export CSV dari filter Riwayat.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $q      = (string) $request->query('q', '');
        $status = (string) $request->query('status', '');
        $method = (string) $request->query('method', '');
        $from   = $request->query('from');
        $to     = $request->query('to');

        $query = Order::with(['event', 'user'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('reference', 'like', "%$q%")
                      ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%$q%")
                                                       ->orWhere('name', 'like', "%$q%"))
                      ->orWhereHas('event', fn($e) => $e->where('title', 'like', "%$q%"));
                });
            })
            ->when($status !== '', fn($qq) => $qq->where('status', $status))
            ->when($method !== '', fn($qq) => $qq->where('payment_method', $method))
            ->when($from, fn($qq) => $qq->whereDate('created_at', '>=', $from))
            ->when($to, fn($qq) => $qq->whereDate('created_at', '<=', $to))
            ->orderByDesc('id');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Ref', 'Pembeli', 'Event', 'Metode', 'Status', 'Total', 'Dibuat']);

            $query->chunk(100, function ($rows) use ($out) {
                foreach ($rows as $o) {
                    fputcsv($out, [
                        $o->reference,
                        $o->user->email ?? '-',
                        $o->event->title ?? '-',
                        strtoupper($o->payment_method),
                        $o->status,
                        $o->total,
                        $o->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, 'riwayat_kasir.csv', ['Content-Type' => 'text/csv']);
    }
}
