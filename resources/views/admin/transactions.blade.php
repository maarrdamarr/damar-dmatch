@extends('layouts.app', ['title'=>'Admin • Transaksi','page'=>'Transaksi'])

@section('content')
<div class="card bg-base-100 shadow">
  <div class="card-body">
    <div class="flex items-center justify-between mb-3">
      <h3 class="card-title">Daftar Transaksi</h3>
      <span class="badge badge-outline">Total: {{ $tx->total() }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>Ref</th>
            <th>Event</th>
            <th>Kanal</th>
            <th>Metode</th>
            <th>Jumlah</th>
            <th>Tipe</th>
            <th>Dibayar</th>
            <th>Waktu</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tx as $t)
            @php
              $o = $t->order;
              $ev = optional($o)->event;
            @endphp
            <tr>
              <td>{{ $o?->reference ?? '-' }}</td>
              <td>{{ $ev?->title ?? '-' }}</td>
              <td>{{ strtoupper($o?->channel ?? '-') }}</td>
              <td>{{ $t->method ?? '-' }}</td>
              <td>Rp {{ number_format($t->amount,0,',','.') }}</td>
              <td>
                <span class="badge {{ $t->type === 'in' ? 'badge-success' : 'badge-error' }}">
                  {{ $t->type }}
                </span>
              </td>
              <td>{{ $t->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
              <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center opacity-70">Belum ada transaksi.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $tx->links() }}</div>
  </div>
</div>
@endsection
