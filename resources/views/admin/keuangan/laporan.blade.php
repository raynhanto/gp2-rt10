@extends('layouts.admin')
@section('title', 'Laporan Keuangan')
@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <a href="/admin/keuangan" style="font-size:13px;color:var(--ink-soft)">← Keuangan</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Laporan Keuangan</div>
    </div>
    <button onclick="window.print()" class="btn-secondary" style="padding:9px 18px;font-size:13px">🖨️ Print</button>
  </div>

  {{-- Tabs --}}
  <div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:1.5rem">
    <button onclick="showTab('arus')"       id="tab-arus"       class="tab-btn tab-active">Arus Kas</button>
    <button onclick="showTab('pengeluaran')" id="tab-pengeluaran" class="tab-btn">Pengeluaran</button>
    <button onclick="showTab('iuran')"      id="tab-iuran"      class="tab-btn">Iuran</button>
    <button onclick="showTab('neraca')"     id="tab-neraca"     class="tab-btn">Neraca</button>
  </div>

  {{-- Arus Kas --}}
  <div id="panel-arus">
    <div style="display:flex;gap:8px;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap">
      <label style="font-size:13px">Tahun:</label>
      <select id="ak-tahun" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none">
        @for($y = date('Y'); $y >= 2024; $y--)<option value="{{ $y }}" @if($y == date('Y')) selected @endif>{{ $y }}</option>@endfor
      </select>
      <button onclick="loadArusKas()" class="btn-secondary" style="padding:7px 14px;font-size:13px">Tampilkan</button>
      <a id="export-ak" href="#" onclick="exportKas()" class="btn-secondary" style="padding:7px 14px;font-size:13px">↓ Excel</a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem" id="ak-summary"></div>
    <div class="card" style="margin-bottom:1.5rem">
      <canvas id="chart-arus" height="200"></canvas>
    </div>
    <div id="ak-table"></div>
  </div>

  {{-- Pengeluaran --}}
  <div id="panel-pengeluaran" style="display:none">
    <div style="display:flex;gap:8px;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap">
      <label style="font-size:13px">Tahun:</label>
      <select id="pe-tahun" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none">
        @for($y = date('Y'); $y >= 2024; $y--)<option value="{{ $y }}" @if($y == date('Y')) selected @endif>{{ $y }}</option>@endfor
      </select>
      <button onclick="loadPengeluaranReport()" class="btn-secondary" style="padding:7px 14px;font-size:13px">Tampilkan</button>
      <button onclick="exportPengeluaran()" class="btn-secondary" style="padding:7px 14px;font-size:13px">↓ Excel</button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
      <div class="card">
        <div style="font-size:14px;font-weight:600;margin-bottom:1rem">Per Kategori</div>
        <canvas id="chart-pengeluaran" height="250"></canvas>
        <div id="pe-legend" style="margin-top:0.75rem;font-size:12px"></div>
      </div>
      <div class="card">
        <div style="font-size:14px;font-weight:600;margin-bottom:1rem">Per Bulan</div>
        <canvas id="chart-pe-bulan" height="250"></canvas>
      </div>
    </div>
    <div id="pe-detail" style="margin-top:1.5rem"></div>
  </div>

  {{-- Iuran --}}
  <div id="panel-iuran" style="display:none">
    <div style="display:flex;gap:8px;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap">
      <label style="font-size:13px">Tahun:</label>
      <select id="iu-tahun" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none">
        @for($y = date('Y'); $y >= 2024; $y--)<option value="{{ $y }}" @if($y == date('Y')) selected @endif>{{ $y }}</option>@endfor
      </select>
      <button onclick="loadIuranReport()" class="btn-secondary" style="padding:7px 14px;font-size:13px">Tampilkan</button>
      <button onclick="exportIuran()" class="btn-secondary" style="padding:7px 14px;font-size:13px">↓ Excel</button>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem" id="iu-summary"></div>
    <div id="iu-table"></div>
  </div>

  {{-- Neraca --}}
  <div id="panel-neraca" style="display:none">
    <div style="display:flex;gap:8px;align-items:center;margin-bottom:1.5rem">
      <label style="font-size:13px">Per Tanggal:</label>
      <input type="date" id="nc-per" value="{{ date('Y-m-d') }}" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
      <button onclick="loadNeraca()" class="btn-secondary" style="padding:7px 14px;font-size:13px">Tampilkan</button>
    </div>
    <div id="nc-content"></div>
  </div>

</div>
<style>
.tab-btn{padding:10px 20px;border:none;background:none;font-size:13px;font-weight:500;color:var(--ink-soft);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px}
.tab-active{color:var(--forest);border-bottom-color:var(--forest)!important}
@media print { nav,footer,.no-print{display:none!important} main{padding:1rem 0!important} }
</style>
@endsection
@section('scripts')
<script>
const bulanLabel=['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
let chartArus, chartPe, chartPeBulan;

function rp(n){ return 'Rp '+(parseInt(n)||0).toLocaleString('id-ID'); }
function showTab(t) {
  ['arus','pengeluaran','iuran','neraca'].forEach(id=>{
    document.getElementById('panel-'+id).style.display=id===t?'block':'none';
    document.getElementById('tab-'+id).classList.toggle('tab-active',id===t);
  });
  if(t==='arus')        loadArusKas();
  if(t==='pengeluaran') loadPengeluaranReport();
  if(t==='iuran')       loadIuranReport();
  if(t==='neraca')      loadNeraca();
}

async function loadArusKas() {
  const tahun = document.getElementById('ak-tahun').value;
  const r = await fetch('/api/laporan/arus-kas?tahun=' + tahun);
  const j = await r.json();
  if (!j.success) return;

  const totals = j.totals;
  document.getElementById('ak-summary').innerHTML = [
    [rp(totals.total_masuk),'Total Masuk','#E8F4ED','var(--forest)'],
    [rp(totals.total_keluar),'Total Keluar','#FDECEA','var(--rust)'],
    [rp(totals.total_masuk-totals.total_keluar),'Saldo Kas','var(--gold-pale)','#7A5C00'],
  ].map(([val,lbl,bg,col])=>`<div style="background:${bg};border-radius:var(--radius-sm);padding:1rem 1.25rem">
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:${col};opacity:.7;margin-bottom:4px">${lbl}</div>
    <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:${col}">${val}</div>
  </div>`).join('');

  if (chartArus) chartArus.destroy();
  const ctx = document.getElementById('chart-arus').getContext('2d');
  chartArus = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: j.data.map(d=>bulanLabel[d.bulan]),
      datasets: [
        { label:'Masuk', data:j.data.map(d=>d.masuk), backgroundColor:'#3D7A5620', borderColor:'#3D7A56', borderWidth:2 },
        { label:'Keluar', data:j.data.map(d=>d.keluar), backgroundColor:'#B5401A20', borderColor:'#B5401A', borderWidth:2 },
      ]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } }, scales:{ y:{ ticks:{ callback: v=>'Rp '+(v/1e6).toFixed(1)+'jt' } } } }
  });

  document.getElementById('ak-table').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">${['Bulan','Masuk','Keluar','Net','Saldo Kumulatif','Transaksi'].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}</tr></thead>
      <tbody>${j.data.map(d=>{
        const net = d.masuk - d.keluar;
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:10px 16px;font-size:13px;font-weight:500">${bulanLabel[d.bulan]} ${d.tahun}</td>
          <td style="padding:10px 16px;font-size:13px;color:var(--forest)">${rp(d.masuk)}</td>
          <td style="padding:10px 16px;font-size:13px;color:var(--rust)">${rp(d.keluar)}</td>
          <td style="padding:10px 16px;font-size:13px;font-weight:600;color:${net>=0?'var(--forest)':'var(--rust)'}">${net>=0?'+':''}${rp(net)}</td>
          <td style="padding:10px 16px;font-size:13px;font-weight:600">${rp(d.saldo_running)}</td>
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${d.transaksi}</td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;
}

async function loadPengeluaranReport() {
  const tahun = document.getElementById('pe-tahun').value;
  const r = await fetch('/api/laporan/pengeluaran?tahun=' + tahun);
  const j = await r.json();
  if (!j.success) return;

  if (chartPe) chartPe.destroy();
  chartPe = new Chart(document.getElementById('chart-pengeluaran').getContext('2d'), {
    type: 'doughnut',
    data: {
      labels: j.by_kategori.map(k=>k.kategori),
      datasets: [{ data:j.by_kategori.map(k=>k.total), backgroundColor:j.by_kategori.map(k=>k.warna+'80'), borderColor:j.by_kategori.map(k=>k.warna), borderWidth:2 }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, cutout:'60%' }
  });
  document.getElementById('pe-legend').innerHTML = j.by_kategori.map(k=>`<div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid var(--border)">
    <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;border-radius:50%;background:${k.warna};flex-shrink:0"></span>${k.kategori}</span>
    <span style="font-weight:600;color:var(--rust)">${rp(k.total)}</span>
  </div>`).join('');

  const bulanData = Array.from({length:12},(_,i)=>(j.by_bulan[i+1]?.total||0));
  if (chartPeBulan) chartPeBulan.destroy();
  chartPeBulan = new Chart(document.getElementById('chart-pe-bulan').getContext('2d'), {
    type: 'bar',
    data: {
      labels: bulanLabel.slice(1),
      datasets: [{ label:'Pengeluaran', data:bulanData, backgroundColor:'#B5401A20', borderColor:'#B5401A', borderWidth:2 }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{ticks:{callback:v=>'Rp '+(v/1e6).toFixed(1)+'jt'}}} }
  });

  const total = j.detail.reduce((s,d)=>s+parseInt(d.nominal),0);
  document.getElementById('pe-detail').innerHTML = `<div style="font-weight:600;font-size:13px;color:var(--rust);text-align:right;margin-bottom:6px">Total: ${rp(total)}</div>
    <div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">${['Tanggal','Keterangan','Kategori','Kampanye','Nominal'].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}</tr></thead>
      <tbody>${j.detail.map(d=>`<tr style="border-top:1px solid var(--border)">
        <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${d.tanggal}</td>
        <td style="padding:10px 16px;font-size:13px">${d.keterangan}</td>
        <td style="padding:10px 16px">${d.kategori?`<span style="font-size:11px;padding:2px 8px;border-radius:99px;background:${d.warna}22;color:${d.warna};border:1px solid ${d.warna}55">${d.kategori}</span>`:'—'}</td>
        <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${d.judul||'—'}</td>
        <td style="padding:10px 16px;font-size:13px;font-weight:600;color:var(--rust)">${rp(d.nominal)}</td>
      </tr>`).join('')}</tbody>
    </table>
  </div>`;
}

async function loadIuranReport() {
  const tahun = document.getElementById('iu-tahun').value;
  const r = await fetch('/api/laporan/iuran?tahun=' + tahun);
  const j = await r.json();
  if (!j.success) return;

  document.getElementById('iu-summary').innerHTML = [
    [j.total_tagihan,'Total Tagihan','#EDF3FB','#2D5AA8'],
    [j.total_lunas,'Lunas','#E8F4ED','var(--forest)'],
    [j.compliance_pct+'%','Compliance Rate','#E8F4ED','var(--forest)'],
    [rp(j.total_terkumpul),'Total Terkumpul','var(--gold-pale)','#7A5C00'],
  ].map(([val,lbl,bg,col])=>`<div style="background:${bg};border-radius:var(--radius-sm);padding:1rem 1.25rem">
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:${col};opacity:.7;margin-bottom:4px">${lbl}</div>
    <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:${col}">${val}</div>
  </div>`).join('');

  document.getElementById('iu-table').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">${['Bulan','Nominal/Unit','Total Unit','Lunas','Pending','Belum','Dispensasi','Terkumpul','Compliance'].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}</tr></thead>
      <tbody>${j.data.map(d=>{
        const pct = d.total>0 ? Math.round(d.lunas/d.total*100) : 0;
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:10px 16px;font-size:13px;font-weight:500">${bulanLabel[d.bulan]}</td>
          <td style="padding:10px 16px;font-size:13px">${rp(d.nominal)}</td>
          <td style="padding:10px 16px;font-size:13px">${d.total}</td>
          <td style="padding:10px 16px;font-size:13px;font-weight:600;color:var(--forest)">${d.lunas}</td>
          <td style="padding:10px 16px;font-size:13px;color:#C8A030">${d.pending}</td>
          <td style="padding:10px 16px;font-size:13px;color:var(--rust)">${d.belum}</td>
          <td style="padding:10px 16px;font-size:13px;color:var(--ink-soft)">${d.dispensasi}</td>
          <td style="padding:10px 16px;font-size:13px;font-weight:600">${rp(d.terkumpul)}</td>
          <td style="padding:10px 16px">
            <div style="display:flex;align-items:center;gap:8px">
              <div style="height:6px;background:var(--parchment);border-radius:99px;width:60px"><div style="height:100%;border-radius:99px;background:${pct>=80?'var(--forest)':pct>=50?'var(--gold)':'var(--rust)'};width:${pct}%"></div></div>
              <span style="font-size:12px;font-weight:600">${pct}%</span>
            </div>
          </td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;
}

async function loadNeraca() {
  const per = document.getElementById('nc-per').value;
  const r = await fetch('/api/laporan/neraca?per=' + per);
  const j = await r.json();
  if (!j.success) return;
  const kas = j.kas;
  document.getElementById('nc-content').innerHTML = `
    <div style="max-width:600px">
      <div style="font-size:15px;font-weight:600;margin-bottom:1rem">Neraca per ${j.per_tanggal}</div>
      <div class="card" style="margin-bottom:1rem">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;color:var(--ink-soft);letter-spacing:.06em;margin-bottom:1rem">Kas RT</div>
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
          <span>Total Pemasukan</span><span style="font-weight:600;color:var(--forest)">${rp(kas.total_masuk)}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
          <span>Total Pengeluaran</span><span style="font-weight:600;color:var(--rust)">${rp(kas.total_keluar)}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 0;margin-top:4px">
          <span style="font-weight:600">Saldo Kas Tersedia</span><span style="font-weight:700;font-size:1.1rem;color:${kas.saldo>=0?'var(--forest)':'var(--rust)'}">${rp(kas.saldo)}</span>
        </div>
      </div>
      <div class="card">
        <div style="font-size:12px;font-weight:600;text-transform:uppercase;color:var(--ink-soft);letter-spacing:.06em;margin-bottom:1rem">Kewajiban & Target</div>
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
          <span>Iuran Tertunggak (belum bayar)</span><span style="font-weight:600;color:var(--rust)">${rp(j.iuran_tertunggak)}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:8px 0">
          <span>Kekurangan Target Kampanye Aktif</span><span style="font-weight:600;color:#C8A030">${rp(j.kekurangan_target)}</span>
        </div>
      </div>
    </div>`;
}

function exportKas() {
  const tahun = document.getElementById('ak-tahun').value;
  window.location = `/api/laporan/export/kas?dari=${tahun}-01-01&sampai=${tahun}-12-31`;
}
function exportPengeluaran() {
  const tahun = document.getElementById('pe-tahun').value;
  window.location = '/api/laporan/export/pengeluaran?tahun=' + tahun;
}
function exportIuran() {
  const tahun = document.getElementById('iu-tahun').value;
  window.location = '/api/laporan/export/iuran?tahun=' + tahun;
}

loadArusKas();
</script>
@endsection
