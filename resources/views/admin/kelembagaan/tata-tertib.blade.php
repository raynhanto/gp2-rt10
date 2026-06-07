@extends('layouts.admin')
@section('title', 'Tata Tertib')
@section('content')
<div class="container" style="max-width:900px">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <div class="section-label">Kelembagaan</div>
      <div class="section-title">Tata Tertib RT 10</div>
    </div>
    <button onclick="openForm()" class="btn-primary"><i class="fa fa-plus" style="font-size:11px"></i> Tambah Peraturan</button>
  </div>

  <div style="font-size:12px;color:var(--ink-mute);margin-bottom:1rem" id="tt-count"></div>

  <div id="tt-list" style="display:flex;flex-direction:column;gap:.75rem"></div>
  <div id="tt-empty" style="display:none;text-align:center;padding:3rem;background:#fff;border-radius:var(--radius);border:1px solid var(--border);color:var(--ink-soft)">
    <i class="fa fa-scale-balanced" style="font-size:2rem;display:block;margin-bottom:10px;color:var(--ink-mute)"></i>
    Belum ada peraturan. Klik "Tambah Peraturan" untuk mulai.
  </div>
</div>

{{-- Modal --}}
<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:520px;max-height:92vh;overflow-y:auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest)" id="form-title">Tambah Peraturan</div>
      <button onclick="closeForm()" style="border:none;background:none;cursor:pointer;font-size:20px;color:var(--ink-mute)"><i class="fa fa-xmark"></i></button>
    </div>
    <input type="hidden" id="f-id">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Kategori *</label>
        <input type="text" id="f-kategori" placeholder="Cth: Keamanan, Kebersihan" list="kat-list" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
        <datalist id="kat-list" id="kat-list"></datalist>
      </div>
      <div>
        <label class="flabel">Nomor Pasal</label>
        <input type="text" id="f-pasal" placeholder="Cth: Pasal 1, 1.1" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
      </div>
      <div style="grid-column:span 2">
        <label class="flabel">Judul *</label>
        <input type="text" id="f-judul" placeholder="Judul peraturan" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
      </div>
      <div style="grid-column:span 2">
        <label class="flabel">Isi Peraturan *</label>
        <textarea id="f-isi" rows="5" placeholder="Tuliskan isi peraturan..." style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;resize:vertical;font-family:'DM Sans',sans-serif;box-sizing:border-box;line-height:1.65"></textarea>
      </div>
      <div>
        <label class="flabel">Urutan</label>
        <input type="number" id="f-urutan" value="0" min="0" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
      </div>
      <div style="display:flex;align-items:flex-end;padding-bottom:2px">
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
          <input type="checkbox" id="f-active" checked style="accent-color:var(--forest);width:15px;height:15px">
          <span>Aktif (tampil publik)</span>
        </label>
      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button onclick="save()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeForm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

<style>
.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}
.tt-row{background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem 1.25rem;display:flex;gap:1rem;align-items:flex-start}
.tt-row.inactive{opacity:.5}
</style>
@endsection
@section('scripts')
<script>
const _csrfToken = '{{ csrf_token() }}';
let _all = [];

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

async function load() {
  const r = await fetch('/api/tata-tertib');
  const j = await r.json();
  _all = j.success ? j.data : [];
  // Populate category datalist
  const kats = [...new Set(_all.map(t => t.kategori))];
  document.getElementById('kat-list').innerHTML = kats.map(k => `<option value="${esc(k)}">`).join('');
  renderList();
}

function renderList() {
  const el  = document.getElementById('tt-list');
  const emp = document.getElementById('tt-empty');
  document.getElementById('tt-count').textContent = `${_all.length} peraturan`;
  if (!_all.length) { el.innerHTML = ''; emp.style.display = ''; return; }
  emp.style.display = 'none';

  // Group by kategori
  const groups = {};
  _all.forEach(t => {
    if (!groups[t.kategori]) groups[t.kategori] = [];
    groups[t.kategori].push(t);
  });

  el.innerHTML = Object.entries(groups).map(([kat, items]) => `
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
      <div style="background:var(--forest);color:#fff;padding:.65rem 1.25rem;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;display:flex;align-items:center;gap:8px">
        <i class="fa fa-gavel"></i> ${esc(kat)}
        <span style="margin-left:auto;font-size:10px;opacity:.7">${items.length} peraturan</span>
      </div>
      ${items.map(t => `
        <div class="tt-row${t.is_active ? '' : ' inactive'}" style="border-bottom:1px solid var(--border)">
          <div style="flex:1;min-width:0">
            ${t.pasal ? `<div style="font-size:10px;font-weight:700;color:var(--forest-light);text-transform:uppercase;letter-spacing:.07em;margin-bottom:2px">${esc(t.pasal)}</div>` : ''}
            <div style="font-weight:600;font-size:13px;color:var(--ink);margin-bottom:4px">${esc(t.judul)}</div>
            <div style="font-size:12px;color:var(--ink-soft);line-height:1.6">${esc(t.isi)}</div>
            ${!t.is_active ? `<div style="font-size:10px;color:var(--ink-mute);margin-top:4px"><i class="fa fa-eye-slash"></i> Tidak aktif</div>` : ''}
          </div>
          <div style="display:flex;flex-direction:column;gap:5px;flex-shrink:0">
            <button onclick='editRule(${JSON.stringify(t).replace(/'/g,"&#39;")})' class="btn-secondary" style="padding:6px 12px;font-size:11px"><i class="fa fa-pen"></i></button>
            <button onclick="delRule(${t.id})" style="padding:6px 12px;font-size:11px;border:1px solid var(--border);border-radius:var(--radius-sm);background:#fff;cursor:pointer;color:var(--rust)"><i class="fa fa-trash"></i></button>
          </div>
        </div>`).join('')}
    </div>`).join('');
}

function openForm() {
  document.getElementById('f-id').value       = '';
  document.getElementById('f-kategori').value = '';
  document.getElementById('f-pasal').value    = '';
  document.getElementById('f-judul').value    = '';
  document.getElementById('f-isi').value      = '';
  document.getElementById('f-urutan').value   = '0';
  document.getElementById('f-active').checked = true;
  document.getElementById('form-title').textContent = 'Tambah Peraturan';
  document.getElementById('form-overlay').style.display = 'flex';
}

function closeForm() { document.getElementById('form-overlay').style.display = 'none'; }

function editRule(t) {
  document.getElementById('f-id').value       = t.id;
  document.getElementById('f-kategori').value = t.kategori;
  document.getElementById('f-pasal').value    = t.pasal || '';
  document.getElementById('f-judul').value    = t.judul;
  document.getElementById('f-isi').value      = t.isi;
  document.getElementById('f-urutan').value   = t.urutan;
  document.getElementById('f-active').checked = !!t.is_active;
  document.getElementById('form-title').textContent = 'Edit Peraturan';
  document.getElementById('form-overlay').style.display = 'flex';
}

async function save() {
  const id       = document.getElementById('f-id').value;
  const kategori = document.getElementById('f-kategori').value.trim();
  const judul    = document.getElementById('f-judul').value.trim();
  const isi      = document.getElementById('f-isi').value.trim();
  if (!kategori || !judul || !isi) { alert('Kategori, judul, dan isi wajib diisi.'); return; }

  const body = {
    kategori,
    pasal:     document.getElementById('f-pasal').value.trim() || null,
    judul, isi,
    urutan:    parseInt(document.getElementById('f-urutan').value) || 0,
    is_active: document.getElementById('f-active').checked,
  };

  const url = id ? `/api/tata-tertib/${id}` : '/api/tata-tertib';
  const method = id ? 'PUT' : 'POST';
  const r = await fetch(url, { method, headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken}, body:JSON.stringify(body) });
  const j = await r.json();
  if (j.success) { closeForm(); showToast(j.message); load(); }
  else alert(j.message);
}

async function delRule(id) {
  if (!confirm('Hapus peraturan ini?')) return;
  const r = await fetch(`/api/tata-tertib/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  if (j.success) { showToast(j.message); load(); }
  else alert(j.message);
}

load();
</script>
@endsection
