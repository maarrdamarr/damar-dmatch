<!doctype html>
<html lang="id" data-theme="emerald">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title ?? 'DAMAR DMATCH' }}</title>
  @vite(['resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200">
<div class="drawer lg:drawer-open">
  <input id="app-drawer" type="checkbox" class="drawer-toggle"/>
  <div class="drawer-content flex flex-col">
    <!-- Topbar -->
    <div class="navbar bg-base-100 shadow">
      <div class="flex-none lg:hidden">
        <label for="app-drawer" class="btn btn-ghost btn-square">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </label>
      </div>
      <div class="flex-1">
        <a href="{{ route('home') }}" class="btn btn-ghost text-lg font-bold">🎟️ DMATCH</a>
      </div>
      <div class="flex-none">
        @auth
          <a class="btn btn-ghost" href="{{ route('profile.edit') }}">Profil</a>
          <form method="POST" action="{{ route('logout') }}" class="ml-2">@csrf
            <button class="btn btn-primary">Logout</button>
          </form>
        @else
          <a class="btn btn-ghost" href="{{ route('login') }}">Login</a>
          <a class="btn btn-primary ml-2" href="{{ route('register') }}">Daftar</a>
        @endauth
      </div>
    </div>

    <!-- Content -->
    <main class="p-4 lg:p-6">
      @isset($page)<h1 class="text-2xl font-semibold mb-4">{{ $page }}</h1>@endisset
      {{ $slot ?? '' }}
      @yield('content')
    </main>
  </div>

  <!-- Sidebar -->
  <div class="drawer-side z-40">
    <label for="app-drawer" class="drawer-overlay"></label>
    <aside class="w-72 bg-base-100 p-4 border-r">
      <div class="mb-4">
        <div class="text-xl font-bold">Menu</div>
        <div class="text-sm opacity-70">DAMAR DMATCH</div>
      </div>
      <ul class="menu">
        <li><a class="{{ request()->routeIs('events.index')?'active':'' }}" href="{{ route('events.index') }}">🎫 Event Publik</a></li>
        @auth
          @if(auth()->user()->role === 'admin')
            <li class="menu-title">Admin</li>
            <li><a class="{{ request()->routeIs('admin.dashboard')?'active':'' }}" href="{{ route('admin.dashboard') }}">📊 Dashboard</a></li>
            <li><a class="{{ request()->is('admin/events*')?'active':'' }}" href="{{ route('admin.events.create') }}">🗓️ Kelola Event</a></li>
            <li><a class="{{ request()->routeIs('admin.transactions')?'active':'' }}" href="{{ route('admin.transactions') }}">💸 Transaksi</a></li>
            <li><a class="{{ request()->routeIs('admin.users')?'active':'' }}" href="{{ route('admin.users') }}">👥 Users</a></li>
          @endif
          @if(in_array(auth()->user()->role, ['kasir','admin']))
            <li class="menu-title">Kasir</li>
            <li><a class="{{ request()->routeIs('kasir.dashboard')?'active':'' }}" href="{{ route('kasir.dashboard') }}">🧾 Kasir</a></li>
          @endif
        @endauth
      </ul>
    </aside>
  </div>
</div>
</body>
</html>
