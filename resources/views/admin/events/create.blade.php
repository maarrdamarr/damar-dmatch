@extends('layouts.app', ['title'=>'Admin • Kelola Event','page'=>'Kelola Event'])

@section('content')
<div class="grid lg:grid-cols-2 gap-4">
  <div class="card bg-base-100 shadow">
    <div class="card-body">
      <h3 class="card-title">Buat Event + Generate Kursi</h3>
      <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-3">
        @csrf
        <label class="form-control">
          <span class="label-text">Judul</span>
          <input name="title" class="input input-bordered" required>
        </label>
        <label class="form-control">
          <span class="label-text">Deskripsi</span>
          <textarea name="description" class="textarea textarea-bordered"></textarea>
        </label>
        <div class="grid grid-cols-2 gap-3">
          <label class="form-control">
            <span class="label-text">Mulai</span>
            <input type="datetime-local" name="start_at" class="input input-bordered" required>
          </label>
          <label class="form-control">
            <span class="label-text">Selesai</span>
            <input type="datetime-local" name="end_at" class="input input-bordered">
          </label>
        </div>
        <label class="form-control">
          <span class="label-text">Venue</span>
          <input name="venue" class="input input-bordered" value="Stadion Utama">
        </label>

        <div class="divider">Generate Kursi Otomatis</div>
        <div class="grid grid-cols-2 gap-3">
          <label class="form-control">
            <span class="label-text">Section(s)</span>
            <input name="sections" class="input input-bordered" value="A">
            <span class="label-text-alt">Pisahkan koma jika banyak, contoh: A,B</span>
          </label>
          <label class="form-control">
            <span class="label-text">Baris/Section</span>
            <input type="number" name="rows" class="input input-bordered" value="10" min="1">
          </label>
          <label class="form-control">
            <span class="label-text">Kursi/Baris</span>
            <input type="number" name="seats_per_row" class="input input-bordered" value="15" min="1">
          </label>
          <label class="form-control">
            <span class="label-text">Harga dasar</span>
            <input type="number" name="base_price" class="input input-bordered" value="75000" min="0">
          </label>
        </div>

        <button class="btn btn-primary">Simpan & Generate Kursi</button>
      </form>
    </div>
  </div>
</div>
@endsection
