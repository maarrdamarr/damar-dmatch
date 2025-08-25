@extends('layouts.app', ['title'=>'Sukses','page'=>'Pembayaran Berhasil'])

@section('content')
<div class="alert alert-success">
  <span>Terima kasih! Pesanan <b>{{ $order->reference }}</b> sudah <b>{{ $order->status }}</b>.</span>
</div>
<a href="{{ route('events.index') }}" class="btn btn-primary">Kembali ke Event</a>
@endsection
