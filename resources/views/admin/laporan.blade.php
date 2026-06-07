@extends('layouts.admin')
@section('title', 'Laporan Keuangan')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Laporan Keuangan</div>
    </div>
    <button onclick="window.print()" class="btn-secondary">🖨️ Print / PDF</button>
  </div>

  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:2rem">
    @foreach(['s-masuk'=>['Total Masuk','#E8F4ED','var(--forest)'],'s-keluar'=>['Total Keluar','#FDECEA','var(--rust)'],'s-saldo'=>['Saldo Kas','var(--gold-pale)','#7A5C00'],'s-donatur'=>['Total Donatur','#E8F0FD','#2D5AA8']] as $id=>[$lbl,$bg,$col])
    <div style="background:{{ $bg }};border-radius:var(--radius-sm);padding:1.25rem">
      <div style="font-size:12px;color:var(--ink-soft);margin-bottom:4px">{{ $lbl }}</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.4rem;color:{{ $col }}" id="{{ $id }}">—</div>
    </div>
    @endforeach
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
    <div class="card">
      <div style="font-size:15px;font-weight:500;margin-bottom:1rem">Progress kampanye</div>
      <div id="kampanye-report"></div>
    </div>
    <div class="card">
      <div style="font-size:15px;font-weight:500;margin-bottom:1rem">Pengeluaran terbaru</div>
      <div id="pengeluaran-report"></div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
async function loadLaporan() {
  const [sumRes, kRes, pRes] = await Promise.all([
    fetch('/api/kas/summary'), fetch('/api/kampanye'), fetch('/api/pengeluaran')
  ]);
  const sum = await sumRes.json();
  const k = await kRes.json();
  const p = await pRes.json();
  if (sum.success) {
    document.getElementById('s-masuk').textContent = 'Rp '+(parseInt(sum.data.masuk||0)).toLocaleString('id-ID');
    document.getElementById('s-keluar').textContent = 'Rp '+(parseInt(sum.data.keluar||0)).toLocaleString('id-ID');
    document.getElementById('s-saldo').textContent = 'Rp '+(parseInt(sum.data.saldo||0)).toLocaleString('id-ID');
    document.getElementById('s-donatur').textContent = sum.data.donasi_count||0;
  }
  if (k.success) {
    document.getElementById('kampanye-report').innerHTML = k.data.map(kk => {
      const pct = Math.min(100,Math.round(kk.terkumpul/kk.target*100));
      return `<div style="margin-bottom:1rem">
        <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
          <span style="font-weight:500">${kk.judul}</span><span style="color:var(--ink-soft)">${pct}%</span>
        </div>
        <div class="progress-track" style="margin:0 0 4px"><div class="progress-fill pf-green" style="width:${pct}%"></div></div>
        <div style="font-size:11px;color:var(--ink-soft)">Rp ${parseInt(kk.terkumpul).toLocaleString('id-ID')} / Rp ${parseInt(kk.target).toLocaleString('id-ID')}</div>
      </div>`;
    }).join('');
  }
  if (p.success) {
    document.getElementById('pengeluaran-report').innerHTML = !p.data.length
      ? '<div style="color:var(--ink-soft);font-size:13px">Belum ada pengeluaran.</div>'
      : p.data.slice(0,8).map(pp => `<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px">
          <span>${pp.keterangan}</span>
          <span style="font-weight:600;color:var(--rust)">- Rp ${parseInt(pp.nominal).toLocaleString('id-ID')}</span>
        </div>`).join('');
  }
}
loadLaporan();
</script>
@endsection
