@extends('layouts.app', ['title'=>'Kasir • Dashboard','page'=>'Kasir'])

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
<div class="grid md:grid-cols-3 gap-4">
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Input Penjualan Offline</h3>
      <form method="POST" action="{{ route('kasir.offline.sale') }}" class="space-y-3">
        @csrf
        <label class="form-control w-full">
          <div class="label"><span class="label-text">Event</span></div>
          <select name="event_id" class="select select-bordered">
            @foreach($events as $e)
              <option value="{{ $e->id }}">{{ $e->title }} ({{ $e->start_at->format('d/m H:i') }})</option>
            @endforeach
          </select>
        </label>
        <label class="form-control w-full">
          <div class="label"><span class="label-text">ID Kursi (pisahkan koma)</span></div>
          <input name="seat_ids[]" class="input input-bordered" placeholder="cth: 1,2,3"
                 oninput="this.setAttribute('name','seat_ids[]')">
          <div class="label"><span class="label-text-alt">Demo cepat: isi 1 kursi, mis. <code>1</code></span></div>
        </label>
        <label class="form-control w-full">
          <div class="label"><span class="label-text">Metode</span></div>
          <select name="method" class="select select-bordered">
            <option value="cash">Cash</option>
            <option value="qris">QRIS</option>
            <option value="transfer">Transfer</option>
          </select>
        </label>
        <button class="btn btn-primary w-full">Simpan</button>
      </form>
    </div>
  </div>

  <div class="md:col-span-2 card bg-base-100 shadow">
    <div class="card-body">
      <div class="flex items-center justify-between">
        <h3 class="card-title">Ringkasan</h3>
        <div class="badge badge-outline">Pendapatan Hari Ini: Rp {{ number_format($todayIncome,0,',','.') }}</div>
      </div>
      <div class="overflow-x-auto mt-2">
        <table class="table table-sm">
          <thead><tr><th>Ref</th><th>Event</th><th>Total</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($orders as $o)
              <tr>
                <td>{{ $o->reference }}</td>
                <td>{{ $o->event->title }}</td>
                <td>Rp {{ number_format($o->total,0,',','.') }}</td>
                <td>{{ $o->status }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
