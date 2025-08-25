@extends('layouts.app', ['title'=>'Checkout','page'=>'Checkout'])

@section('content')
<div class="card bg-base-100 shadow">
  <div class="card-body p-0">
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Kursi</th><th>Harga</th></tr></thead>
        <tbody>
          @foreach($seats as $sp)
            <tr>
              <td>{{ $sp->stadiumSeat->section }}-{{ $sp->stadiumSeat->row_label }}-{{ $sp->stadiumSeat->seat_number }}</td>
              <td>Rp {{ number_format($sp->price,0,',','.') }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr><th>Subtotal</th><th>Rp {{ number_format($subtotal,0,',','.') }}</th></tr>
          <tr><th>Biaya Platform (5%)</th><th>Rp {{ number_format($fee,0,',','.') }}</th></tr>
          <tr><th>Total Dibayar</th><th>Rp {{ number_format($total,0,',','.') }}</th></tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<form method="POST" action="{{ route('checkout.place') }}" class="mt-4 space-y-4" enctype="multipart/form-data">
  @csrf
  <div class="card bg-base-100 shadow">
    <div class="card-body grid md:grid-cols-2 gap-4">
      <label class="form-control">
        <span class="label-text">Metode Pembayaran</span>
        <select name="payment_method" id="payMethod" class="select select-bordered" required>
          <option value="qris">QRIS</option>
          <option value="transfer">Transfer</option>
          <option value="va">Virtual Account</option>
          <option value="cash">Cash (bayar ke kasir)</option>
        </select>
        <span class="label-text-alt">Untuk selain cash, unggah bukti pembayaran.</span>
      </label>

      <label class="form-control" id="proofWrap">
        <span class="label-text">Bukti Pembayaran (jpg/png, maks 2MB)</span>
        <input type="file" name="payment_proof" class="file-input file-input-bordered" accept="image/*">
      </label>

      <div class="alert">
        <div>
          <b>Status:</b>
          <span id="statusPreview">Menunggu konfirmasi kasir</span>
        </div>
      </div>
    </div>
  </div>

  <div class="flex items-center justify-between">
    <a href="{{ route('events.index') }}" class="btn btn-ghost">Kembali</a>
    <button class="btn btn-primary">Kirim Pembayaran</button>
  </div>
</form>

@push('scripts')
<script>
  const m = document.getElementById('payMethod');
  const wrap = document.getElementById('proofWrap');
  const st = document.getElementById('statusPreview');
  function sync() {
    const isCash = m.value === 'cash';
    wrap.style.display = isCash ? 'none' : 'block';
    st.textContent = isCash ? 'Menunggu pembayaran cash di kasir' : 'Menunggu konfirmasi kasir';
  }
  m.addEventListener('change', sync); sync();
</script>
@endpush
@endsection
