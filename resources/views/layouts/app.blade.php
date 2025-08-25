{{-- resources/views/layouts/app.blade.php --}}
@php
  $appTitle = $title ?? ($page ?? 'DMATCH');
@endphp
<!doctype html>
<html lang="id" data-theme="night">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $appTitle }} • DMATCH</title>
  @vite(['resources/js/app.js'])
</head>
<body class="bg-base-200 text-base-content">

<div class="drawer lg:drawer-open">          {{-- wrapper utama drawer --}}
  <input id="app-drawer" type="checkbox" class="drawer-toggle" />  {{-- <-- dipindah ke dalam .drawer --}}

  {{-- ====== CONTENT AREA ====== --}}
  <div class="drawer-content min-h-screen">
    {{-- NAVBAR --}}
    <div class="navbar bg-base-100/80 backdrop-blur sticky top-0 z-40 shadow-sm">
      <div class="flex-none lg:hidden">
        <label for="app-drawer" class="btn btn-ghost btn-square" aria-label="open sidebar">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </label>
      </div>
      <div class="flex-1">
        <a href="{{ url('/') }}" class="font-extrabold tracking-wide text-lg">🎫 DMATCH</a>
      </div>
      <div class="flex-none gap-2">
        @auth
          @if (Route::has('profile.edit'))
            <a class="btn btn-ghost btn-sm" href="{{ route('profile.edit') }}">Profil</a>
          @endif
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-primary btn-sm">Logout</button>
          </form>
        @else
          @if (Route::has('login')) <a class="btn btn-ghost btn-sm" href="{{ route('login') }}">Login</a> @endif
          @if (Route::has('register')) <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Daftar</a> @endif
        @endauth
      </div>
    </div>

    {{-- PAGE CONTAINER --}}
    <div class="p-4">
      @if(!empty($page))
        <div class="mb-3 text-sm breadcrumbs">
          <ul>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="font-semibold">{{ $page }}</li>
          </ul>
        </div>
      @endif

      {{-- Flash & errors --}}
      @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-error mb-3">
          <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </div>
  </div>

  {{-- ====== SIDEBAR ====== --}}
  <div class="drawer-side">
    <label for="app-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
    <aside class="w-72 min-h-full bg-base-100 border-r">
      <div class="p-4">
        <div class="font-bold text-lg leading-tight">Menu</div>
        @auth
          <div class="text-xs opacity-70 mt-1">{{ strtoupper(auth()->user()->name ?? 'TAMU') }}</div>
        @endauth
      </div>

      <ul class="menu px-2 pb-6 gap-1">
        {{-- PUBLIC --}}
        <li class="menu-title">Umum</li>
        <li>
          <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*') ? 'active' : '' }}">
            🟨 Event Publik
          </a>
        </li>

        {{-- USER MENU (setelah Event Publik) --}}
        @auth
          @php($role = auth()->user()->role ?? 'user')
          @if(!in_array($role, ['admin','kasir']))
            <li class="menu-title">Akun</li>
            <li>
              <a href="{{ route('user.dashboard') }}"
                 class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                 👤 Dashboard Saya
              </a>
            </li>
            <li><a href="mailto:cs@dmatch.test">💬 Hubungi CS (Email)</a></li>
            <li><a target="_blank" href="https://wa.me/6281234567890">🟢 WhatsApp Kasir</a></li>
          @endif
        @endauth

        {{-- ADMIN MENU --}}
        @auth
          @if(($role ?? null) === 'admin')
            <li class="menu-title">Admin</li>
            @if(Route::has('admin.dashboard'))
              <li><a class="{{ request()->routeIs('admin.dashboard')?'active':'' }}" href="{{ route('admin.dashboard') }}">📊 Dashboard</a></li>
            @endif
            @if(Route::has('admin.users'))
              <li><a class="{{ request()->routeIs('admin.users')?'active':'' }}" href="{{ route('admin.users') }}">👥 Users</a></li>
            @endif
            @if(Route::has('admin.transactions'))
              <li><a class="{{ request()->routeIs('admin.transactions')?'active':'' }}" href="{{ route('admin.transactions') }}">💸 Transaksi</a></li>
            @endif
            @if(Route::has('admin.events.create'))
              <li><a class="{{ request()->is('admin/events*')?'active':'' }}" href="{{ route('admin.events.create') }}">🗓️ Kelola Event</a></li>
            @endif
          @endif
        @endauth

        {{-- KASIR MENU --}}
        @auth
          @if(($role ?? null) === 'kasir')
            <li class="menu-title">Kasir</li>
            @if(Route::has('kasir.dashboard'))
              <li><a class="{{ request()->routeIs('kasir.dashboard')?'active':'' }}" href="{{ route('kasir.dashboard') }}">🧾 Kasir</a></li>
            @endif
            @if(Route::has('kasir.refund.form'))
              <li><a class="{{ request()->routeIs('kasir.refund.*')?'active':'' }}" href="{{ route('kasir.refund.form') }}">↩️ Refund / Pindah Kursi</a></li>
            @endif
            @if(Route::has('kasir.history'))
              <li><a class="{{ request()->routeIs('kasir.history')?'active':'' }}" href="{{ route('kasir.history') }}">📜 Riwayat Transaksi</a></li>
            @endif
            @if(Route::has('kasir.print.form'))
              <li><a class="{{ request()->routeIs('kasir.print.form')?'active':'' }}" href="{{ route('kasir.print.form') }}">🖨️ Cetak Tiket (Offline)</a></li>
            @endif
            @if(Route::has('kasir.help'))
              <li><a class="{{ request()->routeIs('kasir.help')?'active':'' }}" href="{{ route('kasir.help') }}">🧠 Bantuan</a></li>
            @endif
          @endif
        @endauth
      </ul>
    </aside>
  </div>
</div>

@stack('scripts')
</body>
</html>
