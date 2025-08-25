Siap. Ini versi **rapi & elegan** untuk user dashboard. Ganti file berikut:

**resources/views/user/dashboard.blade.php**

```blade
@extends('layouts.app', ['title'=>'Akun Saya','page'=>'Dashboard Pengguna'])

@section('content')
@php
  $user = auth()->user();
  $totalOrders  = method_exists($orders,'total')  ? $orders->total()  : $orders->count();
  $totalTickets = method_exists($tickets,'total') ? $tickets->total() : $tickets->count();
  $pendingOnPage = collect($orders instanceof \Illuminate\Pagination\AbstractPaginator ? $orders->items() : $orders)
                    ->where('status','!=','paid')->count();

  function badgeClass($status){
    return match($status){
      'paid' => 'badge-success',
      'waiting_approval' => 'badge-warning',
      'awaiting_cash' => 'badge-neutral',
      'refunded' => 'badge-info',
      'cancelled' => 'badge-error',
      default => 'badge-ghost'
    };
  }
@endphp

{{-- HERO --}}
<div class="relative overflow-hidden rounded-2xl mb-6">
  <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 via-violet-600/10 to-cyan-500/10"></div>
  <div class="relative p-6 md:p-8 bg-base-100/40 backdrop-blur">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <div class="text-xs opacity-70">Selamat datang,</div>
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">{{ $user->name }}</h1>
        <p class="opacity-80 mt-1 text-sm">Kelola pesanan, cetak tiket, dan ajukan bantuan dengan mudah.</p>
      </div>
      <div class="stats bg-base-200 shadow-sm rounded-xl">
        <div class="stat">
          <div class="stat-title">Total Pesanan</div>
          <div class="stat-value text-primary">{{ number_format($totalOrders) }}</div>
        </div>
        <div class="stat">
          <div class="stat-title">Tiket Dimiliki</div>
          <div class="stat-value text-success">{{ number_format($totalTickets) }}</div>
        </div>
        <div class="stat">
          <div class="stat-title">Perlu Tindakan</div>
          <div class="stat-value text-warning">{{ $pendingOnPage }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- TAB WRAPPER --}}
<div class="tabs tabs-boxed mb-4">
  <a class="tab tab-active" id="tab-orders"   onclick="switchTab('orders')">Pesanan</a>
  <a class="tab"              id="tab-tickets" onclick="switchTab('tickets')">Tiket Dimiliki</a>
  <a class="tab"              id="tab-help"    onclick="switchTab('help')">Pengajuan & CS</a>
</div>

{{-- PESANAN SAYA --}}
<section id="panel-orders">
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h3 class="card-title">Pesanan Saya</h3>
        <div class="join">
          <input id="orderSearch" type="text" class="input input-bordered join-item" placeholder="Cari ref / judul event">
          <button class="btn btn-ghost join-item" onclick="filterOrders()">Cari</button>
          <button class="btn join-item" onclick="resetOrders()">Reset</button>
        </div>
      </div>

      <div class="overflow-x-auto mt-2">
        <table class="table table-zebra" id="ordersTable">
          <thead>
            <tr>
              <th>Ref</th>
              <th>Event</th>
              <th>Metode</th>
              <th>Total</th>
              <th>Status</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
          @forelse($orders as $o)
            <tr>
              <td class="font-mono">{{ $o->reference }}</td>
              <td>{{ $o->event->title }}</td>
              <td>{{ strtoupper($o->payment_method) }}</td>
              <td>Rp {{ number_format($o->total,0,',','.') }}</td>
              <td><span class="badge {{ badgeClass($o->status) }}">{{ $o->status }}</span></td>
              <td class="text-right">
                <div class="join">
                  <a class="btn btn-xs btn-outline join-item" target="_blank" href="{{ route('tickets.print',$o->reference) }}">Cetak</a>
                  <a class="btn btn-xs join-item" href="{{ route('events.show',$o->event_id) }}">Detail</a>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center opacity-70">Belum ada pesanan.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-2">{{ $orders->links() }}</div>
    </div>
  </div>
</section>

{{-- TIKET DIMILIKI --}}
<section id="panel-tickets" class="hidden">
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Tiket Dimiliki</h3>
      @if($tickets->count() === 0)
        <div class="alert">Belum ada tiket yang lunas.</div>
      @endif

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($tickets as $it)
          @php $s = $it->eventSeatPricing->stadiumSeat; @endphp
          <div class="card bg-base-200/50 border">
            <div class="card-body">
              <div class="text-sm opacity-70">Event</div>
              <div class="font-semibold">{{ $it->order->event->title }}</div>
              <div class="mt-2 flex flex-wrap gap-2">
                <span class="badge badge-outline">Section {{ $s->section }}</span>
                <span class="badge badge-outline">Row {{ $s->row_label }}</span>
                <span class="badge badge-outline">Seat {{ $s->seat_number }}</span>
              </div>
              <div class="mt-2 text-sm">Harga: <b>Rp {{ number_format($it->price,0,',','.') }}</b></div>
              <div class="card-actions justify-end mt-3">
                <a class="btn btn-sm btn-primary" target="_blank" href="{{ route('tickets.print',$it->order->reference) }}">Cetak Tiket</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">{{ $tickets->links() }}</div>
    </div>
  </div>
</section>

{{-- PENGAJUAN & CS --}}
<section id="panel-help" class="hidden">
  <div class="grid lg:grid-cols-2 gap-4">
    {{-- Refund --}}
    <div class="card bg-base-100 shadow">
      <div class="card-body">
        <h3 class="card-title">Ajukan Refund</h3>
        <p class="text-sm opacity-70 -mt-1">Pengembalian sebagian/seluruh nominal. Konfirmasi melalui CS.</p>
        <form method="POST" action="{{ route('support.refund') }}" class="space-y-3">@csrf
          <label class="form-control">
            <span class="label-text">Nomor Pemesanan</span>
            <input name="reference" class="input input-bordered" placeholder="cth: ORD-XXXX" required>
          </label>
          <label class="form-control">
            <span class="label-text">Nominal Refund (Rp)</span>
            <input type="number" min="1000" name="amount" class="input input-bordered" required>
          </label>
          <label class="form-control">
            <span class="label-text">Alasan (opsional)</span>
            <textarea name="reason" class="textarea textarea-bordered" rows="3"></textarea>
          </label>
          <button class="btn btn-warning">Kirim Pengajuan</button>
        </form>
      </div>
    </div>

    {{-- Pindah kursi --}}
    <div class="card bg-base-100 shadow">
      <div class="card-body">
        <h3 class="card-title">Ajukan Pindah Kursi</h3>
        <p class="text-sm opacity-70 -mt-1">Minta tukar kursi selama kuota tersedia. Selisih harga akan dikonfirmasi.</p>
        <form method="POST" action="{{ route('support.swap') }}" class="space-y-3">@csrf
          <label class="form-control">
            <span class="label-text">Nomor Pemesanan</span>
            <input name="reference" class="input input-bordered" required>
          </label>
          <div class="grid grid-cols-2 gap-3">
            <label class="form-control">
              <span class="label-text">ID Kursi Lama</span>
              <input type="number" name="old_sp_id" class="input input-bordered" required>
            </label>
            <label class="form-control">
              <span class="label-text">ID Kursi Baru (available)</span>
              <input type="number" name="new_sp_id" class="input input-bordered" required>
            </label>
          </div>
          <label class="form-control">
            <span class="label-text">Alasan (opsional)</span>
            <textarea name="reason" class="textarea textarea-bordered" rows="3"></textarea>
          </label>
          <button class="btn btn-primary">Kirim Pengajuan</button>
        </form>
      </div>
    </div>

    {{-- CS --}}
    <div class="lg:col-span-2 card bg-base-100 shadow">
      <div class="card-body">
        <h3 class="card-title">Butuh Bantuan?</h3>
        <div class="grid sm:grid-cols-3 gap-3">
          <a class="btn btn-outline" href="mailto:cs@dmatch.test">📧 Email CS</a>
          <a class="btn btn-outline" target="_blank" href="https://wa.me/6281234567890">🟢 WhatsApp Kasir</a>
          <a class="btn btn-outline" href="{{ route('kasir.help') }}">📘 Panduan Kasir/Admin</a>
        </div>

        @isset($supports)
          @if($supports->count())
            <div class="divider my-4">Pengajuan Terakhir</div>
            <div class="overflow-x-auto">
              <table class="table table-sm">
                <thead><tr><th>Waktu</th><th>Jenis</th><th>Ref</th><th>Status</th></tr></thead>
                <tbody>
                  @foreach($supports as $t)
                    <tr>
                      <td>{{ $t->created_at->format('d/m H:i') }}</td>
                      <td>{{ strtoupper($t->type) }}</td>
                      <td class="font-mono">{{ $t->reference }}</td>
                      <td><span class="badge">{{ $t->status }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        @endisset
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  function switchTab(name){
    const panels = ['orders','tickets','help'];
    panels.forEach(p=>{
      document.getElementById('panel-'+p).classList.toggle('hidden', p!==name);
      document.getElementById('tab-'+p).classList.toggle('tab-active', p===name);
    });
  }
  function filterOrders(){
    const q = (document.getElementById('orderSearch').value || '').toLowerCase();
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    rows.forEach(tr=>{
      tr.style.display = [...tr.children].some(td=>td.innerText.toLowerCase().includes(q)) ? '' : 'none';
    });
  }
  function resetOrders(){
    document.getElementById('orderSearch').value='';
    filterOrders();
  }
</script>
@endpush
```
