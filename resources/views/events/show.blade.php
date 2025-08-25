@extends('layouts.app', ['title'=>'Pilih Kursi','page'=>$event->title])

@section('content')
@php
  // radius dalam % dari sisi container (sesuaikan bila ingin lebih renggang)
  $radiusForRing = [1=>44, 2=>30, 3=>16]; // 1=Reguler(outer), 2=VIP, 3=VVIP
@endphp

<form method="POST" action="{{ route('cart.add') }}" class="space-y-5">
  @csrf
  <input type="hidden" name="event_id" value="{{ $event->id }}">

  <div class="flex flex-wrap items-center gap-2">
    <span class="badge badge-info">Reguler</span>
    <span class="badge badge-warning">VIP</span>
    <span class="badge badge-error">VVIP</span>
    <span class="badge">Abu-abu = Terjual/Reserved</span>
  </div>

  <div class="mx-auto w-full max-w-3xl">
    <div class="relative aspect-square rounded-box bg-base-100 shadow border">
      <!-- lingkaran panduan -->
      <div class="absolute inset-0 m-6 rounded-full border-2 border-base-300"></div>
      <div class="absolute inset-[20%] rounded-full border border-base-300"></div>
      <div class="absolute inset-[36%] rounded-full border border-base-300"></div>
      <div class="absolute inset-[48%] rounded-full bg-base-200 flex items-center justify-center text-xs opacity-70">
        Panggung
      </div>

      @foreach($event->seatPricings as $sp)
        @php
          $seat   = $sp->stadiumSeat;
          $ring   = (int)($seat->ring ?? 1);
          $angle  = (int)($seat->angle_deg ?? 0);
          $radius = $radiusForRing[$ring] ?? 40;

          $sold   = $sp->status !== 'available';
          $btn    = match($seat->seat_class){
                      'vvip' => 'btn-error',
                      'vip'  => 'btn-warning',
                      default=> 'btn-info'
                    };
          $disabled = $sold ? 'opacity-40 pointer-events-none' : 'hover:scale-110';
          $label = $seat->section.'-'.$seat->row_label.'-'.$seat->seat_number;
        @endphp

        <label
          class="absolute top-1/2 left-1/2 transition-transform duration-150"
          style="transform:
                 rotate({{ $angle }}deg)
                 translate({{ $radius }}%)
                 rotate(-{{ $angle }}deg)
                 translate(-50%, -50%);"
          title="{{ strtoupper($seat->seat_class) }} • {{ $label }} • Rp {{ number_format($sp->price,0,',','.') }}"
        >
          <input type="checkbox" name="seat_ids[]" value="{{ $sp->id }}" class="sr-only" {{ $sold?'disabled':'' }}/>
          <div class="btn btn-xs btn-circle {{ $btn }} {{ $disabled }}"></div>
        </label>
      @endforeach
    </div>
  </div>

  <div class="flex items-center gap-3">
    <button class="btn btn-primary">Tambah ke Keranjang</button>
    <a href="{{ route('events.index') }}" class="btn">Kembali</a>
  </div>
</form>
@endsection
