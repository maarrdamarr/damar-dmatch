@extends('layouts.app', ['title' => 'Pilih Kursi', 'page' => $event->title])

@section('content')
  {{-- Info Event --}}
  <div class="card bg-base-100 shadow mb-4">
    <div class="card-body">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
          <h2 class="text-xl font-semibold">{{ $event->title }}</h2>
          <p class="opacity-70">
            {{ $event->start_at->format('d M Y, H:i') }}
            @if($event->end_at)– {{ $event->end_at->format('H:i') }}@endif
            • {{ $event->venue }}
          </p>
        </div>
        <div class="flex items-center gap-2">
          <span class="badge">{{ ucfirst($event->status) }}</span>
          <span class="badge badge-outline">Kapasitas: {{ $event->capacity }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Jika kursi belum ada --}}
  @if($event->seatPricings->isEmpty())
    <div class="alert alert-warning">
      Kursi belum disetting untuk event ini. Silakan kembali lagi nanti atau hubungi admin.
    </div>
  @else
    @php
      $available = $event->seatPricings->where('status','available')->count();
      $unavailable = $event->seatPricings->count() - $available;
    @endphp

    {{-- Legend & ringkasan --}}
    <div class="flex flex-wrap items-center gap-3 mb-3">
      <span class="badge badge-success">Tersedia: {{ $available }}</span>
      <span class="badge badge-neutral">Terjual/Reserved: {{ $unavailable }}</span>
    </div>

    {{-- Form pilih kursi --}}
    <form method="POST" action="{{ route('cart.add') }}" class="space-y-4">
      @csrf
      <input type="hidden" name="event_id" value="{{ $event->id }}">

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
        @foreach($event->seatPricings as $sp)
          @php
            $s = $sp->stadiumSeat;
            $sold = $sp->status !== 'available';
          @endphp
          <label class="relative group">
            <input
              type="checkbox"
              name="seat_ids[]"
              value="{{ $sp->id }}"
              class="checkbox checkbox-sm absolute top-2 left-2"
              {{ $sold ? 'disabled' : '' }}
            >
            <div class="p-3 rounded-box border text-center transition
                        {{ $sold
                          ? 'bg-base-300 text-base-content/60 border-base-300 cursor-not-allowed'
                          : 'bg-base-100 hover:bg-primary/10 hover:border-primary/40 cursor-pointer' }}">
              <div class="font-semibold">
                {{ $s->section }}-{{ $s->row_label }}-{{ $s->seat_number }}
              </div>
              <div class="text-sm">Rp {{ number_format($sp->price, 0, ',', '.') }}</div>
              @if($sold)
                <div class="mt-1 text-xs opacity-70">({{ $sp->status }})</div>
              @endif
            </div>
          </label>
        @endforeach
      </div>

      <div class="flex items-center justify-between">
        <a href="{{ route('events.index') }}" class="btn btn-ghost">Kembali</a>
        <button class="btn btn-primary">Tambah ke Keranjang</button>
      </div>
    </form>
  @endif
@endsection
