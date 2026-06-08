@extends('layouts.admin')
@section('title', 'Laporan Kependudukan')
@section('content')
<style>
.lk-tab-bar { display:flex; gap:.375rem; margin-bottom:1.5rem; background:#fff; border:1px solid var(--border); border-radius:10px; padding:.3rem; width:fit-content; }
.lk-tab { padding:.45rem 1.125rem; border-radius:7px; border:none; background:transparent; font-size:13px; font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif; color:var(--ink-soft); transition:all .15s; }
.lk-tab.active { background:var(--forest); color:#fff; }

.lk-blok-card { background:#fff; border:1px solid var(--border); border-radius:12px; overflow:hidden; margin-bottom:1rem; }
.lk-blok-header { background:var(--cream); padding:.75rem 1.25rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); }
.lk-blok-title { font-family:'DM Serif Display',serif; font-size:1.1rem; color:var(--forest); }
.lk-stat-pill { font-size:11px; font-weight:600; padding:3px 10px; border-radius:99px; }

.lk-kk-row { padding:.875rem 1.25rem; border-bottom:1px solid var(--border); }
.lk-kk-row:last-child { border-bottom:none; }
.lk-kk-header { display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; }
.lk-kk-name { font-weight:600; font-size:14px; color:var(--ink); }
.lk-kk-meta { font-size:12px; color:var(--ink-soft); margin-top:2px; }
.lk-anggota-list { margin-top:.625rem; padding-top:.625rem; border-top:1px dashed var(--border); display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:.375rem; }
.lk-anggota-chip { font-size:11.5px; color:var(--ink-mid); padding:3px 8px; background:var(--cream); border-radius:6px; }
.lk-kend-chip { font-size:11px; color:var(--ink-soft); padding:2px 8px; background:#f5f4f2; border-radius:6px; font-family:monospace; }

.lk-summary-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:.875rem; margin-bottom:1.5rem; }
.lk-summary-card { background:#fff; border:1px solid var(--border); border-radius:10px; padding:1rem 1.125rem; }
.lk-summary-label { font-size:11px; color:var(--ink-soft); margin-bottom:4px; }
.lk-summary-val { font-family:'DM Serif Display',serif; font-size:1.6rem; color:var(--forest); }

@media print {
  .admin-sidebar, .admin-topbar, .no-print { display:none !important; }
  .admin-body { margin-left:0 !important; }
  .admin-content { padding:1rem !important; }
  .lk-tab-bar, .lk-tab { display:none !important; }
  .lk-blok-card { break-inside:avoid; border:1px solid #ccc; }
  .lk-kk-row { break-inside:avoid; }
}
</style>

<div class="admin-page-header no-print">
  <div>
    <div class="admin-page-title">Laporan Kependudukan</div>
    <div class="admin-page-sub">Ringkasan dan daftar lengkap warga RT 10 Golden Park 2</div>
  </div>
  <div style="display:flex;gap:.5rem;align-items:center">
    <select id="f-blok" onchange="onBlokChange()" style="padding:7px 14px;border:1.5px solid var(--border);border-radius:99px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;color:var(--ink);cursor:pointer">
      <option value="">Semua Blok</option>
      @foreach(range('A','Z') as $b)
      <option value="{{ $b }}">Blok {{ $b }}</option>
      @endforeach
    </select>
    <button onclick="printPage()" class="btn-secondary no-print" style="padding:8px 18px;font-size:13px">
      <i class="fa fa-print"></i> Cetak
    </button>
    <button onclick="exportExcel()" class="btn-primary no-print" style="padding:8px 18px;font-size:13px">
      <i class="fa fa-file-excel"></i> Export Excel
    </button>
  </div>
</div>

{{-- Summary cards --}}
<div class="lk-summary-grid" id="summary-cards">
  <div class="lk-summary-card"><div class="lk-summary-label">Total KK</div><div class="lk-summary-val" id="s-kk">—</div></div>
  <div class="lk-summary-card"><div class="lk-summary-label">Total Jiwa</div><div class="lk-summary-val" id="s-jiwa">—</div></div>
  <div class="lk-summary-card"><div class="lk-summary-label">Total Kendaraan</div><div class="lk-summary-val" id="s-kend">—</div></div>
  <div class="lk-summary-card"><div class="lk-summary-label">Status Tetap</div><div class="lk-summary-val" id="s-tetap">—</div></div>
  <div class="lk-summary-card"><div class="lk-summary-label">Kontrak/Kos</div><div class="lk-summary-val" id="s-kontrak">—</div></div>
</div>

{{-- Tabs --}}
<div class="lk-tab-bar no-print">
  <button class="lk-tab active" id="tab-btn-ringkasan" onclick="setTab('ringkasan')">Ringkasan per Blok</button>
  <button class="lk-tab" id="tab-btn-detail" onclick="setTab('detail')">Daftar KK Lengkap</button>
</div>

{{-- Ringkasan tab --}}
<div id="tab-ringkasan">
  <div id="ringkasan-content">
    <div style="text-align:center;padding:3rem;color:var(--ink-mute)">Memuat...</div>
  </div>
</div>

{{-- Detail tab --}}
<div id="tab-detail" style="display:none">
  <div id="detail-content">
    <div style="text-align:center;padding:3rem;color:var(--ink-mute)">Memuat...</div>
  </div>
</div>

<script>
const _csrfT = '{{ csrf_token() }}';
let _curTab = 'ringkasan', _blok = '', _ringkasanData = null, _detailLoaded = false;

async function loadRingkasan() {
  const r = await fetch('/api/kependudukan/laporan', { headers: { 'X-CSRF-TOKEN': _csrfT } });
  const j = await r.json();
  if (!j.success) return;
  _ringkasanData = j.data;
  renderSummary(j.data);
  renderRingkasan(j.data);
}

function renderSummary(d) {
  let kk = d.totalKK, jiwa = d.totalJiwa, kend = d.totalKend;
  let tetap = 0, kontrak = 0;

  if (_blok) {
    const blokData = d.perBlok.find(b => b.blok === _blok.toUpperCase());
    if (blokData) {
      kk = blokData.jumlah_kk;
      jiwa = blokData.jumlah_jiwa;
      kend = blokData.jumlah_kendaraan;
      tetap = blokData.tetap;
      kontrak = (blokData.kontrak || 0) + (blokData.kos || 0);
    }
  } else {
    tetap = d.statusTinggal?.tetap || 0;
    kontrak = (d.statusTinggal?.kontrak || 0) + (d.statusTinggal?.kos || 0);
  }

  document.getElementById('s-kk').textContent    = kk;
  document.getElementById('s-jiwa').textContent   = jiwa;
  document.getElementById('s-kend').textContent   = kend;
  document.getElementById('s-tetap').textContent  = tetap;
  document.getElementById('s-kontrak').textContent = kontrak;
}

function renderRingkasan(d) {
  const blokData = _blok
    ? d.perBlok.filter(b => b.blok === _blok.toUpperCase())
    : d.perBlok;

  if (!blokData.length) {
    document.getElementById('ringkasan-content').innerHTML =
      '<div style="text-align:center;padding:3rem;color:var(--ink-mute)">Tidak ada data.</div>';
    return;
  }

  document.getElementById('ringkasan-content').innerHTML = `
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden">
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
          <tr style="background:var(--cream);border-bottom:1px solid var(--border)">
            <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Blok</th>
            <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Jumlah KK</th>
            <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Total Jiwa</th>
            <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Kendaraan</th>
            <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Tetap</th>
            <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Kontrak</th>
            <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Kos</th>
          </tr>
        </thead>
        <tbody>
          ${blokData.map(b => `
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:11px 16px;font-family:'DM Serif Display',serif;font-size:1rem;color:var(--forest)">Blok ${escHtml(b.blok)}</td>
              <td style="padding:11px 16px;text-align:center;font-weight:600">${b.jumlah_kk}</td>
              <td style="padding:11px 16px;text-align:center;font-weight:600">${b.jumlah_jiwa}</td>
              <td style="padding:11px 16px;text-align:center;color:var(--ink-soft)">${b.jumlah_kendaraan}</td>
              <td style="padding:11px 16px;text-align:center"><span style="background:var(--forest-pale);color:var(--forest);font-size:11px;font-weight:700;padding:2px 9px;border-radius:99px">${b.tetap||0}</span></td>
              <td style="padding:11px 16px;text-align:center"><span style="background:var(--gold-pale);color:var(--gold-dark);font-size:11px;font-weight:700;padding:2px 9px;border-radius:99px">${b.kontrak||0}</span></td>
              <td style="padding:11px 16px;text-align:center"><span style="background:#efefed;color:var(--ink-soft);font-size:11px;font-weight:700;padding:2px 9px;border-radius:99px">${b.kos||0}</span></td>
            </tr>
          `).join('')}
        </tbody>
        <tfoot>
          <tr style="background:var(--cream);font-weight:700">
            <td style="padding:10px 16px;font-weight:700">Total</td>
            <td style="padding:10px 16px;text-align:center">${blokData.reduce((s,b)=>s+b.jumlah_kk,0)}</td>
            <td style="padding:10px 16px;text-align:center">${blokData.reduce((s,b)=>s+b.jumlah_jiwa,0)}</td>
            <td style="padding:10px 16px;text-align:center">${blokData.reduce((s,b)=>s+b.jumlah_kendaraan,0)}</td>
            <td style="padding:10px 16px;text-align:center">${blokData.reduce((s,b)=>s+(b.tetap||0),0)}</td>
            <td style="padding:10px 16px;text-align:center">${blokData.reduce((s,b)=>s+(b.kontrak||0),0)}</td>
            <td style="padding:10px 16px;text-align:center">${blokData.reduce((s,b)=>s+(b.kos||0),0)}</td>
          </tr>
        </tfoot>
      </table>
    </div>`;
}

async function loadDetail() {
  const url = '/api/kependudukan/laporan/detail' + (_blok ? '?blok=' + _blok : '');
  document.getElementById('detail-content').innerHTML =
    '<div style="text-align:center;padding:3rem;color:var(--ink-mute)">Memuat...</div>';
  const r = await fetch(url, { headers: { 'X-CSRF-TOKEN': _csrfT } });
  const j = await r.json();
  if (!j.success) return;

  if (!j.data.length) {
    document.getElementById('detail-content').innerHTML =
      '<div style="text-align:center;padding:3rem;color:var(--ink-mute)">Tidak ada data.</div>';
    return;
  }

  // Group by blok
  const byBlok = {};
  j.data.forEach(kk => {
    const b = kk.unit_rumah?.blok || '?';
    if (!byBlok[b]) byBlok[b] = [];
    byBlok[b].push(kk);
  });

  const statusLabel = { tetap:'Tetap', kontrak:'Kontrak', kos:'Kos' };
  const jenisLabel  = { L:'Laki-laki', P:'Perempuan' };

  document.getElementById('detail-content').innerHTML = Object.entries(byBlok)
    .sort(([a],[b]) => a.localeCompare(b))
    .map(([blok, rows]) => `
      <div class="lk-blok-card">
        <div class="lk-blok-header">
          <div class="lk-blok-title">Blok ${escHtml(blok)}</div>
          <span class="lk-stat-pill" style="background:var(--forest-pale);color:var(--forest)">${rows.length} KK</span>
        </div>
        ${rows.map(kk => `
          <div class="lk-kk-row">
            <div class="lk-kk-header">
              <div>
                <div class="lk-kk-name">${escHtml(kk.nama)}</div>
                <div class="lk-kk-meta">
                  Unit ${kk.unit_rumah ? `Blok ${kk.unit_rumah.blok}-${kk.unit_rumah.nomor}` : '—'}
                  ${kk.nik ? ' · NIK: ' + kk.nik : ''}
                  ${kk.no_wa ? ' · <i class="fa fa-whatsapp" style="color:#25D366"></i> ' + escHtml(kk.no_wa) : ''}
                </div>
              </div>
              <div style="text-align:right;flex-shrink:0">
                <span class="lk-stat-pill" style="background:${kk.status_tinggal==='tetap'?'var(--forest-pale)':kk.status_tinggal==='kontrak'?'var(--gold-pale)':'#efefed'};color:${kk.status_tinggal==='tetap'?'var(--forest)':kk.status_tinggal==='kontrak'?'var(--gold-dark)':'var(--ink-soft)'}">
                  ${statusLabel[kk.status_tinggal] || kk.status_tinggal || '—'}
                </span>
                <div style="font-size:11px;color:var(--ink-mute);margin-top:3px">${(kk.anggota_keluarga?.length||0)+1} jiwa · ${kk.kendaraan?.length||0} kendaraan</div>
              </div>
            </div>
            ${kk.anggota_keluarga?.length ? `
              <div class="lk-anggota-list">
                ${kk.anggota_keluarga.map(ag => `
                  <div class="lk-anggota-chip">
                    ${escHtml(ag.nama)}
                    <span style="color:var(--ink-mute)">${ag.hubungan ? ' · '+ag.hubungan : ''}${ag.jenis_kelamin ? ' · '+jenisLabel[ag.jenis_kelamin] : ''}</span>
                  </div>
                `).join('')}
              </div>
            ` : ''}
            ${kk.kendaraan?.length ? `
              <div style="margin-top:.5rem;display:flex;flex-wrap:wrap;gap:.3rem">
                ${kk.kendaraan.map(kend => `
                  <span class="lk-kend-chip">${escHtml(kend.plat_nomor)} <span style="font-family:sans-serif;font-size:10px;color:var(--ink-mute)">${escHtml(kend.merek||'')}</span></span>
                `).join('')}
              </div>
            ` : ''}
          </div>
        `).join('')}
      </div>
    `).join('');
}

function setTab(tab) {
  _curTab = tab;
  document.getElementById('tab-ringkasan').style.display = tab === 'ringkasan' ? '' : 'none';
  document.getElementById('tab-detail').style.display    = tab === 'detail'    ? '' : 'none';
  document.getElementById('tab-btn-ringkasan').classList.toggle('active', tab === 'ringkasan');
  document.getElementById('tab-btn-detail').classList.toggle('active', tab === 'detail');
  if (tab === 'detail' && !_detailLoaded) { _detailLoaded = true; loadDetail(); }
}

function onBlokChange() {
  _blok = document.getElementById('f-blok').value;
  _detailLoaded = false;
  loadRingkasan();
  if (_curTab === 'detail') { _detailLoaded = true; loadDetail(); }
}

function printPage() { window.print(); }

function exportExcel() {
  const url = '/api/kependudukan/laporan/export' + (_blok ? '?blok=' + _blok : '');
  window.location.href = url;
}

function escHtml(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

loadRingkasan();
</script>
@endsection
