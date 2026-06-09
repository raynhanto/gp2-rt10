@extends('layouts.admin')
@section('title', 'Permohonan Surat — Admin')
@section('content')
<style>
.sr-filters { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1.25rem; }
.sr-filter-pill { padding:5px 14px; border-radius:99px; border:1.5px solid var(--border); background:#fff; font-size:12px; font-weight:500; cursor:pointer; color:var(--ink-soft); font-family:'DM Sans',sans-serif; transition:.15s; }
.sr-filter-pill.active { background:var(--forest); color:#fff; border-color:var(--forest); }

.sr-row { background:#fff; border:1px solid var(--border); border-radius:10px; padding:1rem 1.25rem; margin-bottom:.625rem; display:grid; grid-template-columns:1fr auto; gap:.75rem; align-items:start; cursor:pointer; transition:box-shadow .15s; }
.sr-row:hover { box-shadow:0 2px 10px rgba(0,0,0,.07); }
.sr-row.is-menunggu { border-left:3px solid var(--gold); }
.sr-row.is-diproses { border-left:3px solid #2d5aa8; }
.sr-row.is-selesai { border-left:3px solid var(--forest-light); }
.sr-row.is-ditolak { border-left:3px solid var(--rust); }

.sr-badge { display:inline-block; font-size:10px; font-weight:700; padding:2px 9px; border-radius:99px; letter-spacing:.03em; background:var(--forest-pale); color:var(--forest); }

.sr-status-badge { font-size:10px; font-weight:700; padding:2px 9px; border-radius:99px; white-space:nowrap; }
.sr-status-menunggu { background:var(--gold-pale); color:var(--gold-dark); }
.sr-status-diproses { background:#e8f0fd; color:#2d5aa8; }
.sr-status-selesai { background:var(--forest-pale); color:var(--forest); }
.sr-status-ditolak { background:#fdecea; color:var(--rust); }

/* Detail panel — right drawer on desktop, bottom sheet on mobile */
.sr-detail { display:none; position:fixed; top:0; right:0; bottom:0; width:480px; background:#fff; border-left:1px solid var(--border); box-shadow:-8px 0 32px rgba(0,0,0,.12); z-index:300; flex-direction:column; overflow:hidden; }
.sr-detail.open { display:flex; }
@media(max-width:900px) {
  .sr-detail { top:auto; left:0; right:0; bottom:0; width:100%; height:88vh; border-left:none; border-top:1px solid var(--border); border-radius:16px 16px 0 0; box-shadow:0 -8px 32px rgba(0,0,0,.15); }
}
.sr-detail-header { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); flex-shrink:0; display:flex; align-items:flex-start; gap:.75rem; }
.sr-detail-header-info { flex:1; min-width:0; }
.sr-detail-body { flex:1; overflow-y:auto; padding:1.25rem 1.5rem; }
.sr-detail-footer { padding:1rem 1.5rem; border-top:1px solid var(--border); flex-shrink:0; }
.sr-field-label { font-size:11px; font-weight:600; color:var(--ink-mid); margin-bottom:.3rem; text-transform:uppercase; letter-spacing:.06em; }
.sr-field-val { font-size:13px; color:var(--ink); line-height:1.65; }
.sr-field-group { margin-bottom:1rem; }
.sr-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:299; }
.sr-overlay.open { display:block; }

/* Print template */
@media print {
  body * { visibility:hidden; }
  #printArea, #printArea * { visibility:visible; }
  #printArea { position:fixed; top:0; left:0; width:100%; padding:2cm; font-family:'DM Sans',sans-serif; }
}

.sr-input { border:1.5px solid var(--border); border-radius:8px; padding:.5rem .75rem; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--ink); background:#fff; width:100%; margin-bottom:.625rem; }
.sr-input:focus { outline:none; border-color:var(--forest); }
.sr-textarea { resize:vertical; min-height:80px; }

/* Status action buttons */
.sr-action-grid { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; margin-bottom:.875rem; }
.sr-action-btn {
  display:flex; align-items:center; gap:.5rem;
  padding:.625rem .875rem; border-radius:10px; border:1.5px solid var(--border);
  font-size:12.5px; font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif;
  transition:all .15s; background:#f5f4f2; color:var(--ink-mute);
}
.sr-action-btn i { font-size:12px; flex-shrink:0; }
.sr-action-btn:not(.current):hover { border-color:#bbb; color:var(--ink-soft); background:#eee; }
.sr-action-btn .sr-check { margin-left:auto; font-size:11px; display:none; }

/* Selected = solid filled, white text */
.sr-action-btn.current { cursor:default; font-weight:700; color:#fff; border-color:transparent; }
.sr-action-btn.current .sr-check { display:inline; }
.sr-action-btn.current[data-s="menunggu"] { background:#C8A030; }
.sr-action-btn.current[data-s="diproses"] { background:#2d5aa8; }
.sr-action-btn.current[data-s="selesai"]  { background:var(--forest); }
.sr-action-btn.current[data-s="ditolak"]  { background:var(--rust); }
</style>

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Permohonan Surat</div>
    <div class="admin-page-sub">Permintaan surat keterangan dari warga — klik baris untuk memproses</div>
  </div>
  <span id="sr-count" style="font-size:12px;color:var(--ink-mute)"></span>
</div>

{{-- Status filters --}}
<div class="sr-filters">
  <button class="sr-filter-pill active" onclick="setFilter(this,'')">Semua</button>
  <button class="sr-filter-pill" onclick="setFilter(this,'status:menunggu')">
    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--gold);margin-right:4px;vertical-align:middle"></span>Menunggu
  </button>
  <button class="sr-filter-pill" onclick="setFilter(this,'status:diproses')">
    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#2d5aa8;margin-right:4px;vertical-align:middle"></span>Diproses
  </button>
  <button class="sr-filter-pill" onclick="setFilter(this,'status:selesai')">
    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--forest-light);margin-right:4px;vertical-align:middle"></span>Selesai
  </button>
  <button class="sr-filter-pill" onclick="setFilter(this,'status:ditolak')">
    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--rust);margin-right:4px;vertical-align:middle"></span>Ditolak
  </button>
</div>

<div id="sr-list"></div>
<div id="sr-empty" style="display:none;text-align:center;padding:3rem 0;color:var(--ink-mute);font-size:14px;">
  <i class="fa fa-file-lines" style="font-size:2rem;opacity:.3;margin-bottom:.75rem;display:block"></i>
  Belum ada permohonan surat
</div>

{{-- Overlay --}}
<div class="sr-overlay" id="srOverlay" onclick="closeDetail()"></div>

{{-- Detail panel --}}
<div class="sr-detail" id="srDetail">
  <div class="sr-detail-header">
    <div class="sr-detail-header-info">
      <div id="dpJenis" style="font-size:14px;font-weight:600;color:var(--ink);margin-bottom:.25rem"></div>
      <div id="dpAtas" style="font-size:12px;color:var(--ink-soft)"></div>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center">
      <button id="dpPrintBtn" onclick="printSurat()" style="background:var(--forest-pale);color:var(--forest);border:none;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">
        <i class="fa fa-print"></i> Cetak
      </button>
      <button onclick="closeDetail()" style="background:none;border:none;font-size:18px;cursor:pointer;color:var(--ink-soft);padding:4px;line-height:1">&times;</button>
    </div>
  </div>
  <div class="sr-detail-body" id="dpBody"></div>
  <div class="sr-detail-footer" id="dpFooter"></div>
</div>

{{-- Hidden print area --}}
<div id="printArea" style="display:none"></div>

<script>
const _csrfT = '{{ csrf_token() }}';
let _data = [], _filter = '', _active = null, _pendingStatus = null;

async function loadSurat() {
  const [statusF] = _filter ? _filter.split(':') : [''];
  const statusVal = _filter.includes(':') ? _filter.split(':')[1] : '';
  let url = '/api/surat';
  if (statusVal) url += '?status=' + statusVal;
  const r = await fetch(url, { headers: { 'X-CSRF-TOKEN': _csrfT } });
  const j = await r.json();
  if (!j.success) return;
  _data = j.data;
  render();
}

function render() {
  const list = document.getElementById('sr-list');
  const empty = document.getElementById('sr-empty');
  document.getElementById('sr-count').textContent = _data.length + ' permohonan';
  if (!_data.length) { list.innerHTML = ''; empty.style.display = ''; return; }
  empty.style.display = 'none';
  list.innerHTML = _data.map(s => `
    <div class="sr-row is-${s.status}" onclick="openDetail(${s.id})">
      <div>
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem">
          <span class="sr-badge">${escHtml(s.jenis_label)}</span>
          <span class="sr-status-badge sr-status-${s.status}">${statusLabel(s.status)}</span>
          ${s.nomor_surat ? `<span style="font-size:11px;color:var(--ink-mute)">${escHtml(s.nomor_surat)}</span>` : ''}
        </div>
        <div style="font-size:14px;font-weight:600;color:var(--ink)">${escHtml(s.atas_nama)}</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:.2rem">
          ${s.user ? escHtml(s.user.nama || s.user.email) : '<em>Tamu</em>'} &mdash; ${formatDate(s.created_at)}
        </div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:.15rem">${escHtml(s.keperluan.substring(0, 90))}${s.keperluan.length > 90 ? '...' : ''}</div>
      </div>
    </div>
  `).join('');
}

function setFilter(btn, f) {
  _filter = f;
  document.querySelectorAll('.sr-filter-pill').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadSurat();
}

function openDetail(id) {
  const s = _data.find(x => x.id === id);
  if (!s) return;
  _active = s;
  document.getElementById('dpJenis').textContent = s.jenis_label;
  document.getElementById('dpAtas').textContent = 'a.n. ' + s.atas_nama + (s.nik ? ' — NIK: ' + s.nik : '');

  // Build body
  let bodyHtml = '';
  bodyHtml += field('Pemohon', s.user ? (s.user.nama || s.user.email) : 'Tamu');
  bodyHtml += field('Status', `<span class="sr-status-badge sr-status-${s.status}">${statusLabel(s.status)}</span>`);
  if (s.nomor_surat) bodyHtml += field('Nomor Surat', s.nomor_surat);
  if (s.tanggal_surat) bodyHtml += field('Tanggal Surat', formatDate(s.tanggal_surat));
  bodyHtml += field('Tanggal Permohonan', formatDate(s.created_at));
  bodyHtml += field('NIK', s.nik || '—');
  bodyHtml += field('Alamat', s.alamat || '—');
  bodyHtml += field('Keperluan', '<span style="white-space:pre-line">' + escHtml(s.keperluan) + '</span>');

  // Extra fields from data_tambahan
  if (s.data_tambahan && Object.keys(s.data_tambahan).length) {
    bodyHtml += '<div style="height:1px;background:var(--border);margin:1rem 0"></div>';
    bodyHtml += '<div class="sr-field-label" style="margin-bottom:.5rem">Data Tambahan</div>';
    for (const [k, v] of Object.entries(s.data_tambahan)) {
      if (v) bodyHtml += field(k.replace(/_/g,' '), escHtml(String(v)));
    }
  }

  if (s.catatan_admin) {
    bodyHtml += '<div style="height:1px;background:var(--border);margin:1rem 0"></div>';
    bodyHtml += `<div style="background:var(--forest-pale);border-radius:8px;padding:.875rem">
      <div class="sr-field-label" style="color:var(--forest);margin-bottom:.3rem"><i class="fa fa-note-sticky"></i> Catatan Admin</div>
      <div class="sr-field-val" style="white-space:pre-line">${escHtml(s.catatan_admin)}</div>
    </div>`;
  }

  if (s.processed_by_data) {
    bodyHtml += field('Diproses oleh', s.processed_by_data.nama || s.processed_by_data.email);
    bodyHtml += field('Diproses pada', formatDate(s.processed_at));
  }

  document.getElementById('dpBody').innerHTML = bodyHtml;

  // Build footer (process form)
  const statusMeta = {
    menunggu: { icon:'fa-clock',        label:'Menunggu' },
    diproses: { icon:'fa-spinner',      label:'Diproses' },
    selesai:  { icon:'fa-circle-check', label:'Selesai' },
    ditolak:  { icon:'fa-circle-xmark', label:'Ditolak' },
  };
  const actionBtns = Object.entries(statusMeta).map(([val, {icon, label}]) => `
    <button class="sr-action-btn${s.status===val?' current':''}" data-s="${val}" onclick="setStatus('${val}')">
      <i class="fa ${icon}"></i> ${label}
      <i class="fa fa-check sr-check"></i>
    </button>
  `).join('');

  document.getElementById('dpFooter').innerHTML = `
    <div style="font-size:11px;font-weight:600;color:var(--ink-mute);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem">Ubah Status</div>
    <div class="sr-action-grid">${actionBtns}</div>

    <div style="height:1px;background:var(--border);margin:.75rem 0"></div>

    <div style="font-size:11px;font-weight:600;color:var(--ink-mute);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem">Detail Surat</div>
    <input class="sr-input" id="dpNomor" placeholder="Nomor Surat (opsional)" value="${escAttr(s.nomor_surat||'')}">
    <input class="sr-input" id="dpTanggal" type="date" value="${s.tanggal_surat ? s.tanggal_surat.substring(0,10) : todayIso()}">
    <textarea class="sr-input sr-textarea" id="dpCatatan" placeholder="Catatan untuk pemohon (opsional)">${escHtml(s.catatan_admin||'')}</textarea>

    <div style="display:flex;gap:.5rem;margin-top:.25rem">
      <button id="dpSaveBtn" onclick="saveSurat()" style="flex:1;background:var(--forest);color:#fff;border:none;border-radius:8px;padding:.625rem;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif">
        <i class="fa fa-floppy-disk"></i> Simpan Detail
      </button>
      <button onclick="deleteSurat(${s.id})" title="Hapus permohonan" style="background:#fdecea;color:var(--rust);border:none;border-radius:8px;padding:.625rem 14px;font-size:13px;cursor:pointer;font-family:'DM Sans',sans-serif">
        <i class="fa fa-trash"></i>
      </button>
    </div>
  `;
  _pendingStatus = s.status;

  document.getElementById('srDetail').classList.add('open');
  document.getElementById('srOverlay').classList.add('open');
}

function closeDetail() {
  _active = null;
  document.getElementById('srDetail').classList.remove('open');
  document.getElementById('srOverlay').classList.remove('open');
}

function setStatus(val) {
  _pendingStatus = val;
  document.querySelectorAll('.sr-action-btn').forEach(btn => {
    const isCurrent = btn.dataset.s === val;
    btn.classList.toggle('current', isCurrent);
  });
}

async function saveSurat() {
  if (!_active) return;
  const btn = document.getElementById('dpSaveBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';

  const body = {
    status:        _pendingStatus || _active.status,
    nomor_surat:   document.getElementById('dpNomor').value.trim() || null,
    tanggal_surat: document.getElementById('dpTanggal').value || null,
    catatan_admin: document.getElementById('dpCatatan').value.trim() || null,
  };
  const r = await fetch(`/api/surat/${_active.id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfT },
    body: JSON.stringify(body),
  });
  const j = await r.json();
  if (j.success) { closeDetail(); loadSurat(); } else {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-floppy-disk"></i> Simpan Detail';
    alert(j.message || 'Gagal menyimpan.');
  }
}

async function deleteSurat(id) {
  if (!confirm('Hapus permohonan surat ini?')) return;
  const r = await fetch(`/api/surat/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrfT } });
  const j = await r.json();
  if (j.success) { closeDetail(); loadSurat(); }
}

function printSurat() {
  if (!_active) return;
  const s = _active;
  const today = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
  const nomorSurat = s.nomor_surat || '___/RT10/GP2/__________';
  const tanggalSurat = s.tanggal_surat ? new Date(s.tanggal_surat).toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' }) : today;

  let extraRows = '';
  if (s.data_tambahan) {
    for (const [k, v] of Object.entries(s.data_tambahan)) {
      if (v) extraRows += `<tr><td style="width:180px;padding:3px 8px 3px 0;vertical-align:top;color:#444">${k.replace(/_/g,' ')}</td><td style="padding:3px 0">: ${escHtml(String(v))}</td></tr>`;
    }
  }

  const html = `
    <div style="font-family:'Times New Roman',serif;max-width:700px;margin:0 auto;padding:2rem;color:#000">
      <div style="text-align:center;border-bottom:3px double #000;padding-bottom:1rem;margin-bottom:1.5rem">
        <div style="font-size:22px;font-weight:bold;letter-spacing:.05em">RUKUN TETANGGA 10</div>
        <div style="font-size:14px">Perumahan Golden Park 2, Serang, Banten</div>
        <div style="font-size:12px;margin-top:.25rem">Kecamatan Walantaka, Kota Serang 42183</div>
      </div>
      <div style="text-align:center;margin-bottom:1.5rem">
        <div style="font-size:16px;font-weight:bold;text-decoration:underline;letter-spacing:.03em">${escHtml(s.jenis_label).toUpperCase()}</div>
        <div style="font-size:12px;margin-top:.4rem">Nomor: ${escHtml(nomorSurat)}</div>
      </div>
      <p style="margin-bottom:1rem;font-size:13px;line-height:1.8">Yang bertanda tangan di bawah ini, Ketua RT 10 Perumahan Golden Park 2, dengan ini menerangkan bahwa:</p>
      <table style="width:100%;margin-bottom:1.25rem;font-size:13px">
        <tr><td style="width:180px;padding:3px 8px 3px 0;vertical-align:top;color:#444">Nama</td><td style="padding:3px 0">: <strong>${escHtml(s.atas_nama)}</strong></td></tr>
        ${s.nik ? `<tr><td style="padding:3px 8px 3px 0;color:#444">NIK</td><td style="padding:3px 0">: ${escHtml(s.nik)}</td></tr>` : ''}
        ${s.alamat ? `<tr><td style="padding:3px 8px 3px 0;color:#444">Alamat</td><td style="padding:3px 0">: ${escHtml(s.alamat)}</td></tr>` : ''}
        ${extraRows}
      </table>
      <p style="font-size:13px;line-height:1.8;margin-bottom:1.5rem">adalah benar warga RT 10 Perumahan Golden Park 2 yang memerlukan surat keterangan ini untuk keperluan: <em>${escHtml(s.keperluan)}</em>.</p>
      <p style="font-size:13px;line-height:1.8;margin-bottom:2.5rem">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
      <div style="text-align:right;font-size:13px">
        <p>Serang, ${tanggalSurat}</p>
        <p style="margin:0">Ketua RT 10 GP2</p>
        <div style="height:60px"></div>
        <p style="margin:0;border-top:1px solid #000;display:inline-block;padding-top:.25rem;min-width:180px">(__________________________)</p>
      </div>
    </div>`;

  document.getElementById('printArea').innerHTML = html;
  document.getElementById('printArea').style.display = 'block';
  window.print();
  document.getElementById('printArea').style.display = 'none';
}

function field(label, val) {
  return `<div class="sr-field-group"><div class="sr-field-label">${label}</div><div class="sr-field-val">${val}</div></div>`;
}
function statusLabel(s) {
  return { menunggu:'Menunggu', diproses:'Diproses', selesai:'Selesai', ditolak:'Ditolak' }[s] || s;
}
function formatDate(dt) {
  if (!dt) return '—';
  return new Date(dt).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
}
function todayIso() {
  return new Date().toLocaleDateString('sv'); // 'sv' locale gives YYYY-MM-DD
}
function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escAttr(s) {
  return String(s).replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

loadSurat();
</script>
@endsection
