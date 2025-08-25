@extends('layouts.app', ['title'=>'Event','page'=>'Daftar Event'])

@section('content')
<div class="grid md:grid-cols-3 gap-4">
  @foreach($events as $e)
    <div class="card bg-base-100 shadow">
      <div class="card-body">
        <h3 class="card-title">{{ $e->title }}</h3>
        <p class="opacity-70">{{ $e->start_at->format('d M Y, H:i') }} • {{ $e->venue }}</p>
        <div class="card-actions justify-end">
          <a href="{{ route('events.show',$e) }}" class="btn btn-primary">Pilih Kursi</a>
        </div>
      </div>
    </div>
  @endforeach
</div>
<div class="mt-4">{{ $events->links() }}</div>
@endsection
