@extends('layouts.admin')
@section('title', 'Data Kependudukan')
@section('content')
<div class="container">

  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Kependudukan</div>
      <div style="font-size:13px;color:var(--ink-soft);margin-top:4px">Data warga RT 10 Golden Park 2</div>
    </div>
    <a href="/admin/kependudukan/warga" class="btn-primary">
      <i class="fa fa-users"></i> Data Warga
    </a>
  </div>

  {{-- KPI --}}
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem">
    <div style="background:var(--forest-pale);border-radius:var(--radius-sm);padding:1.25rem 1.5rem">
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--forest);opacity:0.7;margin-bottom:4px">Total KK</div>
      <div style="font-family:'DM Serif Display',serif;font-size:2rem;color:var(--forest)" id="kpi-kk">—</div>
    </div>
    <div style="background:var(--gold-pale);border-radius:var(--radius-sm);padding:1.25rem 1.5rem">
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#7A5C00;opacity:0.7;margin-bottom:4px">Total Jiwa</div>
      <div style="font-family:'DM Serif Display',serif;font-size:2rem;color:#7A5C00" id="kpi-jiwa">—</div>
    </div>
    <div style="background:#EDF3FB;border-radius:var(--radius-sm);padding:1.25rem 1.5rem">
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#2D5AA8;opacity:0.7;margin-bottom:4px">Total Kendaraan</div>
      <div style="font-family:'DM Serif Display',serif;font-size:2rem;color:#2D5AA8" id="kpi-kend">—</div>
    </div>
  </div>

  {{-- Per Blok --}}
  <div class="card">
    <div style="font-size:14px;font-weight:600;margin-bottom:1.25rem">Distribusi Warga per Blok</div>
    <div id="blok-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:0.75rem">
      <div style="text-align:center;padding:2rem;color:var(--ink-mute);font-size:13px;grid-column:1/-1">Memuat...</div>
    </div>
  </div>

  {{-- Quick links --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1.5rem">
    <a href="/admin/kependudukan/warga" class="card" style="display:flex;align-items:center;gap:1rem;text-decoration:none;transition:box-shadow 0.2s" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow=''">
      <div style="width:44px;height:44px;border-radius:12px;background:var(--forest-pale);display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="fa fa-users" style="color:var(--forest);font-size:18px"></i>
      </div>
      <div>
        <div style="font-size:14px;font-weight:600;color:var(--ink)">Data Warga & KK</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">Kelola kepala keluarga, anggota, dan kendaraan</div>
      </div>
      <i class="fa fa-chevron-right" style="color:var(--ink-mute);margin-left:auto;font-size:12px"></i>
    </a>
    <a href="/admin/kependudukan/warga?tambah=1" class="card" style="display:flex;align-items:center;gap:1rem;text-decoration:none;transition:box-shadow 0.2s" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow=''">
      <div style="width:44px;height:44px;border-radius:12px;background:var(--gold-pale);display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="fa fa-user-plus" style="color:var(--gold-dark);font-size:18px"></i>
      </div>
      <div>
        <div style="font-size:14px;font-weight:600;color:var(--ink)">Tambah KK Baru</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">Daftarkan kepala keluarga baru</div>
      </div>
      <i class="fa fa-chevron-right" style="color:var(--ink-mute);margin-left:auto;font-size:12px"></i>
    </a>
  </div>

</div>
@endsection
@section('scripts')
<script>
async function loadStats() {
  try {
    const r = await fetch('/api/kependudukan/stats');
    const j = await r.json();
    if (!j.success) return;
    const d = j.data;
    document.getElementById('kpi-kk').textContent   = d.totalKK;
    document.getElementById('kpi-jiwa').textContent  = d.totalJiwa;
    document.getElementById('kpi-kend').textContent  = d.totalKend;

    if (d.perBlok && d.perBlok.length) {
      document.getElementById('blok-grid').innerHTML = d.perBlok.map(b => `
        <div style="background:var(--warm);border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem;text-align:center">
          <div style="font-family:'DM Serif Display',serif;font-size:1.75rem;color:var(--forest)">${b.jumlah}</div>
          <div style="font-size:11px;font-weight:600;color:var(--ink-soft);margin-top:2px">Blok ${b.blok}</div>
        </div>
      `).join('');
    } else {
      document.getElementById('blok-grid').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-mute);font-size:13px;grid-column:1/-1">Belum ada data</div>';
    }
  } catch(e) { console.error(e); }
}
loadStats();
</script>
@endsection
