<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSeatPricing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashierController extends Controller
{
    public function dashboard()
    {
        $events = Event::orderBy('start_at', 'desc')->get();
        $todayIncome = Transaction::whereDate('created_at', today())->sum('amount');
        $orders = Order::with('event')->orderByDesc('id')->limit(20)->get();
        return view('kasir.dashboard', compact('events', 'todayIncome', 'orders'));
    }

    public function offlineSale(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:event_seat_pricings,id',
            'method' => 'required|in:cash,transfer,qris',
        ]);

        return DB::transaction(function () use ($request) {
            $seats = EventSeatPricing::lockForUpdate()
                ->whereIn('id', $request->seat_ids)
                ->where('status', 'available')
                ->get();

            if ($seats->count() !== count($request->seat_ids)) {
                return back()->withErrors('Kursi tidak tersedia.');
            }

            $subtotal = $seats->sum('price');
            $total = $subtotal;

            /** @var \App\Models\User|null $user */
            $user = $request->user();

            $order = Order::create([
                'user_id'        => null,
                'event_id'       => $request->event_id,
                'channel'        => 'offline',
                'status'         => 'paid',
                'subtotal'       => $subtotal,
                'fee'            => 0,
                'total'          => $total,
                'payment_method' => $request->method,
                'reference'      => 'OFF-' . Str::upper(Str::random(8)),
            ]);

            foreach ($seats as $seat) {
                OrderItem::create([
                    'order_id'               => $order->id,
                    'event_seat_pricing_id'  => $seat->id,
                    'price'                  => $seat->price,
                ]);
                $seat->update(['status' => 'sold']);
            }

            Transaction::create([
                'order_id'    => $order->id,
                'type'        => 'in',
                'amount'      => $total,
                'method'      => $request->method,
                'recorded_by' => $user?->email, // <-- ganti dari Auth::user()
                'paid_at'     => now(),
            ]);

            return redirect()->route('kasir.dashboard')->with('success', 'Transaksi offline berhasil.');
        });
    }

    public function confirmPayment(Order $order)
    {
        $order->update(['status' => 'paid']);
        return back();
    }
}
