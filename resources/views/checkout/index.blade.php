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
          <tr><th>Total</th><th>Rp {{ number_format($total,0,',','.') }}</th></tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
<form method="POST" action="{{ route('checkout.place') }}" class="mt-4">@csrf
  <button class="btn btn-success">Bayar (Demo)</button>
</form>
@endsection
