@extends('layouts.admin')
@section('title', 'Data Kendaraan — Admin')
@section('content')
<style>
.kend-filters { display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; margin-bottom:1.25rem; }
.kend-search { flex:1; min-width:200px; border:1.5px solid var(--border); border-radius:8px; padding:.5rem .875rem; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--ink); background:#fff; }
.kend-search:focus { outline:none; border-color:var(--forest); }
.kend-sel { border:1.5px solid var(--border); border-radius:8px; padding:.5rem .75rem; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--ink); background:#fff; }

.kend-table { width:100%; border-collapse:separate; border-spacing:0; }
.kend-table th { font-size:11px; font-weight:600; color:var(--ink-mid); text-align:left; padding:.625rem 1rem; background:var(--cream); border-bottom:1px solid var(--border); white-space:nowrap; }
.kend-table th:first-child { border-radius:8px 0 0 0; }
.kend-table th:last-child  { border-radius:0 8px 0 0; }
.kend-table td { font-size:13px; color:var(--ink); padding:.75rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.kend-table tr:last-child td { border-bottom:none; }
.kend-table tr:hover td { background:var(--forest-pale); }
.kend-table .wrap { background:var(--card); border:1px solid var(--border); border-radius:10px; overflow:hidden; }

.jenis-badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:600; padding:3px 10px; border-radius:99px; }
.jenis-mobil  { background:#e8f0fd; color:#2d5aa8; }
.jenis-motor  { background:#e8f5ec; color:var(--forest); }
.jenis-lain   { background:var(--gold-pale); color:var(--gold-dark); }

.kend-actions { display:flex; gap:.375rem; }
.kend-btn { padding:5px 12px; border-radius:99px; border:1.5px solid var(--border); background:#fff; font-size:12px; font-weight:500; font-family:'DM Sans',sans-serif; cursor:pointer; display:inline-flex; align-items:center; gap:5px; color:var(--ink-soft); transition:.15s; }
.kend-btn:hover { border-color:var(--forest); color:var(--forest); background:var(--forest-pale); }
.kend-btn.danger:hover { border-color:var(--rust); color:var(--rust); background:#fdecea; }

/* Modal */
.kend-modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:400; align-items:center; justify-content:center; padding:1rem; }
.kend-modal-bg.open { display:flex; }
.kend-modal { background:#fff; border-radius:var(--radius); padding:1.75rem; width:100%; max-width:min(480px,calc(100vw - 2rem)); box-shadow:var(--shadow-lg); }
.kend-modal-title { font-family:'DM Serif Display',serif; font-size:1.15rem; color:var(--forest); margin-bottom:1.25rem; }
.kend-field { margin-bottom:.875rem; }
.kend-field label { font-size:11px; font-weight:600; color:var(--ink-mid); display:block; margin-bottom:.3rem; text-transform:uppercase; letter-spacing:.05em; }
.kend-field input, .kend-field select {
  width:100%; border:1.5px solid var(--border); border-radius:8px; padding:.5rem .75rem;
  font-size:13px; font-family:'DM Sans',sans-serif; color:var(--ink); box-sizing:border-box;
}
.kend-field input:focus, .kend-field select:focus { outline:none; border-color:var(--forest); }
.kend-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
.kend-modal-footer { display:flex; gap:.5rem; justify-content:flex-end; margin-top:1.25rem; }
</style>

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Data Kendaraan</div>
    <div class="admin-page-sub">Registrasi kendaraan warga RT 10</div>
  </div>
  <button class="btn-primary" onclick="openModal()">
    <i class="fa fa-plus"></i> Tambah Kendaraan
  </button>
</div>

{{-- Filters --}}
<div class="kend-filters">
  <input type="text" class="kend-search" id="kend-search" placeholder="Cari plat nomor, merek, nama pemilik..." oninput="debounceLoad()">
  <select class="kend-sel" id="kend-jenis" onchange="load()">
    <option value="">Semua Jenis</option>
    <option value="Mobil">Mobil</option>
    <option value="Motor">Motor</option>
    <option value="Lainnya">Lainnya</option>
  </select>
  <span id="kend-count" style="font-size:12px;color:var(--ink-mute);white-space:nowrap"></span>
</div>

{{-- Table --}}
<div class="kend-table wrap" id="kend-wrap">
  <div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div>
</div>

{{-- Add/Edit Modal --}}
<div class="kend-modal-bg" id="kend-modal" onclick="if(event.target===this)closeModal()">
  <div class="kend-modal">
    <div class="kend-modal-title" id="modal-title">Tambah Kendaraan</div>

    <div class="kend-field" id="kk-field">
      <label>Pemilik (Kepala Keluarga)</label>
      <select id="f-kk" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--ink)">
        <option value="">— Pilih pemilik —</option>
      </select>
    </div>

    <div class="kend-grid-2">
      <div class="kend-field">
        <label>Jenis <span style="color:var(--rust)">*</span></label>
        <select id="f-jenis">
          <option value="Mobil">Mobil</option>
          <option value="Motor">Motor</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>
      <div class="kend-field">
        <label>Merek <span style="color:var(--rust)">*</span></label>
        <input type="text" id="f-merek" placeholder="Toyota, Honda, ...">
      </div>
    </div>

    <div class="kend-grid-2">
      <div class="kend-field">
        <label>Model / Tipe</label>
        <input type="text" id="f-model" placeholder="Avanza, Vario, ...">
      </div>
      <div class="kend-field">
        <label>Warna</label>
        <input type="text" id="f-warna" placeholder="Putih, Hitam, ...">
      </div>
    </div>

    <div class="kend-grid-2">
      <div class="kend-field">
        <label>Plat Nomor</label>
        <input type="text" id="f-plat" placeholder="B 1234 ABC" style="text-transform:uppercase">
      </div>
      <div class="kend-field">
        <label>Tahun</label>
        <input type="number" id="f-tahun" placeholder="{{ date('Y') }}" min="1990" max="{{ date('Y') + 1 }}">
      </div>
    </div>

    <div id="modal-msg" style="font-size:12px;color:var(--rust);display:none;margin-bottom:.5rem"></div>

    <div class="kend-modal-footer">
      <button class="btn-secondary" onclick="closeModal()">Batal</button>
      <button class="btn-primary" id="modal-save-btn" onclick="saveKendaraan()">
        <i class="fa fa-floppy-disk"></i> Simpan
      </button>
    </div>
  </div>
</div>

<script>
const _csrf = '{{ csrf_token() }}';
let _all = [], _kkList = [], _editId = null, _debounce = null;

const JENIS_CLS = { Mobil:'jenis-mobil', Motor:'jenis-motor' };
const JENIS_ICON = { Mobil:'fa-car', Motor:'fa-motorcycle', Lainnya:'fa-truck' };

function jenisBadge(j) {
  const cls  = JENIS_CLS[j] || 'jenis-lain';
  const icon = JENIS_ICON[j] || 'fa-car-side';
  return `<span class="jenis-badge ${cls}"><i class="fa ${icon}"></i> ${j}</span>`;
}

function esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function debounceLoad() {
  clearTimeout(_debounce);
  _debounce = setTimeout(load, 280);
}

async function load() {
  const search = document.getElementById('kend-search').value.trim();
  const jenis  = document.getElementById('kend-jenis').value;
  let url = '/api/kependudukan/kendaraan?';
  if (search) url += 'search=' + encodeURIComponent(search) + '&';
  if (jenis)  url += 'jenis='  + encodeURIComponent(jenis);

  const r = await fetch(url);
  const j = await r.json();
  if (!j.success) return;
  _all = j.data;
  render();
}

function render() {
  const wrap = document.getElementById('kend-wrap');
  document.getElementById('kend-count').textContent = `${_all.length} kendaraan`;

  if (!_all.length) {
    wrap.innerHTML = `<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Tidak ada kendaraan ditemukan.</div>`;
    return;
  }

  wrap.innerHTML = `<table class="kend-table">
    <thead>
      <tr>
        <th>Jenis</th>
        <th>Merek / Model</th>
        <th>Plat Nomor</th>
        <th>Warna</th>
        <th>Tahun</th>
        <th>Pemilik</th>
        <th>Unit</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      ${_all.map(k => {
        const kk   = k.kepala_keluarga;
        const unit = kk?.unit_rumah ? `Blok ${kk.unit_rumah.blok}/${kk.unit_rumah.nomor}` : '—';
        const nama = kk?.nama || '—';
        const model = k.model ? ` <span style="color:var(--ink-mute);font-weight:400">${esc(k.model)}</span>` : '';
        return `<tr>
          <td>${jenisBadge(k.jenis)}</td>
          <td style="font-weight:600">${esc(k.merek)}${model}</td>
          <td><span style="font-family:monospace;font-size:13px;font-weight:700;letter-spacing:.04em;color:var(--ink)">${esc(k.plat_nomor||'—')}</span></td>
          <td>${esc(k.warna||'—')}</td>
          <td>${k.tahun||'—'}</td>
          <td>
            <a href="/admin/kependudukan/warga/${kk?.id||''}" style="color:var(--forest);font-weight:500">${esc(nama)}</a>
          </td>
          <td><span style="font-size:12px;color:var(--ink-mute)">${unit}</span></td>
          <td>
            <div class="kend-actions">
              <button class="kend-btn" onclick="openEdit(${k.id})"><i class="fa fa-pen"></i></button>
              <button class="kend-btn danger" onclick="hapus(${k.id})"><i class="fa fa-trash"></i></button>
            </div>
          </td>
        </tr>`;
      }).join('')}
    </tbody>
  </table>`;
}

async function loadKkList() {
  if (_kkList.length) return;
  const r = await fetch('/api/kependudukan/kepala?all=1');
  const j = await r.json();
  if (!j.success) return;
  _kkList = j.data || [];
  const sel = document.getElementById('f-kk');
  sel.innerHTML = '<option value="">— Pilih pemilik —</option>' +
    _kkList.map(kk => {
      const unit = kk.unit_rumah ? ` (Blok ${kk.unit_rumah.blok}/${kk.unit_rumah.nomor})` : '';
      return `<option value="${kk.id}">${esc(kk.nama)}${unit}</option>`;
    }).join('');
}

function openModal(prefillKkId = null) {
  _editId = null;
  document.getElementById('modal-title').textContent = 'Tambah Kendaraan';
  document.getElementById('kk-field').style.display  = '';
  document.getElementById('f-kk').value    = prefillKkId || '';
  document.getElementById('f-jenis').value = 'Mobil';
  document.getElementById('f-merek').value = '';
  document.getElementById('f-model').value = '';
  document.getElementById('f-warna').value = '';
  document.getElementById('f-plat').value  = '';
  document.getElementById('f-tahun').value = '';
  document.getElementById('modal-msg').style.display = 'none';
  loadKkList();
  document.getElementById('kend-modal').classList.add('open');
}

function openEdit(id) {
  const k = _all.find(x => x.id === id);
  if (!k) return;
  _editId = id;
  document.getElementById('modal-title').textContent = 'Edit Kendaraan';
  document.getElementById('kk-field').style.display  = 'none';
  document.getElementById('f-jenis').value = k.jenis || 'Mobil';
  document.getElementById('f-merek').value = k.merek || '';
  document.getElementById('f-model').value = k.model || '';
  document.getElementById('f-warna').value = k.warna || '';
  document.getElementById('f-plat').value  = k.plat_nomor || '';
  document.getElementById('f-tahun').value = k.tahun || '';
  document.getElementById('modal-msg').style.display = 'none';
  document.getElementById('kend-modal').classList.add('open');
}

function closeModal() {
  document.getElementById('kend-modal').classList.remove('open');
  _editId = null;
}

async function saveKendaraan() {
  const btn    = document.getElementById('modal-save-btn');
  const msgEl  = document.getElementById('modal-msg');
  const jenis  = document.getElementById('f-jenis').value;
  const merek  = document.getElementById('f-merek').value.trim();
  const model  = document.getElementById('f-model').value.trim();
  const warna  = document.getElementById('f-warna').value.trim();
  const plat   = document.getElementById('f-plat').value.trim().toUpperCase();
  const tahun  = document.getElementById('f-tahun').value.trim();
  const kkId   = document.getElementById('f-kk').value;

  if (!merek) { showModalMsg('Merek wajib diisi.'); return; }
  if (!_editId && !kkId) { showModalMsg('Pilih pemilik kendaraan.'); return; }

  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';

  const body = { jenis, merek, model: model||null, warna: warna||null, plat_nomor: plat||null, tahun: tahun ? parseInt(tahun) : null };
  const url    = _editId
    ? `/api/kependudukan/kendaraan/${_editId}`
    : `/api/kependudukan/kepala/${kkId}/kendaraan`;
  const method = _editId ? 'PUT' : 'POST';

  const r = await fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
    body: JSON.stringify(body),
  });
  const j = await r.json();

  btn.disabled = false;
  btn.innerHTML = '<i class="fa fa-floppy-disk"></i> Simpan';

  if (j.success) {
    closeModal();
    load();
  } else {
    showModalMsg(j.message || 'Gagal menyimpan.');
  }
}

function showModalMsg(msg) {
  const el = document.getElementById('modal-msg');
  el.textContent   = msg;
  el.style.display = '';
}

async function hapus(id) {
  if (!confirm('Hapus data kendaraan ini?')) return;
  const r = await fetch(`/api/kependudukan/kendaraan/${id}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': _csrf },
  });
  const j = await r.json();
  if (j.success) load();
}

load();
</script>
@endsection
