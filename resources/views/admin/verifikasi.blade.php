@extends('layouts.admin')
@section('title', 'Verifikasi Donasi')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Verifikasi Donasi</div>
    </div>
    <div style="display:flex;gap:8px">
      @foreach(['pending'=>'Menunggu','verified'=>'Terverifikasi','rejected'=>'Ditolak'] as $v=>$l)
      <button onclick="filterStatus('{{ $v }}')" id="f-{{ $v }}"
        style="padding:7px 16px;border-radius:100px;font-size:13px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif;border:1.5px solid {{ $v==='pending' ? 'var(--forest)' : 'var(--border)' }};background:{{ $v==='pending' ? 'var(--forest)' : '#fff' }};color:{{ $v==='pending' ? '#fff' : 'var(--ink-soft)' }}">
        {{ $l }}
      </button>
      @endforeach
    </div>
  </div>

  <div id="donasi-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
</div>

<div id="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:480px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.25rem" id="modal-title">Verifikasi Donasi</div>
    <div id="modal-info" style="background:var(--cream);border-radius:var(--radius-sm);padding:1rem;margin-bottom:1rem;font-size:13px;line-height:2"></div>
    <div id="modal-bukti" style="margin-bottom:1rem"></div>
    <textarea id="modal-catatan" placeholder="Catatan (opsional)" rows="2"
      style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;resize:none;margin-bottom:1rem"></textarea>
    <div style="display:flex;gap:8px">
      <button onclick="submitVerifikasi('verify')" class="btn-primary" style="flex:1;justify-content:center">✅ Approve</button>
      <button onclick="submitVerifikasi('reject')" style="flex:1;padding:12px;border-radius:100px;border:1.5px solid var(--rust);background:#fff;color:var(--rust);font-size:14px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif">❌ Tolak</button>
      <button onclick="closeModal()" style="padding:12px 16px;border-radius:100px;border:1.5px solid var(--border);background:#fff;font-size:14px;cursor:pointer;font-family:'DM Sans',sans-serif">Batal</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
let currentStatus = 'pending';
let currentId = null;
let donasiCache = [];

async function loadDonasi() {
  const res = await fetch('/api/donasi?status=' + currentStatus);
  const data = await res.json();
  if (!data.success) return;
  const list = data.data;
  donasiCache = list;
  const statusColor = {pending:'#7A5C00',verified:'var(--forest)',rejected:'var(--rust)'};
  const statusBg = {pending:'var(--gold-pale)',verified:'#E8F4ED',rejected:'#FDECEA'};
  const statusLabel = {pending:'Menunggu',verified:'Terverifikasi',rejected:'Ditolak'};

  document.getElementById('donasi-list').innerHTML = !list.length
    ? `<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Tidak ada donasi ${statusLabel[currentStatus].toLowerCase()}.</div>`
    : `<div class="card" style="padding:0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
          <thead><tr style="background:var(--cream)">
            ${['Warga','Kampanye','Nominal','Metode','Waktu','Status',''].map(h=>`<th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--ink-soft)">${h}</th>`).join('')}
          </tr></thead>
          <tbody>${list.map(d => {
            const tgl = new Date(d.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
            return `<tr style="border-top:1px solid var(--border)">
              <td style="padding:12px 16px"><div style="font-size:13px;font-weight:500">${d.nama||'—'}</div><div style="font-size:11px;color:var(--ink-soft)">${d.email||''}</div></td>
              <td style="padding:12px 16px;font-size:13px">${d.judul||'—'}</td>
              <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--forest)">Rp ${parseInt(d.nominal).toLocaleString('id-ID')}</td>
              <td style="padding:12px 16px;font-size:12px;text-transform:uppercase">${d.metode}</td>
              <td style="padding:12px 16px;font-size:12px;color:var(--ink-soft)">${tgl}</td>
              <td style="padding:12px 16px"><span style="font-size:11px;padding:3px 10px;border-radius:99px;background:${statusBg[d.status]};color:${statusColor[d.status]}">${statusLabel[d.status]}</span></td>
              <td style="padding:12px 16px">${d.status==='pending'?`<button onclick="openModal(${d.id})" style="padding:6px 14px;border-radius:99px;border:1.5px solid var(--forest);background:#fff;color:var(--forest);font-size:12px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif">Cek</button>`:'—'}</td>
            </tr>`;
          }).join('')}</tbody>
        </table>
      </div>`;
}

function filterStatus(s) {
  currentStatus = s;
  ['pending','verified','rejected'].forEach(v => {
    const b = document.getElementById('f-'+v);
    const active = v===s;
    b.style.background = active?'var(--forest)':'#fff';
    b.style.color = active?'#fff':'var(--ink-soft)';
    b.style.borderColor = active?'var(--forest)':'var(--border)';
  });
  loadDonasi();
}

function openModal(id) {
  currentId = id;
  const d = donasiCache.find(x => x.id == id);
  if (!d) return;
  document.getElementById('modal-info').innerHTML = `
    <b>Warga:</b> ${d.nama||'—'}<br>
    <b>Kampanye:</b> ${d.judul||'—'}<br>
    <b>Nominal:</b> Rp ${parseInt(d.nominal).toLocaleString('id-ID')}<br>
    <b>Metode:</b> ${d.metode.toUpperCase()}<br>
    <b>Waktu:</b> ${new Date(d.created_at).toLocaleString('id-ID')}`;
  document.getElementById('modal-bukti').innerHTML = d.bukti_url
    ? `<img src="${d.bukti_url}" style="width:100%;max-height:200px;object-fit:contain;border-radius:var(--radius-sm);border:1px solid var(--border)">`
    : `<div style="text-align:center;padding:1rem;background:var(--cream);border-radius:var(--radius-sm);font-size:13px;color:var(--ink-soft)">Belum ada bukti upload</div>`;
  document.getElementById('modal-catatan').value = '';
  document.getElementById('modal-overlay').style.display = 'flex';
}

function closeModal() { document.getElementById('modal-overlay').style.display = 'none'; }

async function submitVerifikasi(action) {
  const catatan = document.getElementById('modal-catatan').value;
  const res = await fetch('/api/donasi/'+currentId+'/verify', {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},
    body: JSON.stringify({action, catatan})
  });
  const data = await res.json();
  if (data.success) { closeModal(); showToast(data.message); loadDonasi(); }
  else alert(data.message);
}

document.getElementById('modal-overlay').addEventListener('click', e => { if(e.target===e.currentTarget) closeModal(); });
loadDonasi();
</script>
@endsection
