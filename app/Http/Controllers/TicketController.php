<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Event;
use App\Models\EventSeatPricing;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketPrint;


class TicketController extends Controller
{
    /**
     * Tambah kursi ke keranjang (session)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'event_id'   => 'required|exists:events,id',
            'seat_ids'   => 'required|array|min:1',
            'seat_ids.*' => 'exists:event_seat_pricings,id',
        ]);

        $cart = [
            'event_id' => (int) $request->event_id,
            'seat_ids' => array_values($request->seat_ids),
        ];

        session()->put('cart', $cart);

        return redirect()->route('checkout');
    }

    /**
     * Halaman ringkasan checkout
     */
    public function checkout()
    {
        $cart = session('cart');
        abort_unless($cart && isset($cart['event_id'], $cart['seat_ids']), 404);

        $seats = EventSeatPricing::with('stadiumSeat')
            ->whereIn('id', $cart['seat_ids'])
            ->where('status', 'available') // hanya yang masih available
            ->get();

        // Jika ada kursi yang sudah tidak available, arahkan balik
        if ($seats->count() !== count($cart['seat_ids'])) {
            return redirect()->route('events.show', $cart['event_id'])
                ->withErrors('Maaf, ada kursi yang sudah diambil. Silakan pilih ulang.');
        }

        $subtotal = $seats->sum('price');
        $fee      = (int) ceil($subtotal * 0.05);
        $total    = $subtotal + $fee;

        return view('checkout.index', compact('seats', 'subtotal', 'fee', 'total'));
    }

    /**
     * Simpan order:
     * - Validasi metode & bukti (non-cash wajib bukti)
     * - Lock kursi -> buat order + items
     * - Set kursi jadi "reserved" (akan jadi "sold" saat kasir ACC)
     * - Status order: waiting_approval (non-cash) / awaiting_cash (cash)
     */
    public function placeOrder(Request $request)
    {
        $cart = session('cart');
        abort_unless($cart && isset($cart['event_id'], $cart['seat_ids']), 404);

        // Validasi input pembayaran
        $request->validate([
            'payment_method' => 'required|in:qris,transfer,va,cash',
            'payment_proof'  => 'nullable|image|max:2048',
        ]);

        $method = $request->string('payment_method')->toString();

        // Untuk selain cash, bukti wajib
        if ($method !== 'cash') {
            $request->validate([
                'payment_proof' => 'required|image|max:2048',
            ]);
        }

        return DB::transaction(function () use ($cart, $request, $method) {
            // Ambil kursi dengan lock & pastikan masih available
            $seats = EventSeatPricing::lockForUpdate()
                ->whereIn('id', $cart['seat_ids'])
                ->where('status', 'available')
                ->get();

            if ($seats->count() !== count($cart['seat_ids'])) {
                return redirect()->route('checkout')
                    ->withErrors('Maaf, ada kursi yang sudah diambil. Silakan pilih ulang.');
            }

            // Hitung biaya
            $subtotal = $seats->sum('price');
            $fee      = (int) ceil($subtotal * 0.05);
            $total    = $subtotal + $fee;

            // Simpan bukti (non-cash)
            $proofPath = null;
            if ($method !== 'cash' && $request->hasFile('payment_proof')) {
                $proofPath = $request->file('payment_proof')->store('payments', 'public');
            }

            // Tentukan status awal order
            $status = $method === 'cash' ? 'awaiting_cash' : 'waiting_approval';

            // Buat order
            $order = Order::create([
                'user_id'       => Auth::id(),
                'event_id'      => (int) $cart['event_id'],
                'channel'       => 'online',
                'status'        => $status,
                'subtotal'      => $subtotal,
                'fee'           => $fee,
                'total'         => $total,
                'payment_method'=> $method,
                'payment_proof' => $proofPath,
                'reference'     => 'ORD-'.Str::upper(Str::random(10)),
            ]);

            // Buat item & ubah kursi -> reserved
            foreach ($seats as $seat) {
                OrderItem::create([
                    'order_id'               => $order->id,
                    'event_seat_pricing_id'  => $seat->id,
                    'price'                  => $seat->price,
                ]);
                $seat->update(['status' => 'reserved']);
            }

            // Bersihkan keranjang
            session()->forget('cart');

            return redirect()->route('checkout.success', $order->reference);
        });
    }

    /**
     * Halaman sukses setelah submit pembayaran
     */
    public function success(string $reference)
    {
        $order = Order::where('reference', $reference)->firstOrFail();
        return view('checkout.success', compact('order'));
    }

    public function print(Request $request, string $reference)
    {
        $order = Order::with(['event','items.eventSeatPricing.stadiumSeat','user'])
            ->where('reference',$reference)->firstOrFail();

        // hanya boleh cetak kalau sudah paid atau awaiting_cash (kasir mau bantu cetak)
        if (!in_array($order->status, ['paid','awaiting_cash'])) {
            abort(403, 'Order belum bisa dicetak.');
        }

        // log cetak
        TicketPrint::create([
            'order_id'   => $order->id,
            'source'     => $request->query('source','online'), // online|cashier
            'printed_by' => Auth::id(),
        ]);

        return view('tickets.print', compact('order'));
    }
}
