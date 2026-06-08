@extends('layouts.admin')
@section('title', 'Bantuan Sosial — Admin')
@section('content')
<style>
.bs-jenis-badge { display:inline-block; font-size:10px; font-weight:700; padding:2px 9px; border-radius:99px; }
.bs-jenis-sembako     { background:#e8f5ec; color:var(--forest); }
.bs-jenis-uang_tunai  { background:var(--gold-pale); color:var(--gold-dark); }
.bs-jenis-kesehatan   { background:#e8f0fd; color:#2d5aa8; }
.bs-jenis-pendidikan  { background:#f3e8fd; color:#7c3aed; }
.bs-jenis-lainnya     { background:#efefed; color:var(--ink-soft); }

.bs-status-aktif   { background:var(--forest-pale); color:var(--forest); }
.bs-status-selesai { background:#efefed; color:var(--ink-soft); }

#add-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:400; align-items:center; justify-content:center; padding:1rem; }
#add-modal.open { display:flex; }
.modal-box { background:#fff; border-radius:14px; padding:1.75rem; max-width:480px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.f-input { width:100%; padding:.55rem .75rem; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--ink); background:#fff; margin-bottom:.625rem; }
.f-input:focus { outline:none; border-color:var(--forest); }
.f-label { font-size:11px; font-weight:600; color:var(--ink-mid); margin-bottom:.3rem; display:block; }
</style>

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Bantuan Sosial</div>
    <div class="admin-page-sub">Pencatatan penerima manfaat program bantuan RT</div>
  </div>
  <button class="btn-primary" onclick="openAdd()"><i class="fa fa-plus"></i> Tambah</button>
</div>

{{-- Filters --}}
<div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.25rem;align-items:center">
  <input id="search" placeholder="Cari nama penerima..." oninput="debounceLoad()"
    style="padding:7px 14px;border:1.5px solid var(--border);border-radius:99px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;color:var(--ink);min-width:200px">
  <select id="f-jenis" onchange="load()" style="padding:7px 14px;border:1.5px solid var(--border);border-radius:99px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;color:var(--ink);cursor:pointer">
    <option value="">Semua Jenis</option>
    <option value="sembako">Sembako</option>
    <option value="uang_tunai">Uang Tunai</option>
    <option value="kesehatan">Kesehatan</option>
    <option value="pendidikan">Pendidikan</option>
    <option value="lainnya">Lainnya</option>
  </select>
  <select id="f-status" onchange="load()" style="padding:7px 14px;border:1.5px solid var(--border);border-radius:99px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;color:var(--ink);cursor:pointer">
    <option value="">Semua Status</option>
    <option value="aktif">Aktif</option>
    <option value="selesai">Selesai</option>
  </select>
  <span id="bs-count" style="font-size:12px;color:var(--ink-mute);margin-left:.25rem"></span>
</div>

{{-- Summary cards --}}
<div id="summary-row" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.875rem;margin-bottom:1.5rem"></div>

{{-- Table --}}
<div style="background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden">
  <table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead>
      <tr style="background:var(--cream);border-bottom:1px solid var(--border)">
        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Nama Penerima</th>
        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Jenis</th>
        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Tanggal</th>
        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Keterangan</th>
        <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Status</th>
        <th style="padding:10px 16px;width:80px"></th>
      </tr>
    </thead>
    <tbody id="bs-tbody">
      <tr><td colspan="6" style="text-align:center;padding:2.5rem;color:var(--ink-mute)">Memuat...</td></tr>
    </tbody>
  </table>
</div>
<div id="bs-empty" style="display:none;text-align:center;padding:3rem 0;color:var(--ink-mute)">
  <i class="fa fa-hand-holding-heart" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.75rem"></i>
  Belum ada data bantuan sosial
</div>

{{-- Add modal --}}
<div id="add-modal">
  <div class="modal-box">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.1rem;color:var(--forest)" id="modal-title">Tambah Bantuan Sosial</div>
      <button onclick="closeAdd()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--ink-soft);line-height:1">&times;</button>
    </div>

    <input type="hidden" id="edit-id">

    <div style="margin-bottom:.625rem">
      <label class="f-label">Nama Penerima <span style="color:var(--rust)">*</span></label>
      <input class="f-input" id="f-nama" placeholder="Nama lengkap penerima">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.625rem;margin-bottom:.625rem">
      <div>
        <label class="f-label">Jenis Bantuan <span style="color:var(--rust)">*</span></label>
        <select class="f-input" id="f-jenis-modal" onchange="toggleNominal()" style="margin:0">
          <option value="">— Pilih —</option>
          <option value="sembako">Sembako</option>
          <option value="uang_tunai">Uang Tunai</option>
          <option value="kesehatan">Kesehatan</option>
          <option value="pendidikan">Pendidikan</option>
          <option value="lainnya">Lainnya</option>
        </select>
      </div>
      <div>
        <label class="f-label">Tanggal <span style="color:var(--rust)">*</span></label>
        <input class="f-input" id="f-tanggal" type="date" style="margin:0">
      </div>
    </div>
    <div id="nominal-row" style="display:none;margin-bottom:.625rem">
      <label class="f-label">Nominal (Rp)</label>
      <input class="f-input" id="f-nominal" type="number" min="0" placeholder="0" style="margin:0">
    </div>
    <div style="margin-bottom:1rem">
      <label class="f-label">Keterangan</label>
      <textarea class="f-input" id="f-keterangan" rows="3" placeholder="Catatan tambahan..." style="resize:vertical;min-height:70px;margin:0"></textarea>
    </div>
    <div id="modal-msg" style="display:none;font-size:13px;padding:9px 13px;border-radius:8px;margin-bottom:.875rem"></div>
    <div style="display:flex;gap:.5rem">
      <button id="modal-save-btn" onclick="saveBantuan()" style="flex:1;background:var(--forest);color:#fff;border:none;border-radius:8px;padding:.65rem;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif">
        <i class="fa fa-check"></i> Simpan
      </button>
      <button onclick="closeAdd()" style="padding:.65rem 18px;background:#f5f4f2;color:var(--ink-soft);border:none;border-radius:8px;font-size:13px;cursor:pointer;font-family:'DM Sans',sans-serif">Batal</button>
    </div>
  </div>
</div>

<script>
const _csrfT = '{{ csrf_token() }}';
let _data = [], _debounce;

async function load() {
  const s = document.getElementById('search').value.trim();
  const j = document.getElementById('f-jenis').value;
  const st = document.getElementById('f-status').value;
  let url = '/api/bantuan-sosial?';
  if (s) url += `search=${encodeURIComponent(s)}&`;
  if (j) url += `jenis=${j}&`;
  if (st) url += `status=${st}&`;

  const r = await fetch(url, { headers: { 'X-CSRF-TOKEN': _csrfT } });
  const json = await r.json();
  if (!json.success) return;
  _data = json.data;
  render();
}

function render() {
  const tbody = document.getElementById('bs-tbody');
  const empty = document.getElementById('bs-empty');
  document.getElementById('bs-count').textContent = _data.length + ' data';

  // Summary
  const byJenis = {};
  _data.forEach(d => { byJenis[d.jenis] = (byJenis[d.jenis] || 0) + 1; });
  const jenisLabel = { sembako:'Sembako', uang_tunai:'Uang Tunai', kesehatan:'Kesehatan', pendidikan:'Pendidikan', lainnya:'Lainnya' };
  document.getElementById('summary-row').innerHTML = Object.entries(byJenis).map(([k, v]) => `
    <div style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:.875rem 1rem">
      <div style="font-size:11px;color:var(--ink-soft);margin-bottom:4px">${jenisLabel[k]||k}</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--forest)">${v}</div>
    </div>
  `).join('');

  if (!_data.length) {
    tbody.innerHTML = '';
    empty.style.display = '';
    return;
  }
  empty.style.display = 'none';
  tbody.innerHTML = _data.map(d => `
    <tr style="border-bottom:1px solid var(--border)">
      <td style="padding:11px 16px">
        <div style="font-weight:600;color:var(--ink)">${escHtml(d.nama_penerima)}</div>
        ${d.unit_rumah ? `<div style="font-size:11px;color:var(--ink-mute)">Blok ${d.unit_rumah.blok}${d.unit_rumah.nomor}</div>` : ''}
      </td>
      <td style="padding:11px 16px">
        <span class="bs-jenis-badge bs-jenis-${d.jenis}">${jenisLabel[d.jenis]||d.jenis}</span>
        ${d.nominal ? `<div style="font-size:11px;color:var(--ink-soft);margin-top:3px">Rp ${parseInt(d.nominal).toLocaleString('id-ID')}</div>` : ''}
      </td>
      <td style="padding:11px 16px;font-size:12px;color:var(--ink-soft)">${formatDate(d.tanggal)}</td>
      <td style="padding:11px 16px;font-size:12px;color:var(--ink-soft);max-width:200px">${d.keterangan ? escHtml(d.keterangan).substring(0,60) : '—'}</td>
      <td style="padding:11px 16px;text-align:center">
        <span class="bs-jenis-badge bs-status-${d.status}" style="cursor:pointer" onclick="toggleStatus(${d.id},'${d.status}')">
          ${d.status === 'aktif' ? 'Aktif' : 'Selesai'}
        </span>
      </td>
      <td style="padding:11px 16px;text-align:right">
        <button onclick="deleteBantuan(${d.id})" style="background:none;border:none;color:var(--rust);cursor:pointer;font-size:13px;padding:4px 8px;border-radius:6px" title="Hapus">
          <i class="fa fa-trash"></i>
        </button>
      </td>
    </tr>
  `).join('');
}

function debounceLoad() {
  clearTimeout(_debounce);
  _debounce = setTimeout(load, 280);
}

function openAdd() {
  document.getElementById('edit-id').value = '';
  document.getElementById('f-nama').value = '';
  document.getElementById('f-jenis-modal').value = '';
  document.getElementById('f-tanggal').value = new Date().toLocaleDateString('sv');
  document.getElementById('f-nominal').value = '';
  document.getElementById('f-keterangan').value = '';
  document.getElementById('nominal-row').style.display = 'none';
  document.getElementById('modal-msg').style.display = 'none';
  document.getElementById('modal-title').textContent = 'Tambah Bantuan Sosial';
  document.getElementById('add-modal').classList.add('open');
}

function closeAdd() { document.getElementById('add-modal').classList.remove('open'); }

function toggleNominal() {
  const j = document.getElementById('f-jenis-modal').value;
  document.getElementById('nominal-row').style.display = j === 'uang_tunai' ? '' : 'none';
}

async function saveBantuan() {
  const nama    = document.getElementById('f-nama').value.trim();
  const jenis   = document.getElementById('f-jenis-modal').value;
  const tanggal = document.getElementById('f-tanggal').value;
  const msg = document.getElementById('modal-msg');

  if (!nama)    { showModalMsg('Nama penerima wajib diisi.', false); return; }
  if (!jenis)   { showModalMsg('Pilih jenis bantuan.', false); return; }
  if (!tanggal) { showModalMsg('Tanggal wajib diisi.', false); return; }

  const btn = document.getElementById('modal-save-btn');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

  const body = {
    nama_penerima: nama, jenis, tanggal,
    keterangan: document.getElementById('f-keterangan').value.trim() || null,
    nominal: jenis === 'uang_tunai' ? (parseInt(document.getElementById('f-nominal').value) || null) : null,
  };

  const r = await fetch('/api/bantuan-sosial', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfT },
    body: JSON.stringify(body),
  });
  const j = await r.json();
  btn.disabled = false; btn.innerHTML = '<i class="fa fa-check"></i> Simpan';
  if (j.success) { closeAdd(); load(); } else { showModalMsg(j.message || 'Gagal menyimpan.', false); }
}

async function toggleStatus(id, current) {
  const next = current === 'aktif' ? 'selesai' : 'aktif';
  await fetch(`/api/bantuan-sosial/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfT },
    body: JSON.stringify({ status: next }),
  });
  load();
}

async function deleteBantuan(id) {
  if (!confirm('Hapus data bantuan ini?')) return;
  await fetch(`/api/bantuan-sosial/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrfT } });
  load();
}

function showModalMsg(text, ok) {
  const el = document.getElementById('modal-msg');
  el.textContent = text; el.style.display = '';
  el.style.background = ok ? '#E8F4ED' : '#FDECEA';
  el.style.color = ok ? 'var(--forest)' : 'var(--rust)';
}
function formatDate(dt) {
  if (!dt) return '—';
  return new Date(dt + 'T00:00:00').toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
}
function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

load();
</script>
@endsection
