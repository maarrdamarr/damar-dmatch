<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SupportTicket;

class UserController extends Controller
{
    /**
     * Dashboard pengguna:
     * - Pesanan (semua status) milik user
     * - Tiket dimiliki (order sudah PAID)
     * - (opsional) daftar tiket bantuan/refund/swap yang diajukan user
     */
    public function dashboard()
    {
        // Pesanan milik user (termasuk status pending/paid/dll)
        $orders = Order::with('event')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        // Tiket dimiliki: item dari order yang sudah paid
        $tickets = OrderItem::with(['eventSeatPricing.stadiumSeat', 'order.event'])
            ->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id())
                  ->where('status', 'paid');
            })
            ->latest()
            ->paginate(10);

        // (Opsional) Pengajuan ke CS (refund/swap) milik user
        $supports = class_exists(SupportTicket::class)
            ? SupportTicket::where('user_id', Auth::id())->latest()->limit(10)->get()
            : collect();

        return view('user.dashboard', compact('orders', 'tickets', 'supports'));
    }
}
