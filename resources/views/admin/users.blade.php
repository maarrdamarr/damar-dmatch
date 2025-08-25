@extends('layouts.app', ['title'=>'Admin • Users','page'=>'Users'])

@section('content')
<div class="card bg-base-100 shadow">
  <div class="card-body">
    <div class="flex items-center justify-between mb-3">
      <h3 class="card-title">Daftar Pengguna</h3>
      <span class="badge badge-outline">Total: {{ $users->total() }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Dibuat</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $u)
            <tr>
              <td>{{ $u->id }}</td>
              <td>{{ $u->name }}</td>
              <td>{{ $u->email }}</td>
              <td><span class="badge">{{ $u->role }}</span></td>
              <td>{{ $u->created_at->format('d/m/Y H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
  </div>
</div>
@endsection
