@extends('layouts.app', ['title'=>'Kasir • Dashboard','page'=>'Kasir'])

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any()) <div class="alert alert-error"><ul class="list-disc ml-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div> @endif

<div class="grid md:grid-cols-3 gap-4">
  {{-- FORM PENJUALAN OFFLINE (INLINE, TANPA PARTIAL) --}}
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Input Penjualan Offline</h3>
      <form id="offlineForm" method="POST" action="{{ route('kasir.offline.sale') }}" class="space-y-3">
        @csrf
        <label class="form-control">
          <span class="label-text">Event</span>
          <select name="event_id" class="select select-bordered" required>
            @foreach($events as $e)
              <option value="{{ $e->id }}">{{ $e->title }} ({{ $e->start_at->format('d/m H:i') }})</option>
            @endforeach
          </select>
        </label>

        <label class="form-control">
          <span class="label-text">ID Kursi (pisahkan koma)</span>
          <input id="seatText" class="input input-bordered" placeholder="cth: 1,2,3">
          <span class="label-text-alt">Masukkan ID dari tabel event_seat_pricings.</span>
        </label>

        <label class="form-control">
          <span class="label-text">Metode</span>
          <select name="method" class="select select-bordered" required>
            <option value="cash">Cash</option>
            <option value="qris">QRIS</option>
            <option value="transfer">Transfer</option>
          </select>
        </label>

        {{-- container untuk input hidden seat_ids[] yang akan di-generate --}}
        <div id="seatHiddenWrap"></div>

        <button class="btn btn-primary w-full">Simpan</button>
      </form>
    </div>
  </div>

  {{-- RINGKASAN --}}
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

{{-- MENUNGGU ACC --}}
<div class="card bg-base-100 shadow mt-6">
  <div class="card-body">
    <div class="flex items-center justify-between mb-2">
      <h3 class="card-title">Menunggu ACC Kasir</h3>
      <span class="badge badge-warning">Total: {{ $pending->total() }}</span>
    </div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr><th>Ref</th><th>Pembeli</th><th>Event</th><th>Metode</th><th>Total</th><th>Bukti</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($pending as $ord)
          <tr>
            <td>{{ $ord->reference }}</td>
            <td>{{ $ord->user?->name ?? '-' }}</td>
            <td>{{ $ord->event->title }}</td>
            <td>{{ strtoupper($ord->payment_method) }}</td>
            <td>Rp {{ number_format($ord->total,0,',','.') }}</td>
            <td>
              @if($ord->payment_proof)
                <a class="link" target="_blank" href="{{ asset('storage/'.$ord->payment_proof) }}">Lihat</a>
              @else
                <span class="opacity-60">-</span>
              @endif
            </td>
            <td>
              <form method="POST" action="{{ route('kasir.confirm', $ord) }}">
                @csrf
                <button class="btn btn-success btn-sm">ACC</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center opacity-70">Tidak ada yang menunggu ACC.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $pending->links() }}</div>
  </div>
</div>

{{-- RIWAYAT TERBARU --}}
<div class="card bg-base-100 shadow mt-6">
  <div class="card-body">
    <div class="flex items-center justify-between">
      <h3 class="card-title">Riwayat Terbaru</h3>
      <a class="btn btn-sm btn-outline" href="{{ route('kasir.history') }}">Lihat semua</a>
    </div>
    <div class="overflow-x-auto mt-2">
      <table class="table table-sm">
        <thead><tr><th>Waktu</th><th>Ref</th><th>Event</th><th>Metode</th><th>Jumlah</th></tr></thead>
        <tbody>
          @forelse($recentTx as $t)
            <tr>
              <td>{{ $t->created_at->format('d/m H:i') }}</td>
              <td>{{ $t->order->reference ?? '-' }}</td>
              <td>{{ $t->order->event->title ?? '-' }}</td>
              <td>{{ strtoupper($t->method ?? '-') }}</td>
              <td>Rp {{ number_format($t->amount,0,',','.') }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center opacity-70">Belum ada transaksi.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>


{{-- Script kecil untuk mengubah "1,2,3" -> input hidden seat_ids[] --}}
@push('scripts')
<script>
  const form = document.getElementById('offlineForm');
  const seatText = document.getElementById('seatText');
  const wrap = document.getElementById('seatHiddenWrap');
  form.addEventListener('submit', (e) => {
    wrap.innerHTML = '';
    const ids = (seatText.value || '')
      .split(',')
      .map(v => v.trim())
      .filter(v => v.length > 0);
    if (ids.length === 0) {
      e.preventDefault();
      alert('Masukkan minimal 1 ID kursi.');
      return;
    }
    ids.forEach(id => {
      const inp = document.createElement('input');
      inp.type = 'hidden';
      inp.name = 'seat_ids[]';
      inp.value = id;
      wrap.appendChild(inp);
    });
  });
</script>
@endpush
@endsection
