<!doctype html>
<html lang="id" data-theme="emerald">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tiket {{ $order->reference }}</title>
  @vite(['resources/js/app.js'])
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: #fff !important; }
    }
  </style>
</head>
<body class="min-h-screen bg-base-200">
<div class="container-prose py-6">
  <div class="no-print mb-4">
    <button class="btn btn-primary" onclick="window.print()">Cetak</button>
  </div>

  <div class="card bg-base-100 shadow-xl">
    <div class="card-body">
      <div class="flex items-center justify-between">
        <h2 class="card-title">🎟️ DMATCH • E-Ticket</h2>
        <div class="text-right">
          <div class="font-mono text-sm">Ref: {{ $order->reference }}</div>
          <div class="badge {{ $order->status==='paid' ? 'badge-success' : 'badge-neutral' }}">{{ strtoupper($order->status) }}</div>
        </div>
      </div>
      <div class="divider my-2"></div>

      <div class="grid md:grid-cols-2 gap-3">
        <div>
          <div class="font-semibold">{{ $order->event->title }}</div>
          <div class="opacity-70">{{ $order->event->start_at->format('d M Y, H:i') }} • {{ $order->event->venue }}</div>
          <div class="mt-2 text-sm">Metode: <b>{{ strtoupper($order->payment_method) }}</b></div>
          <div class="text-sm">Total: <b>Rp {{ number_format($order->total,0,',','.') }}</b></div>
          <div class="text-sm">Atas nama: <b>{{ $order->user->name ?? '-' }}</b></div>
        </div>
        <div class="rounded-box border p-3">
          <div class="text-sm mb-1">Kursi</div>
          <ul class="text-sm">
            @foreach($order->items as $it)
              @php $s=$it->eventSeatPricing->stadiumSeat; @endphp
              <li>{{ $s->section }}-{{ $s->row_label }}-{{ $s->seat_number }} • Rp {{ number_format($it->price,0,',','.') }}</li>
            @endforeach
          </ul>
        </div>
      </div>

      <div class="mt-3 text-xs opacity-60">
        Tunjukkan tiket ini pada saat masuk. Dilarang memperbanyak untuk disalahgunakan.
      </div>
    </div>
  </div>
</div>
</body>
</html>
