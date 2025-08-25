@extends('layouts.app', ['title'=>'Kasir • Bantuan','page'=>'Lain-lain (Bantuan Kasir)'])

@section('content')
<div class="grid lg:grid-cols-2 gap-4">
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Tips Operasional Cepat</h3>
      <ul class="list-disc ml-5 space-y-1">
        <li>Menu <b>Riwayat</b> untuk melihat transaksi & export CSV.</li>
        <li>ACC hanya untuk status <b>waiting_approval</b> atau <b>awaiting_cash</b>.</li>
        <li>Gunakan kolom ID kursi sesuai tabel <code>event_seat_pricings</code> untuk penjualan offline.</li>
      </ul>
    </div>
  </div>
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Status & Alur</h3>
      <p class="opacity-80">online non-cash → <code>waiting_approval</code> → ACC → <code>paid</code> (kursi sold).</p>
      <p class="opacity-80">online cash → <code>awaiting_cash</code> → ACC setelah terima uang → <code>paid</code>.</p>
    </div>
  </div>
</div>
@endsection
