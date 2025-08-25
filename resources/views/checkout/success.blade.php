@extends('layouts.app', ['title'=>'Sukses','page'=>'Ringkasan Pembayaran'])

@section('content')
<div class="card bg-base-100 shadow">
  <div class="card-body">
    <h3 class="card-title">Pesanan {{ $order->reference }}</h3>
    <ul class="mt-2 space-y-1">
      <li><b>Total:</b> Rp {{ number_format($order->total,0,',','.') }}</li>
      <li><b>Metode:</b> {{ strtoupper($order->payment_method) }}</li>
      <li>
        <b>Status:</b>
        @switch($order->status)
          @case('waiting_approval') <span class="badge badge-warning">Menunggu konfirmasi kasir</span> @break
          @case('awaiting_cash')   <span class="badge badge-neutral">Menunggu bayar cash</span> @break
          @case('paid')            <span class="badge badge-success">Lunas</span> @break
          @default                 <span class="badge">{{ $order->status }}</span>
        @endswitch
      </li>
      @if($order->payment_proof)
        <li>
          <b>Bukti:</b> <a class="link" target="_blank" href="{{ asset('storage/'.$order->payment_proof) }}">Lihat</a>
        </li>
      @endif
    </ul>
  </div>
</div>

<a href="{{ route('events.index') }}" class="btn btn-primary mt-4">Kembali ke Event</a>
@endsection
