<?php

namespace App\Http\Controllers;

use App\Models\EventSeatPricing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:event_seat_pricings,id',
        ]);

        $cart = [
            'event_id' => $request->event_id,
            'seat_ids' => $request->seat_ids,
        ];
        session()->put('cart', $cart);

        return redirect()->route('checkout');
    }

    public function checkout()
    {
        $cart = session('cart');
        abort_unless($cart && isset($cart['event_id'], $cart['seat_ids']), 404);

        $seats = EventSeatPricing::with('stadiumSeat')
            ->whereIn('id', $cart['seat_ids'])
            ->where('status', 'available')
            ->get();

        $subtotal = $seats->sum('price');
        $fee = (int) ceil($subtotal * 0.05);
        $total = $subtotal + $fee;

        return view('checkout.index', compact('seats', 'subtotal', 'fee', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $cart = session('cart');
        abort_unless($cart && isset($cart['event_id'], $cart['seat_ids']), 404);

        return DB::transaction(function () use ($cart, $request) {
            $seats = EventSeatPricing::lockForUpdate()
                ->whereIn('id', $cart['seat_ids'])
                ->where('status', 'available')
                ->get();

            if ($seats->count() !== count($cart['seat_ids'])) {
                return redirect()->route('checkout')->withErrors('Maaf, ada kursi yang sudah diambil.');
            }

            $subtotal = $seats->sum('price');
            $fee = (int) ceil($subtotal * 0.05);
            $total = $subtotal + $fee;

            /** @var \App\Models\User|null $user */
            $user = $request->user();

            $order = Order::create([
                'user_id'        => $user?->id,
                'event_id'       => $cart['event_id'],
                'channel'        => 'online',
                'status'         => 'paid', // demo
                'subtotal'       => $subtotal,
                'fee'            => $fee,
                'total'          => $total,
                'payment_method' => 'qris',
                'reference'      => 'ORD-' . Str::upper(Str::random(10)),
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
                'method'      => 'qris',
                'recorded_by' => $user?->email, // <-- tidak lagi pakai Auth::user()
                'paid_at'     => now(),
            ]);

            session()->forget('cart');

            return redirect()->route('checkout.success', $order->reference);
        });
    }

    public function success(string $reference)
    {
        $order = Order::where('reference', $reference)->firstOrFail();
        return view('checkout.success', compact('order'));
    }
}
