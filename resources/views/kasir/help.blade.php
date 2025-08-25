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
  <div class="card bg-base-100 shadow mt-4">
  <div class="card-body">
    <h3 class="card-title">Bantuan Cetak Tiket Offline</h3>
    <ol class="list-decimal ml-5 space-y-1 text-sm">
      <li>Buka menu <b>Kasir → Cetak Tiket (Offline)</b>.</li>
      <li>Masukkan <b>Nomor Pemesanan</b> (contoh: ORD-XXXX).</li>
      <li>Klik <b>Cetak</b> → Halaman tiket terbuka → tekan <b>Ctrl+P</b>.</li>
      <li>Cetakan ini otomatis tercatat sebagai <b>cetak kasir</b> di sistem.</li>
    </ol>
  </div>
</div>
@endsection
