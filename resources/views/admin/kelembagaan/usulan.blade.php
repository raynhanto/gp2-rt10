@extends('layouts.admin')
@section('title', 'Usulan Pembangunan — Admin')
@section('content')
<style>
.up-row { background:#fff; border:1px solid var(--border); border-radius:10px; padding:1rem 1.25rem; margin-bottom:.625rem; display:grid; grid-template-columns:1fr auto; gap:.75rem; align-items:start; cursor:pointer; transition:box-shadow .15s; }
.up-row:hover { box-shadow:0 2px 10px rgba(0,0,0,.07); }
.up-row.is-baru       { border-left:3px solid var(--rust); }
.up-row.is-dikaji     { border-left:3px solid var(--gold); }
.up-row.is-disetujui  { border-left:3px solid var(--forest-light); }
.up-row.is-ditolak    { border-left:3px solid #aaa; }
.up-row.is-selesai    { border-left:3px solid rgba(26,61,43,.2); }

.up-status { font-size:10px; font-weight:700; padding:2px 9px; border-radius:99px; white-space:nowrap; }
.up-status-baru       { background:#fdecea; color:var(--rust); }
.up-status-dikaji     { background:var(--gold-pale); color:var(--gold-dark); }
.up-status-disetujui  { background:var(--forest-pale); color:var(--forest); }
.up-status-ditolak    { background:#efefed; color:var(--ink-soft); }
.up-status-selesai    { background:#e8f0fd; color:#2d5aa8; }

.up-prioritas { font-size:10px; font-weight:600; padding:2px 8px; border-radius:99px; }
.up-prioritas-rendah  { background:#efefed; color:var(--ink-soft); }
.up-prioritas-sedang  { background:var(--gold-pale); color:var(--gold-dark); }
.up-prioritas-tinggi  { background:#fdecea; color:var(--rust); }

.up-detail { display:none; position:fixed; top:0; right:0; bottom:0; width:460px; background:#fff; border-left:1px solid var(--border); box-shadow:-8px 0 32px rgba(0,0,0,.12); z-index:300; flex-direction:column; overflow:hidden; }
.up-detail.open { display:flex; }
.up-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:299; }
.up-overlay.open { display:block; }

.up-field-label { font-size:11px; font-weight:600; color:var(--ink-mid); margin-bottom:.3rem; text-transform:uppercase; letter-spacing:.06em; }
.up-field-val { font-size:13px; color:var(--ink); line-height:1.65; }
.up-field-group { margin-bottom:1rem; }

.up-action-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:.4rem; margin-bottom:.75rem; }
.up-action-btn { display:flex; align-items:center; justify-content:center; gap:.35rem; padding:.5rem .5rem; border-radius:8px; border:1.5px solid var(--border); font-size:11.5px; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all .15s; background:#f5f4f2; color:var(--ink-mute); }
.up-action-btn.current { color:#fff; border-color:transparent; }
.up-action-btn.current[data-s="dikaji"]    { background:var(--gold); }
.up-action-btn.current[data-s="disetujui"] { background:var(--forest); }
.up-action-btn.current[data-s="ditolak"]   { background:var(--ink-soft); }
.up-action-btn.current[data-s="selesai"]   { background:#2d5aa8; }
.up-action-btn:not(.current):hover { border-color:#bbb; color:var(--ink-soft); background:#eee; }
.f-input { width:100%; padding:.55rem .75rem; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--ink); background:#fff; margin-bottom:.625rem; }
.f-input:focus { outline:none; border-color:var(--forest); }
</style>

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Usulan Pembangunan</div>
    <div class="admin-page-sub">Ide &amp; usulan warga untuk pengembangan lingkungan — klik baris untuk merespons</div>
  </div>
  <span id="up-count" style="font-size:12px;color:var(--ink-mute)"></span>
</div>

{{-- Filters --}}
<div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.25rem">
  @foreach([''=>'Semua','baru'=>'Baru','dikaji'=>'Dikaji','disetujui'=>'Disetujui','selesai'=>'Selesai','ditolak'=>'Ditolak'] as $val => $lbl)
  <button class="sr-filter-pill{{ $val==='' ? ' active' : '' }}" onclick="setFilter(this,'{{ $val }}')"
    style="padding:5px 14px;border-radius:99px;border:1.5px solid var(--border);background:#fff;font-size:12px;font-weight:500;cursor:pointer;color:var(--ink-soft);font-family:'DM Sans',sans-serif;transition:.15s">{{ $lbl }}</button>
  @endforeach
  <select id="f-prioritas" onchange="load()" style="padding:5px 14px;border-radius:99px;border:1.5px solid var(--border);background:#fff;font-size:12px;font-family:'DM Sans',sans-serif;color:var(--ink-soft);cursor:pointer">
    <option value="">Semua Prioritas</option>
    <option value="tinggi">Tinggi</option>
    <option value="sedang">Sedang</option>
    <option value="rendah">Rendah</option>
  </select>
</div>

<div id="up-list"></div>
<div id="up-empty" style="display:none;text-align:center;padding:3rem 0;color:var(--ink-mute)">
  <i class="fa fa-lightbulb" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.75rem"></i>
  Belum ada usulan pembangunan
</div>

<div class="up-overlay" id="upOverlay" onclick="closeDetail()"></div>
<div class="up-detail" id="upDetail">
  <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);flex-shrink:0;display:flex;align-items:flex-start;gap:.75rem">
    <div style="flex:1;min-width:0">
      <div id="dpJudul" style="font-size:14px;font-weight:600;color:var(--ink);margin-bottom:.25rem"></div>
      <div id="dpMeta" style="font-size:12px;color:var(--ink-soft)"></div>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center">
      <button onclick="closeDetail()" style="background:none;border:none;font-size:18px;cursor:pointer;color:var(--ink-soft);padding:4px;line-height:1">&times;</button>
    </div>
  </div>
  <div id="dpBody" style="flex:1;overflow-y:auto;padding:1.25rem 1.5rem"></div>
  <div id="dpFooter" style="padding:1rem 1.5rem;border-top:1px solid var(--border);flex-shrink:0"></div>
</div>

<script>
const _csrfT = '{{ csrf_token() }}';
let _data = [], _filterStatus = '', _active = null, _pendingStatus = null;

async function load() {
  const prioritas = document.getElementById('f-prioritas').value;
  let url = '/api/usulan?';
  if (_filterStatus) url += `status=${_filterStatus}&`;
  if (prioritas) url += `prioritas=${prioritas}&`;

  const r = await fetch(url, { headers: { 'X-CSRF-TOKEN': _csrfT } });
  const j = await r.json();
  if (!j.success) return;
  _data = j.data;
  render();
}

function render() {
  const list = document.getElementById('up-list');
  const empty = document.getElementById('up-empty');
  document.getElementById('up-count').textContent = _data.length + ' usulan';

  if (!_data.length) { list.innerHTML = ''; empty.style.display = ''; return; }
  empty.style.display = 'none';

  const pLabel = { rendah:'Rendah', sedang:'Sedang', tinggi:'Tinggi' };
  const sLabel = { baru:'Baru', dikaji:'Dikaji', disetujui:'Disetujui', ditolak:'Ditolak', selesai:'Selesai' };

  list.innerHTML = _data.map(u => `
    <div class="up-row is-${u.status}" onclick="openDetail(${u.id})">
      <div>
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem;flex-wrap:wrap">
          <span class="up-status up-status-${u.status}">${sLabel[u.status]||u.status}</span>
          <span class="up-prioritas up-prioritas-${u.prioritas}"><i class="fa fa-flag" style="font-size:9px"></i> ${pLabel[u.prioritas]||u.prioritas}</span>
          ${u.lokasi ? `<span style="font-size:11px;color:var(--ink-mute)"><i class="fa fa-location-dot" style="font-size:10px"></i> ${escHtml(u.lokasi)}</span>` : ''}
        </div>
        <div style="font-size:14px;font-weight:600;color:var(--ink)">${escHtml(u.judul)}</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:.2rem">${escHtml(u.pengusul_display)} &mdash; ${formatDate(u.created_at)}</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:.15rem">${escHtml(u.deskripsi.substring(0,100))}${u.deskripsi.length>100?'...':''}</div>
      </div>
    </div>
  `).join('');
}

function setFilter(btn, f) {
  _filterStatus = f;
  document.querySelectorAll('.sr-filter-pill').forEach(b => {
    b.classList.remove('active');
    b.style.background = '#fff'; b.style.color = 'var(--ink-soft)'; b.style.borderColor = 'var(--border)';
  });
  btn.classList.add('active');
  btn.style.background = 'var(--forest)'; btn.style.color = '#fff'; btn.style.borderColor = 'var(--forest)';
  load();
}

function openDetail(id) {
  const u = _data.find(x => x.id === id);
  if (!u) return;
  _active = u;
  _pendingStatus = u.status;

  document.getElementById('dpJudul').textContent = u.judul;
  document.getElementById('dpMeta').textContent = `${u.pengusul_display} — ${formatDate(u.created_at)}`;

  const pLabel = { rendah:'Rendah', sedang:'Sedang', tinggi:'Tinggi' };
  let body = '';
  body += fld('Lokasi', u.lokasi || '—');
  body += fld('Prioritas', `<span class="up-prioritas up-prioritas-${u.prioritas}">${pLabel[u.prioritas]}</span>`);
  body += fld('Deskripsi', `<span style="white-space:pre-line">${escHtml(u.deskripsi)}</span>`);

  if (u.tanggapan) {
    body += `<div style="background:var(--forest-pale);border-radius:8px;padding:.875rem;margin-top:1rem">
      <div class="up-field-label" style="color:var(--forest);margin-bottom:.3rem"><i class="fa fa-reply"></i> Tanggapan Admin</div>
      <div class="up-field-val" style="white-space:pre-line">${escHtml(u.tanggapan)}</div>
    </div>`;
  }

  document.getElementById('dpBody').innerHTML = body;

  const statuses = [
    { val:'dikaji', icon:'fa-magnifying-glass', label:'Dikaji' },
    { val:'disetujui', icon:'fa-circle-check', label:'Disetujui' },
    { val:'ditolak', icon:'fa-circle-xmark', label:'Ditolak' },
    { val:'selesai', icon:'fa-flag-checkered', label:'Selesai' },
  ];

  document.getElementById('dpFooter').innerHTML = `
    <div style="font-size:11px;font-weight:600;color:var(--ink-mute);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem">Ubah Status</div>
    <div class="up-action-grid">
      ${statuses.map(s => `
        <button class="up-action-btn${u.status===s.val?' current':''}" data-s="${s.val}" onclick="setStatus('${s.val}')">
          <i class="fa ${s.icon}"></i> ${s.label}
        </button>`).join('')}
    </div>
    <div style="height:1px;background:var(--border);margin:.625rem 0"></div>
    <div style="font-size:11px;font-weight:600;color:var(--ink-mute);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem">Tanggapan</div>
    <textarea class="f-input" id="dpTanggapan" rows="4" placeholder="Tulis tanggapan/keputusan pengurus RT..." style="resize:vertical">${escHtml(u.tanggapan||'')}</textarea>
    <div style="display:flex;gap:.5rem">
      <button id="dpSaveBtn" onclick="saveUsulan()" style="flex:1;background:var(--forest);color:#fff;border:none;border-radius:8px;padding:.625rem;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif">
        <i class="fa fa-floppy-disk"></i> Simpan
      </button>
      <button onclick="deleteUsulan(${u.id})" style="background:#fdecea;color:var(--rust);border:none;border-radius:8px;padding:.625rem 14px;font-size:13px;cursor:pointer;font-family:'DM Sans',sans-serif">
        <i class="fa fa-trash"></i>
      </button>
    </div>
  `;

  document.getElementById('upDetail').classList.add('open');
  document.getElementById('upOverlay').classList.add('open');
}

function closeDetail() {
  _active = null;
  document.getElementById('upDetail').classList.remove('open');
  document.getElementById('upOverlay').classList.remove('open');
}

function setStatus(val) {
  _pendingStatus = val;
  document.querySelectorAll('.up-action-btn').forEach(btn => btn.classList.toggle('current', btn.dataset.s === val));
}

async function saveUsulan() {
  if (!_active) return;
  const btn = document.getElementById('dpSaveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

  const r = await fetch(`/api/usulan/${_active.id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfT },
    body: JSON.stringify({
      status:     _pendingStatus,
      tanggapan:  document.getElementById('dpTanggapan').value.trim() || null,
    }),
  });
  const j = await r.json();
  if (j.success) { closeDetail(); load(); } else {
    btn.disabled = false; btn.innerHTML = '<i class="fa fa-floppy-disk"></i> Simpan';
    alert(j.message || 'Gagal menyimpan.');
  }
}

async function deleteUsulan(id) {
  if (!confirm('Hapus usulan ini?')) return;
  await fetch(`/api/usulan/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrfT } });
  closeDetail(); load();
}

function fld(label, val) {
  return `<div class="up-field-group"><div class="up-field-label">${label}</div><div class="up-field-val">${val}</div></div>`;
}
function formatDate(dt) {
  if (!dt) return '—';
  return new Date(dt).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
}
function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

load();
</script>
@endsection
