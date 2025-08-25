@extends('layouts.app', ['title'=>'Kasir • Riwayat','page'=>'Riwayat Transaksi'])

@section('content')
<div class="card bg-base-100 shadow mb-4">
  <div class="card-body">
    <form class="grid md:grid-cols-5 gap-3">
      <input type="text" name="q" value="{{ request('q') }}" class="input input-bordered" placeholder="Cari ref, email, judul event">
      <select name="status" class="select select-bordered">
        <option value="">Semua Status</option>
        @foreach(['waiting_approval'=>'Menunggu ACC','awaiting_cash'=>'Menunggu Cash','paid'=>'Lunas','cancelled'=>'Dibatalkan'] as $k=>$v)
          <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
        @endforeach
      </select>
      <select name="method" class="select select-bordered">
        <option value="">Semua Metode</option>
        @foreach(['cash'=>'Cash','qris'=>'QRIS','transfer'=>'Transfer','va'=>'VA'] as $k=>$v)
          <option value="{{ $k }}" @selected(request('method')===$k)>{{ $v }}</option>
        @endforeach
      </select>
      <input type="date" name="from" value="{{ request('from') }}" class="input input-bordered">
      <input type="date" name="to"   value="{{ request('to') }}"   class="input input-bordered">
      <div class="md:col-span-5 flex gap-2">
        <button class="btn btn-primary">Filter</button>
        <a class="btn btn-ghost" href="{{ route('kasir.history') }}">Reset</a>
        <a class="btn btn-outline" href="{{ route('kasir.history.export', request()->all()) }}">Export CSV</a>
        <span class="ml-auto badge badge-outline">Total nominal (filter): Rp {{ number_format($sum,0,',','.') }}</span>
      </div>
    </form>
  </div>
</div>

<div class="card bg-base-100 shadow">
  <div class="card-body p-0">
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>Ref</th><th>Pembeli</th><th>Event</th><th>Metode</th><th>Total</th><th>Status</th><th>Waktu</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $o)
            <tr>
              <td>{{ $o->reference }}</td>
              <td>{{ $o->user->email ?? '-' }}</td>
              <td>{{ $o->event->title ?? '-' }}</td>
              <td>{{ strtoupper($o->payment_method) }}</td>
              <td>Rp {{ number_format($o->total,0,',','.') }}</td>
              <td>
                @php $badge = [
                  'waiting_approval'=>'badge-warning',
                  'awaiting_cash'=>'badge-neutral',
                  'paid'=>'badge-success',
                ][$o->status] ?? 'badge-ghost'; @endphp
                <span class="badge {{ $badge }}">{{ $o->status }}</span>
              </td>
              <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center opacity-70">Tidak ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="mt-3">
  {{ $orders->links() }}
</div>
@endsection
