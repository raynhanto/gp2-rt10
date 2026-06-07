@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container">
  <div class="fade-in" style="margin-bottom:2.5rem">
    <div class="section-label">Transparansi</div>
    <div class="section-title">Laporan keuangan<br>terbuka untuk semua warga</div>
  </div>

  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:2.5rem" id="summary">
    @foreach(['kas-masuk'=>['Rp 0','Total Masuk','#E8F4ED','var(--forest)'],'kas-keluar'=>['Rp 0','Total Keluar','#FDECEA','var(--rust)'],'kas-saldo'=>['Rp 0','Saldo Kas','var(--gold-pale)','#7A5C00'],'donasi-count'=>['0','Total Donasi','#F0F0EE','var(--ink-mid)']] as $id=>[$val,$lbl,$bg,$col])
    <div style="background:{{ $bg }};border-radius:var(--radius-sm);padding:1.25rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:{{ $col }}" id="{{ $id }}">{{ $val }}</div>
      <div style="font-size:12px;color:var(--ink-soft);margin-top:4px">{{ $lbl }}</div>
    </div>
    @endforeach
  </div>

  <div class="card fade-in" style="margin-bottom:1.5rem">
    <div style="font-size:15px;font-weight:500;color:var(--ink);margin-bottom:1.25rem">Progress per kampanye</div>
    <div id="kampanye-progress"><div style="text-align:center;padding:1.5rem;color:var(--ink-soft)">Memuat...</div></div>
  </div>

  <div class="card fade-in">
    <div style="font-size:15px;font-weight:500;color:var(--ink);margin-bottom:1.25rem">Pengeluaran terbaru</div>
    <div id="pengeluaran-list"><div style="text-align:center;padding:1.5rem;color:var(--ink-soft)">Memuat...</div></div>
  </div>
</div>
</main>
@endsection
@section('scripts')
<script>
async function loadLaporan() {
  const [kRes, kasRes] = await Promise.all([
    fetch('/api/kampanye'),
    fetch('/api/kas/summary')
  ]);
  const kData = await kRes.json();

  if (kData.success) {
    const list = kData.data;
    const colors = ['pf-green','pf-gold','pf-rust','pf-green'];
    document.getElementById('kampanye-progress').innerHTML = list.map((k,i) => {
      const pct = Math.min(100, Math.round(k.terkumpul / k.target * 100));
      return `<div style="margin-bottom:1.25rem">
        <div style="display:flex;justify-content:space-between;margin-bottom:6px">
          <span style="font-size:14px;font-weight:500;color:var(--ink)">${k.judul}</span>
          <span style="font-size:13px;color:var(--ink-soft)">${pct}%</span>
        </div>
        <div class="progress-track" style="margin:0 0 6px"><div class="progress-fill ${colors[i%4]}" style="width:${pct}%"></div></div>
        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-soft)">
          <span>Terkumpul: Rp ${parseInt(k.terkumpul).toLocaleString('id-ID')}</span>
          <span>Target: Rp ${parseInt(k.target).toLocaleString('id-ID')}</span>
        </div>
      </div>`;
    }).join('');
  }

  try {
    const kasData = await kasRes.json();
    if (kasData.success) {
      const {masuk, keluar, saldo, donasi_count} = kasData.data;
      document.getElementById('kas-masuk').textContent = 'Rp ' + parseInt(masuk||0).toLocaleString('id-ID');
      document.getElementById('kas-keluar').textContent = 'Rp ' + parseInt(keluar||0).toLocaleString('id-ID');
      document.getElementById('kas-saldo').textContent = 'Rp ' + parseInt(saldo||0).toLocaleString('id-ID');
      document.getElementById('donasi-count').textContent = donasi_count || 0;
    }
  } catch(e) {}

  try {
    const pRes = await fetch('/api/pengeluaran/public');
    const pData = await pRes.json();
    if (pData.success && pData.data.length) {
      document.getElementById('pengeluaran-list').innerHTML = pData.data.map(p => {
        const tgl = new Date(p.tanggal).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});
        return `<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
          <div><div style="font-size:14px;font-weight:500;color:var(--ink)">${p.keterangan}</div><div style="font-size:12px;color:var(--ink-soft)">${p.judul||''} · ${tgl}</div></div>
          <div style="font-size:14px;font-weight:600;color:var(--rust)">- Rp ${parseInt(p.nominal).toLocaleString('id-ID')}</div>
        </div>`;
      }).join('');
    } else {
      document.getElementById('pengeluaran-list').innerHTML = '<div style="text-align:center;padding:1.5rem;color:var(--ink-soft)">Belum ada pengeluaran tercatat.</div>';
    }
  } catch(e) {}
}
loadLaporan();
</script>
@endsection
