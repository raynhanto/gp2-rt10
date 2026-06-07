@extends('layouts.app')
@section('title', 'Riwayat Donasi')
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container" style="max-width:800px">

  <div style="margin-bottom:2rem">
    <a href="/dashboard" style="font-size:13px;color:var(--ink-soft)">← Dashboard</a>
    <div style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--forest);margin-top:6px">Riwayat Donasi</div>
  </div>

  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:2rem">
    @foreach(['r-total'=>['Total Donasi','#E8F4ED','var(--forest)'],'r-count'=>['Jumlah Transaksi','var(--gold-pale)','#7A5C00'],'r-pending'=>['Menunggu Verifikasi','#E8F0FD','#2D5AA8']] as $id=>[$lbl,$bg,$col])
    <div style="background:{{ $bg }};border-radius:var(--radius-sm);padding:1rem;text-align:center">
      <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:{{ $col }}" id="{{ $id }}">—</div>
      <div style="font-size:11px;color:var(--ink-soft);margin-top:3px">{{ $lbl }}</div>
    </div>
    @endforeach
  </div>

  <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
    @foreach(['semua'=>'Semua','verified'=>'Terverifikasi','pending'=>'Menunggu','rejected'=>'Ditolak'] as $v=>$l)
    <button onclick="filterStatus('{{ $v }}')" id="rf-{{ $v }}"
      style="padding:7px 16px;border-radius:100px;font-size:13px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif;border:1.5px solid {{ $v==='semua' ? 'var(--forest)' : 'var(--border)' }};background:{{ $v==='semua' ? 'var(--forest)' : '#fff' }};color:{{ $v==='semua' ? '#fff' : 'var(--ink-soft)' }}">
      {{ $l }}
    </button>
    @endforeach
  </div>

  <div id="riwayat-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>

</div>
</main>
@endsection
@section('scripts')
<script>
let allDonasi = [];
let activeFilter = 'semua';

const statusColor = {pending:'#7A5C00', verified:'var(--forest)', rejected:'var(--rust)'};
const statusBg    = {pending:'var(--gold-pale)', verified:'#E8F4ED', rejected:'#FDECEA'};
const statusLabel = {pending:'Menunggu verifikasi', verified:'Terverifikasi', rejected:'Ditolak'};
const metodeLabel = {qris:'QRIS', transfer:'Transfer Bank', gopay:'GoPay/OVO', ovo:'OVO', dana:'Dana', lainnya:'Lainnya'};

async function loadRiwayat() {
  const res = await fetch('/api/user/donasi');
  const data = await res.json();
  if (!data.success) return;
  allDonasi = data.data;

  const verified = allDonasi.filter(d => d.status === 'verified');
  const total = verified.reduce((s,d) => s + parseInt(d.nominal), 0);
  document.getElementById('r-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
  document.getElementById('r-count').textContent = allDonasi.length;
  document.getElementById('r-pending').textContent = allDonasi.filter(d => d.status === 'pending').length;

  renderList();
}

function filterStatus(s) {
  activeFilter = s;
  document.querySelectorAll('[id^="rf-"]').forEach(b => {
    const active = b.id === 'rf-' + s;
    b.style.background = active ? 'var(--forest)' : '#fff';
    b.style.color = active ? '#fff' : 'var(--ink-soft)';
    b.style.borderColor = active ? 'var(--forest)' : 'var(--border)';
  });
  renderList();
}

function renderList() {
  const list = activeFilter === 'semua' ? allDonasi : allDonasi.filter(d => d.status === activeFilter);

  if (!list.length) {
    document.getElementById('riwayat-list').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Tidak ada donasi.</div>';
    return;
  }

  document.getElementById('riwayat-list').innerHTML = list.map(d => {
    const tgl = new Date(d.created_at).toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'});
    const sc = statusColor[d.status] || 'var(--ink-soft)';
    const sb = statusBg[d.status] || '#F0F0EE';
    const sl = statusLabel[d.status] || d.status;
    const ml = metodeLabel[d.metode] || d.metode;
    return `<div class="card" style="margin-bottom:10px;padding:1.25rem">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;gap:1rem">
        <div style="flex:1">
          <div style="font-family:'DM Serif Display',serif;font-size:1.05rem;color:var(--forest);margin-bottom:3px">${d.judul || 'Kampanye'}</div>
          <div style="font-size:12px;color:var(--ink-soft)">${tgl}</div>
        </div>
        <div style="text-align:right;flex-shrink:0">
          <div style="font-size:1.1rem;font-weight:600;color:var(--forest)">Rp ${parseInt(d.nominal).toLocaleString('id-ID')}</div>
          <span style="font-size:11px;padding:2px 10px;border-radius:99px;background:${sb};color:${sc};display:inline-block;margin-top:3px">${sl}</span>
        </div>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;padding-top:10px;border-top:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:8px">
          <span style="font-size:12px;color:var(--ink-soft)"><i class="fa fa-credit-card" style="margin-right:5px"></i>${ml}</span>
          ${d.is_anonym == 1 ? '<span style="font-size:11px;padding:2px 8px;border-radius:99px;background:#F0F0EE;color:var(--ink-soft)"><i class="fa-solid fa-eye-slash" style="margin-right:3px;font-size:10px"></i>anonim</span>' : '<span style="font-size:11px;padding:2px 8px;border-radius:99px;background:#E8F4ED;color:var(--forest)"><i class="fa-solid fa-user" style="margin-right:3px;font-size:10px"></i>nama ditampilkan</span>'}
        </div>
        ${d.status === 'pending'
          ? `<div style="display:flex;align-items:center;gap:8px">
              ${d.bukti_url ? `<a href="${d.bukti_url}" target="_blank" style="font-size:12px;color:var(--forest-light)"><i class="fa fa-image" style="margin-right:3px"></i>Bukti</a>` : ''}
              <a href="/dashboard/bayar?donasi=${d.id}" style="font-size:12px;padding:5px 14px;background:var(--forest);color:#fff;border-radius:99px;font-weight:500">Selesaikan →</a>
            </div>`
          : d.bukti_url
          ? `<a href="${d.bukti_url}" target="_blank" style="font-size:12px;color:var(--forest-light)"><i class="fa fa-image" style="margin-right:4px"></i>Lihat bukti</a>`
          : `<span style="font-size:12px;color:var(--ink-soft)">#${d.id}</span>`
        }
      </div>
    </div>`;
  }).join('');
}

loadRiwayat();
</script>
@endsection
