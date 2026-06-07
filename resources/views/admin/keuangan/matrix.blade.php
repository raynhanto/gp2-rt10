@extends('layouts.admin')
@section('title', 'Matrix Compliance Iuran')
@section('content')
<div class="container" style="max-width:1300px">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <a href="/admin/keuangan/iuran" style="font-size:13px;color:var(--ink-soft)">← Iuran</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Matrix Compliance Iuran</div>
      <div style="font-size:13px;color:var(--ink-soft);margin-top:4px">Warna: <span style="background:#C8F7D0;padding:2px 8px;border-radius:4px;font-size:12px">Lunas</span> <span style="background:#FFF3CD;padding:2px 8px;border-radius:4px;font-size:12px">Pending</span> <span style="background:#FDECEA;padding:2px 8px;border-radius:4px;font-size:12px">Belum</span> <span style="background:#E9ECEF;padding:2px 8px;border-radius:4px;font-size:12px">Dispensasi</span></div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <select id="f-tahun" style="padding:9px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none" onchange="loadMatrix()">
        @for($y = date('Y'); $y >= 2024; $y--)<option value="{{ $y }}" @if($y == date('Y')) selected @endif>{{ $y }}</option>@endfor
      </select>
      <button onclick="window.print()" class="btn-secondary" style="padding:9px 16px;font-size:13px">🖨️ Print</button>
      <a href="/api/laporan/export/iuran?tahun={{ date('Y') }}" id="export-link" class="btn-secondary" style="padding:9px 16px;font-size:13px">↓ Excel</a>
    </div>
  </div>

  <div id="stats-row" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem"></div>
  <div id="matrix-container" style="overflow-x:auto"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
</div>
<style>
@media print {
  nav, footer, .no-print { display:none!important }
  main { padding:1rem 0!important }
  .container { max-width:100%!important; padding:0!important }
}
.cell-lunas      { background:#C8F7D0; color:#1A4D30; }
.cell-pending    { background:#FFF3CD; color:#7A5C00; }
.cell-belum      { background:#FDECEA; color:#8B1A00; }
.cell-dispensasi { background:#E9ECEF; color:#5A5A5A; }
.cell-empty      { background:#F8F9FA; color:#9D9080; }
</style>
@endsection
@section('scripts')
<script>
const bulanNama = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

async function loadMatrix() {
  const tahun = document.getElementById('f-tahun').value;
  document.getElementById('export-link').href = `/api/laporan/export/iuran?tahun=${tahun}`;
  document.getElementById('matrix-container').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-soft)">Memuat...</div>';

  const r = await fetch('/api/iuran/matrix?tahun=' + tahun);
  const j = await r.json();
  if (!j.success) return;

  const bulan   = j.bulan;
  const units   = j.data;
  const periodes = j.periodes;

  // Stats
  let totalLunas=0, totalBelum=0, totalPending=0, totalDisp=0, total=0;
  units.forEach(u => bulan.forEach(b => {
    const s = u.bulan[b]?.status;
    if (s) { total++; if(s==='lunas')totalLunas++; else if(s==='belum')totalBelum++; else if(s==='pending')totalPending++; else if(s==='dispensasi')totalDisp++; }
  }));
  const pct = total > 0 ? Math.round(totalLunas/total*100) : 0;

  document.getElementById('stats-row').innerHTML = [
    ['Compliance', pct+'%', '#E8F4ED', 'var(--forest)'],
    ['Lunas', totalLunas, '#E8F4ED', 'var(--forest)'],
    ['Pending', totalPending, '#FFF3CD', '#7A5C00'],
    ['Belum Bayar', totalBelum, '#FDECEA', 'var(--rust)'],
  ].map(([lbl,val,bg,col])=>`<div style="background:${bg};border-radius:var(--radius-sm);padding:1rem 1.25rem">
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:${col};opacity:.7;margin-bottom:4px">${lbl}</div>
    <div style="font-family:'DM Serif Display',serif;font-size:1.6rem;color:${col}">${val}</div>
  </div>`).join('');

  if (!units.length) {
    document.getElementById('matrix-container').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Belum ada data tagihan untuk tahun ini.</div>';
    return;
  }

  // Build table
  let html = `<table style="border-collapse:collapse;font-size:12px;min-width:100%">
    <thead>
      <tr style="background:var(--cream)">
        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);white-space:nowrap;position:sticky;left:0;background:var(--cream);z-index:2">Unit</th>
        ${bulan.map(b=>`<th style="padding:10px 12px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft);min-width:64px">
          <div>${bulanNama[b]}</div>
          ${periodes[b]?`<div style="font-size:10px;color:var(--ink-mute)">Rp ${parseInt(periodes[b].nominal/1000)}rb</div>`:''}
        </th>`).join('')}
        <th style="padding:10px 12px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft)">Total Lunas</th>
      </tr>
    </thead>
    <tbody>
      ${units.map(u=>{
        let lunasCount=0;
        const cells = bulan.map(b=>{
          const data   = u.bulan[b];
          const status = data?.status || '';
          if (status==='lunas') lunasCount++;
          const cls    = status ? `cell-${status}` : 'cell-empty';
          const label  = status ? status.charAt(0).toUpperCase() : '—';
          const tagihanId = data?.tagihan_id;
          return `<td class="${cls}" style="padding:7px 10px;text-align:center;border:1px solid rgba(0,0,0,0.04);cursor:${tagihanId?'pointer':'default'}" ${tagihanId?`onclick="window.location='/admin/keuangan/iuran'"`:''} title="${status||'Belum ada tagihan'}">
            ${label}
          </td>`;
        }).join('');
        return `<tr style="border-bottom:1px solid var(--border)">
          <td style="padding:9px 14px;font-weight:500;white-space:nowrap;position:sticky;left:0;background:#fff;z-index:1;border-right:2px solid var(--border)">Blok ${u.blok}${u.nomor}</td>
          ${cells}
          <td style="padding:9px 12px;text-align:center;font-weight:600;color:${lunasCount===bulan.length?'var(--forest)':lunasCount>0?'#C8A030':'var(--rust)'}">
            ${lunasCount}/${bulan.length}
          </td>
        </tr>`;
      }).join('')}
    </tbody>
  </table>`;

  document.getElementById('matrix-container').innerHTML = html;
}

loadMatrix();
</script>
@endsection
