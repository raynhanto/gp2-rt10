@extends('layouts.admin')
@section('title', 'Dashboard Keuangan')
@section('content')
<div class="container">

  {{-- Header --}}
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <a href="/admin" style="font-size:13px;color:var(--ink-soft)">← Admin</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Keuangan</div>
      <div style="font-size:13px;color:var(--ink-soft);margin-top:4px">Dashboard keuangan RT 10 Golden Park 2</div>
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="location.href='/admin/keuangan/laporan'" class="btn-secondary" style="padding:9px 18px"><i class="fa-solid fa-chart-bar" style="margin-right:6px"></i>Laporan</button>
      <button onclick="location.href='/admin/keuangan/gsheet'" class="btn-secondary" style="padding:9px 18px"><i class="fa-solid fa-link" style="margin-right:6px"></i>G-Sheet</button>
    </div>
  </div>

  {{-- Subnav --}}
  <div style="display:flex;gap:6px;margin-bottom:2rem;flex-wrap:wrap">
    @foreach(['/admin/keuangan'=>'Dashboard','/admin/keuangan/kas'=>'Kas','/admin/keuangan/pengeluaran'=>'Pengeluaran','/admin/keuangan/anggaran'=>'Anggaran','/admin/keuangan/iuran'=>'Iuran','/admin/keuangan/iuran/matrix'=>'Matrix Iuran','/admin/keuangan/kategori'=>'Kategori','/admin/keuangan/laporan'=>'Laporan','/admin/keuangan/gsheet'=>'G-Sheet'] as $url=>$lbl)
    <a href="{{ $url }}" style="font-size:12px;padding:6px 14px;border-radius:100px;border:1.5px solid {{ request()->is(ltrim($url,'/')) ? 'var(--forest)' : 'var(--border)' }};background:{{ request()->is(ltrim($url,'/')) ? 'var(--forest)' : '#fff' }};color:{{ request()->is(ltrim($url,'/')) ? '#fff' : 'var(--ink-soft)' }};font-weight:500;text-decoration:none">{{ $lbl }}</a>
    @endforeach
  </div>

  {{-- KPI Cards --}}
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem" id="kpi-grid">
    @foreach(['kpi-saldo'=>['Saldo Kas','var(--gold-pale)','#7A5C00'],'kpi-masuk'=>['Total Masuk YTD','#E8F4ED','var(--forest)'],'kpi-keluar'=>['Total Keluar YTD','#FDECEA','var(--rust)'],'kpi-masuk-bulan'=>['Masuk Bulan Ini','#EDF3FB','#2D5AA8'],'kpi-keluar-bulan'=>['Keluar Bulan Ini','#FFF3E0','#8B4000'],'kpi-iuran'=>['Iuran Compliance','#F0F7F3','var(--forest)'] ] as $id=>[$lbl,$bg,$col])
    <div style="background:{{ $bg }};border-radius:var(--radius-sm);padding:1.25rem 1.5rem">
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:{{ $col }};margin-bottom:4px;opacity:0.7">{{ $lbl }}</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.6rem;color:{{ $col }}" id="{{ $id }}">—</div>
    </div>
    @endforeach
  </div>

  {{-- Charts Row 1 --}}
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
        <div style="font-size:14px;font-weight:600">Arus Kas Bulanan</div>
        <select id="tahun-bulanan" style="font-size:12px;padding:4px 10px;border:1px solid var(--border);border-radius:6px;background:#fff">
          @for($y = date('Y'); $y >= 2024; $y--)<option value="{{ $y }}">{{ $y }}</option>@endfor
        </select>
      </div>
      <div style="position:relative;height:220px"><canvas id="chart-bulanan"></canvas></div>
    </div>
    <div class="card">
      <div style="font-size:14px;font-weight:600;margin-bottom:1rem">Pengeluaran per Kategori</div>
      <div style="position:relative;height:220px"><canvas id="chart-kategori"></canvas></div>
      <div id="legend-kategori" style="margin-top:0.75rem;font-size:11px"></div>
    </div>
  </div>

  {{-- Charts Row 2 --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
        <div style="font-size:14px;font-weight:600">Compliance Iuran</div>
        <select id="tahun-iuran" style="font-size:12px;padding:4px 10px;border:1px solid var(--border);border-radius:6px;background:#fff">
          @for($y = date('Y'); $y >= 2024; $y--)<option value="{{ $y }}">{{ $y }}</option>@endfor
        </select>
      </div>
      <div style="position:relative;height:200px"><canvas id="chart-iuran"></canvas></div>
    </div>
    <div class="card">
      <div style="font-size:14px;font-weight:600;margin-bottom:1rem">Tren Saldo Kas (12 Bulan)</div>
      <div style="position:relative;height:200px"><canvas id="chart-saldo"></canvas></div>
    </div>
  </div>

</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const bulanLabel = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
let chartBulanan, chartKategori, chartIuran, chartSaldo;

function rp(n) { return 'Rp '+(parseInt(n)||0).toLocaleString('id-ID'); }
function pct(n) { return (parseFloat(n)||0).toFixed(1)+'%'; }
function chartErr(canvasId, msg) {
  const el = document.getElementById(canvasId);
  if (!el) return;
  const wrap = el.parentElement;
  el.style.display = 'none';
  if (!wrap.querySelector('.chart-err')) {
    const d = document.createElement('div');
    d.className = 'chart-err';
    d.style = 'padding:2rem;text-align:center;font-size:12px;color:var(--ink-mute)';
    d.textContent = msg || 'Gagal memuat data';
    wrap.appendChild(d);
  }
}

async function loadKPI() {
  try {
    const r = await fetch('/api/statistik/dashboard');
    const j = await r.json();
    if (!j.success) return;
    const d = j.data;
    document.getElementById('kpi-saldo').textContent        = rp(d.saldo);
    document.getElementById('kpi-masuk').textContent        = rp(d.masuk_ytd);
    document.getElementById('kpi-keluar').textContent       = rp(d.keluar_ytd);
    document.getElementById('kpi-masuk-bulan').textContent  = rp(d.masuk_bulan);
    document.getElementById('kpi-keluar-bulan').textContent = rp(d.keluar_bulan);
    document.getElementById('kpi-iuran').textContent        = pct(d.iuran_compliance) + ' (' + d.iuran_lunas + '/' + d.iuran_total + ')';
  } catch(e) { console.error('loadKPI:', e); }
}

async function loadBulanan(tahun) {
  try {
    const r = await fetch('/api/statistik/bulanan?tahun=' + tahun);
    const j = await r.json();
    if (!j.success) { chartErr('chart-bulanan', j.message); return; }
    const labels = j.data.map(d => bulanLabel[d.bulan]);
    const masuk  = j.data.map(d => d.masuk);
    const keluar = j.data.map(d => d.keluar);
    if (chartBulanan) chartBulanan.destroy();
    chartBulanan = new Chart(document.getElementById('chart-bulanan'), {
      type: 'bar',
      data: { labels, datasets: [
        { label: 'Masuk',  data: masuk,  backgroundColor: '#3D7A5620', borderColor: '#3D7A56', borderWidth: 2 },
        { label: 'Keluar', data: keluar, backgroundColor: '#B5401A20', borderColor: '#B5401A', borderWidth: 2 },
      ]},
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
        scales: { y: { ticks: { callback: v => 'Rp '+(v/1000000).toFixed(1)+'jt', font: { size: 10 } } } }
      }
    });
  } catch(e) { console.error('loadBulanan:', e); chartErr('chart-bulanan', 'Gagal memuat grafik'); }
}

async function loadKategori() {
  try {
    const dari   = new Date().getFullYear() + '-01-01';
    const sampai = new Date().toISOString().slice(0, 10);
    const r = await fetch(`/api/statistik/kategori?dari=${dari}&sampai=${sampai}`);
    const j = await r.json();
    if (!j.success) { chartErr('chart-kategori', j.message); return; }
    if (!j.data || !j.data.length) {
      chartErr('chart-kategori', 'Belum ada data pengeluaran');
      return;
    }
    const top = j.data.slice(0, 8);
    if (chartKategori) chartKategori.destroy();
    chartKategori = new Chart(document.getElementById('chart-kategori'), {
      type: 'doughnut',
      data: {
        labels: top.map(d => d.kategori),
        datasets: [{ data: top.map(d => d.total), backgroundColor: top.map(d => d.warna + '90'), borderColor: top.map(d => d.warna), borderWidth: 2 }]
      },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '65%' }
    });
    document.getElementById('legend-kategori').innerHTML = top.map(d =>
      `<div style="display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px solid var(--border)">
        <span style="display:flex;align-items:center;gap:6px">
          <span style="width:10px;height:10px;border-radius:50%;background:${d.warna};flex-shrink:0"></span>
          <span>${d.kategori}</span>
        </span>
        <span style="font-weight:600;color:var(--rust)">Rp ${parseInt(d.total).toLocaleString('id-ID')}</span>
      </div>`
    ).join('');
  } catch(e) { console.error('loadKategori:', e); chartErr('chart-kategori', 'Gagal memuat grafik'); }
}

async function loadIuran(tahun) {
  try {
    const r = await fetch('/api/statistik/iuran-compliance?tahun=' + tahun);
    const j = await r.json();
    if (!j.success) { chartErr('chart-iuran', j.message); return; }
    if (!j.data || !j.data.length) {
      chartErr('chart-iuran', 'Belum ada data iuran tahun ' + tahun);
      return;
    }
    const labels = j.data.map(d => bulanLabel[d.bulan]);
    const pcts   = j.data.map(d => d.pct);
    if (chartIuran) chartIuran.destroy();
    chartIuran = new Chart(document.getElementById('chart-iuran'), {
      type: 'bar',
      data: { labels, datasets: [{
        label: '% Lunas', data: pcts,
        backgroundColor: pcts.map(p => p >= 80 ? '#E8F4ED' : p >= 50 ? '#FFF3CD' : '#FDECEA'),
        borderColor:     pcts.map(p => p >= 80 ? '#3D7A56' : p >= 50 ? '#C8A030' : '#B5401A'),
        borderWidth: 2
      }]},
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { min: 0, max: 100, ticks: { callback: v => v + '%', font: { size: 10 } } } }
      }
    });
  } catch(e) { console.error('loadIuran:', e); chartErr('chart-iuran', 'Gagal memuat grafik'); }
}

async function loadSaldo() {
  try {
    const r = await fetch('/api/statistik/saldo-trend?bulan=12');
    const j = await r.json();
    if (!j.success) { chartErr('chart-saldo', j.message); return; }
    if (!j.data || !j.data.length) {
      chartErr('chart-saldo', 'Belum ada data kas');
      return;
    }
    if (chartSaldo) chartSaldo.destroy();
    chartSaldo = new Chart(document.getElementById('chart-saldo'), {
      type: 'line',
      data: {
        labels: j.data.map(d => d.periode),
        datasets: [{ label: 'Saldo Kas', data: j.data.map(d => d.saldo),
          borderColor: '#1A3D2B', backgroundColor: '#1A3D2B20',
          fill: true, tension: 0.4, pointRadius: 4 }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { ticks: { callback: v => 'Rp '+(v/1000000).toFixed(1)+'jt', font: { size: 10 } } } }
      }
    });
  } catch(e) { console.error('loadSaldo:', e); chartErr('chart-saldo', 'Gagal memuat grafik'); }
}

document.getElementById('tahun-bulanan').addEventListener('change', e => loadBulanan(e.target.value));
document.getElementById('tahun-iuran').addEventListener('change', e => loadIuran(e.target.value));

const tahunNow = new Date().getFullYear();
loadKPI();
loadBulanan(tahunNow);
loadKategori();
loadIuran(tahunNow);
loadSaldo();
</script>
@endsection
