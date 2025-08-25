@extends('layouts.app', ['title'=>'Kasir • Refund/Pindah','page'=>'Refund & Pindah Kursi'])

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any()) <div class="alert alert-error"><ul class="list-disc ml-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div> @endif

<div class="grid lg:grid-cols-2 gap-4">
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Refund</h3>
      <form method="POST" action="{{ route('kasir.refund.process') }}" class="space-y-3">
        @csrf
        <label class="form-control">
          <span class="label-text">Nomor Pemesanan (Ref)</span>
          <input name="reference" class="input input-bordered" placeholder="cth: ORD-XXXX" required>
        </label>
        <label class="form-control">
          <span class="label-text">Nominal Refund (Rp)</span>
          <input type="number" min="1000" name="amount" class="input input-bordered" required>
        </label>
        <label class="form-control">
          <span class="label-text">Alasan (opsional)</span>
          <textarea name="reason" class="textarea textarea-bordered"></textarea>
        </label>
        <button class="btn btn-warning">Proses Refund</button>
      </form>
    </div>
  </div>

  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Pindah Kursi</h3>
      <form method="POST" action="{{ route('kasir.swap.seat') }}" class="space-y-3">
        @csrf
        <label class="form-control">
          <span class="label-text">Nomor Pemesanan (Ref)</span>
          <input name="reference" class="input input-bordered" required>
        </label>
        <div class="grid grid-cols-2 gap-3">
          <label class="form-control">
            <span class="label-text">ID Seat Lama (event_seat_pricings.id)</span>
            <input type="number" name="old_sp_id" class="input input-bordered" required>
          </label>
          <label class="form-control">
            <span class="label-text">ID Seat Baru (harus available)</span>
            <input type="number" name="new_sp_id" class="input input-bordered" required>
          </label>
        </div>
        <button class="btn btn-primary">Pindahkan</button>
      </form>
      <div class="mt-2 text-sm opacity-70">
        Catatan: selisih harga akan dicatat sebagai transaksi masuk/keluar dan total order akan disesuaikan otomatis.
      </div>
    </div>
  </div>
</div>
@endsection
