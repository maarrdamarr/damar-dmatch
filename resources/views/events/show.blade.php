@extends('layouts.app', ['title'=>'Pilih Kursi','page'=>$event->title])

@section('content')
<form method="POST" action="{{ route('cart.add') }}" class="space-y-4">
  @csrf
  <input type="hidden" name="event_id" value="{{ $event->id }}">

  <div class="alert">
    <div>
      <h4 class="font-semibold">{{ $event->title }}</h4>
      <p class="text-sm opacity-70">{{ $event->start_at->format('d M Y, H:i') }} • {{ $event->venue }}</p>
    </div>
  </div>

  <div class="flex items-center gap-3 text-sm">
    <span class="badge badge-outline">Tersedia</span>
    <span class="badge badge-neutral">Terjual / Reserved</span>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
    @foreach($event->seatPricings as $sp)
      @php $s = $sp->stadiumSeat; $sold = $sp->status !== 'available'; @endphp
      <label class="relative">
        <input type="checkbox" name="seat_ids[]" value="{{ $sp->id }}" class="checkbox checkbox-sm absolute top-2 left-2" {{ $sold?'disabled':'' }}>
        <div class="p-3 rounded-box border text-center {{ $sold ? 'bg-base-300 text-base-content/60' : 'bg-base-100 hover:bg-primary/10 cursor-pointer' }}">
          <div class="font-semibold">{{ $s->section }}-{{ $s->row_label }}-{{ $s->seat_number }}</div>
          <div class="text-sm">Rp {{ number_format($sp->price,0,',','.') }}</div>
        </div>
      </label>
    @endforeach
  </div>

  <button class="btn btn-primary">Tambah ke Keranjang</button>
</form>
@endsection
