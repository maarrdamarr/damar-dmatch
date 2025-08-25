@extends('layouts.app', ['title'=>'Beranda • DMATCH','page'=>null])

@section('content')
<section class="hero min-h-[60vh] bg-base-100 rounded-box shadow">
  <div class="hero-content text-center">
    <div class="max-w-2xl">
      <h1 class="text-4xl font-extrabold">Beli Tiket Stadion Lebih Cepat</h1>
      <p class="py-4 opacity-80">Pilih kursi favorit, bayar instan, dan siap sorak untuk tim kesayangan.</p>
      <a href="{{ route('events.index') }}" class="btn btn-primary btn-lg">Lihat Event</a>
    </div>
  </div>
</section>

<section class="mt-8 grid md:grid-cols-3 gap-4">
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Pilih Kursi</h3>
      <p>Tampilan kursi jelas dengan status tersedia/terjual.</p>
    </div>
  </div>
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Checkout Cepat</h3>
      <p>Ringkasan biaya transparan, fee platform otomatis.</p>
    </div>
  </div>
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Pantau Transaksi</h3>
      <p>Admin & Kasir punya dashboard masing-masing.</p>
    </div>
  </div>
</section>
@endsection
