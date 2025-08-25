<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;

class AdminController extends Controller
{
        public function dashboard() {
        $totalIncome = Transaction::sum('amount');
        $todayIncome = Transaction::whereDate('created_at', today())->sum('amount');
        $ordersCount = Order::count();
        $usersCount  = User::count();
        $recentOrders = Order::with('event')->latest()->limit(10)->get();
        return view('admin.dashboard', compact('totalIncome','todayIncome','ordersCount','usersCount','recentOrders'));
    }

    public function transactions() {
        $tx = Transaction::with('order')->latest()->paginate(20);
        return view('admin.transactions', compact('tx'));
    }

    public function users() {
        $users = User::orderBy('id','desc')->paginate(20);
        return view('admin.users', compact('users'));
    }
}
