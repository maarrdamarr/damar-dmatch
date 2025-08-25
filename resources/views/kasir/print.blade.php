@extends('layouts.app', ['title'=>'Kasir • Cetak Tiket','page'=>'Cetak Tiket (Offline)'])

@section('content')
<div class="card bg-base-100 shadow">
  <div class="card-body">
    <h3 class="card-title">Masukkan Nomor Pemesanan</h3>
    <form class="flex gap-2" method="GET" onsubmit="goPrint(event)">
      <input id="ref" class="input input-bordered w-full" placeholder="cth: ORD-ABCDEFGH">
      <button class="btn btn-primary">Cetak</button>
    </form>
    <div class="mt-3 text-sm opacity-70">Tiket akan dibuka di halaman baru dan siap dicetak.</div>
  </div>
</div>

@push('scripts')
<script>
  function goPrint(e){
    e.preventDefault();
    const ref = document.getElementById('ref').value.trim();
    if(!ref){ alert('Isi nomor pemesanan.'); return; }
    const url = `{{ url('/tickets/print') }}/${encodeURIComponent(ref)}?source=cashier`;
    window.open(url,'_blank');
  }
</script>
@endpush
@endsection
