@extends('layouts.app', ['title'=>'Kasir • Dashboard','page'=>'Kasir'])

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

<div class="grid md:grid-cols-3 gap-4">
  {{-- Form penjualan offline tetap --}}
  @includeWhen(true, 'kasir.partials.offline-form')

  {{-- Ringkasan --}}
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

{{-- Menunggu ACC --}}
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
@endsection
