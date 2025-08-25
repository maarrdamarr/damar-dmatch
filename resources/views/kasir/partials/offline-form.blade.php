{{-- resources/views/kasir/partials/offline-form.blade.php --}}
<div class="card bg-base-100 shadow">
  <div class="card-body">
    <h3 class="card-title">Input Penjualan Offline</h3>
    <form id="offlineForm" method="POST" action="{{ route('kasir.offline.sale') }}" class="space-y-3">
      @csrf
      <label class="form-control">
        <span class="label-text">Event</span>
        <select name="event_id" class="select select-bordered" required>
          @foreach($events as $e)
            <option value="{{ $e->id }}">{{ $e->title }} ({{ $e->start_at->format('d/m H:i') }})</option>
          @endforeach
        </select>
      </label>

      <label class="form-control">
        <span class="label-text">ID Kursi (pisahkan koma)</span>
        <input id="seatText" class="input input-bordered" placeholder="cth: 1,2,3">
        <span class="label-text-alt">Masukkan ID dari tabel event_seat_pricings.</span>
      </label>

      <label class="form-control">
        <span class="label-text">Metode</span>
        <select name="method" class="select select-bordered" required>
          <option value="cash">Cash</option>
          <option value="qris">QRIS</option>
          <option value="transfer">Transfer</option>
        </select>
      </label>

      <div id="seatHiddenWrap"></div>

      <button class="btn btn-primary w-full">Simpan</button>
    </form>
  </div>
</div>

@push('scripts')
<script>
  const form = document.getElementById('offlineForm');
  const seatText = document.getElementById('seatText');
  const wrap = document.getElementById('seatHiddenWrap');
  form?.addEventListener('submit', (e) => {
    wrap.innerHTML = '';
    const ids = (seatText.value || '')
      .split(',').map(v => v.trim()).filter(Boolean);
    if (ids.length === 0) { e.preventDefault(); alert('Masukkan minimal 1 ID kursi.'); return; }
    ids.forEach(id => {
      const inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = 'seat_ids[]'; inp.value = id;
      wrap.appendChild(inp);
    });
  });
</script>
@endpush
