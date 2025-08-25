@extends('layouts.app', ['title'=>'Admin • Dashboard','page'=>'Dashboard'])

@section('content')
<div class="stats stats-vertical md:stats-horizontal shadow w-full">
  <div class="stat"><div class="stat-title">Total Income</div><div class="stat-value">Rp {{ number_format($totalIncome,0,',','.') }}</div></div>
  <div class="stat"><div class="stat-title">Hari Ini</div><div class="stat-value">Rp {{ number_format($todayIncome,0,',','.') }}</div></div>
  <div class="stat"><div class="stat-title">Orders</div><div class="stat-value">{{ $ordersCount }}</div></div>
  <div class="stat"><div class="stat-title">Users</div><div class="stat-value">{{ $usersCount }}</div></div>
</div>

<div class="card bg-base-100 shadow mt-6">
  <div class="card-body p-0">
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Ref</th><th>Event</th><th>Kanal</th><th>Status</th><th>Total</th><th>Waktu</th></tr></thead>
        <tbody>
          @foreach($recentOrders as $o)
            <tr>
              <td>{{ $o->reference }}</td>
              <td>{{ $o->event->title }}</td>
              <td>{{ strtoupper($o->channel) }}</td>
              <td><span class="badge {{ $o->status=='paid'?'badge-success':'badge-ghost' }}">{{ $o->status }}</span></td>
              <td>Rp {{ number_format($o->total,0,',','.') }}</td>
              <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
