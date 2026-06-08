@extends('layouts.admin')
@section('title', 'Inventaris RT — Admin')
@section('content')
<style>
.inv-kondisi { font-size:10px; font-weight:700; padding:2px 9px; border-radius:99px; }
.inv-baik         { background:var(--forest-pale); color:var(--forest); }
.inv-rusak_ringan { background:var(--gold-pale); color:var(--gold-dark); }
.inv-rusak_berat  { background:#fdecea; color:var(--rust); }

#inv-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:400; align-items:center; justify-content:center; padding:1rem; }
#inv-modal.open { display:flex; }
.modal-box { background:#fff; border-radius:14px; padding:1.75rem; max-width:520px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.f-input { width:100%; padding:.55rem .75rem; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--ink); background:#fff; margin-bottom:.5rem; }
.f-input:focus { outline:none; border-color:var(--forest); }
.f-label { font-size:11px; font-weight:600; color:var(--ink-mid); margin-bottom:.25rem; display:block; }
.f-row { margin-bottom:.625rem; }
</style>

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Inventaris RT</div>
    <div class="admin-page-sub">Daftar aset dan perlengkapan milik RT 10</div>
  </div>
  <button class="btn-primary" onclick="openModal()"><i class="fa fa-plus"></i> Tambah Aset</button>
</div>

{{-- Filters --}}
<div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.25rem;align-items:center">
  <input id="search" placeholder="Cari nama atau kode..." oninput="debounceLoad()"
    style="padding:7px 14px;border:1.5px solid var(--border);border-radius:99px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;color:var(--ink);min-width:200px">
  <select id="f-kategori" onchange="load()" style="padding:7px 14px;border-radius:99px;border:1.5px solid var(--border);background:#fff;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--ink);cursor:pointer">
    <option value="">Semua Kategori</option>
    <option value="kursi">Kursi</option>
    <option value="meja">Meja</option>
    <option value="elektronik">Elektronik</option>
    <option value="peralatan">Peralatan</option>
    <option value="kendaraan">Kendaraan</option>
    <option value="lainnya">Lainnya</option>
  </select>
  <select id="f-kondisi" onchange="load()" style="padding:7px 14px;border-radius:99px;border:1.5px solid var(--border);background:#fff;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--ink);cursor:pointer">
    <option value="">Semua Kondisi</option>
    <option value="baik">Baik</option>
    <option value="rusak_ringan">Rusak Ringan</option>
    <option value="rusak_berat">Rusak Berat</option>
  </select>
  <span id="inv-count" style="font-size:12px;color:var(--ink-mute)"></span>
</div>

{{-- Summary cards --}}
<div id="inv-summary" style="display:grid;grid-template-columns:repeat(3,1fr);gap:.875rem;margin-bottom:1.5rem;max-width:480px">
  <div style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:.875rem 1rem">
    <div style="font-size:11px;color:var(--ink-soft);margin-bottom:4px">Total Aset</div>
    <div id="s-total" style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--forest)">—</div>
  </div>
  <div style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:.875rem 1rem">
    <div style="font-size:11px;color:var(--gold-dark);margin-bottom:4px">Rusak Ringan</div>
    <div id="s-ringan" style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--gold-dark)">—</div>
  </div>
  <div style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:.875rem 1rem">
    <div style="font-size:11px;color:var(--rust);margin-bottom:4px">Rusak Berat</div>
    <div id="s-berat" style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--rust)">—</div>
  </div>
</div>

{{-- Table --}}
<div style="background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden">
  <table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead>
      <tr style="background:var(--cream);border-bottom:1px solid var(--border)">
        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Nama Aset</th>
        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Kategori</th>
        <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Jml</th>
        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Lokasi</th>
        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Kondisi</th>
        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em">Harga Beli</th>
        <th style="padding:10px 16px;width:80px"></th>
      </tr>
    </thead>
    <tbody id="inv-tbody">
      <tr><td colspan="7" style="text-align:center;padding:2.5rem;color:var(--ink-mute)">Memuat...</td></tr>
    </tbody>
  </table>
</div>
<div id="inv-empty" style="display:none;text-align:center;padding:3rem 0;color:var(--ink-mute)">
  <i class="fa fa-boxes-stacked" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.75rem"></i>
  Belum ada data inventaris
</div>

{{-- Modal --}}
<div id="inv-modal">
  <div class="modal-box">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.1rem;color:var(--forest)" id="inv-modal-title">Tambah Aset</div>
      <button onclick="closeModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--ink-soft);line-height:1">&times;</button>
    </div>
    <input type="hidden" id="inv-edit-id">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.625rem">
      <div class="f-row" style="grid-column:1/-1">
        <label class="f-label">Nama Aset <span style="color:var(--rust)">*</span></label>
        <input class="f-input" id="inv-nama" placeholder="Nama aset" style="margin:0">
      </div>
      <div class="f-row">
        <label class="f-label">Kode Aset</label>
        <input class="f-input" id="inv-kode" placeholder="INV-001" style="margin:0">
      </div>
      <div class="f-row">
        <label class="f-label">Kategori <span style="color:var(--rust)">*</span></label>
        <select class="f-input" id="inv-kategori" style="margin:0;cursor:pointer">
          <option value="">— Pilih —</option>
          <option value="kursi">Kursi</option>
          <option value="meja">Meja</option>
          <option value="elektronik">Elektronik</option>
          <option value="peralatan">Peralatan</option>
          <option value="kendaraan">Kendaraan</option>
          <option value="lainnya">Lainnya</option>
        </select>
      </div>
      <div class="f-row">
        <label class="f-label">Jumlah</label>
        <input class="f-input" id="inv-jumlah" type="number" min="1" value="1" style="margin:0">
      </div>
      <div class="f-row">
        <label class="f-label">Kondisi</label>
        <select class="f-input" id="inv-kondisi" style="margin:0;cursor:pointer">
          <option value="baik">Baik</option>
          <option value="rusak_ringan">Rusak Ringan</option>
          <option value="rusak_berat">Rusak Berat</option>
        </select>
      </div>
      <div class="f-row">
        <label class="f-label">Lokasi Penyimpanan</label>
        <input class="f-input" id="inv-lokasi" placeholder="Pos RT, Gudang, dll." style="margin:0">
      </div>
      <div class="f-row">
        <label class="f-label">Tanggal Beli</label>
        <input class="f-input" id="inv-tanggal" type="date" style="margin:0">
      </div>
      <div class="f-row">
        <label class="f-label">Harga Beli (Rp)</label>
        <input class="f-input" id="inv-harga" type="number" min="0" placeholder="0" style="margin:0">
      </div>
      <div class="f-row" style="grid-column:1/-1">
        <label class="f-label">Keterangan</label>
        <textarea class="f-input" id="inv-keterangan" rows="2" placeholder="Catatan tambahan..." style="resize:vertical;margin:0"></textarea>
      </div>
    </div>

    <div id="inv-modal-msg" style="display:none;font-size:13px;padding:9px 13px;border-radius:8px;margin:.625rem 0"></div>
    <div style="display:flex;gap:.5rem;margin-top:.75rem">
      <button id="inv-save-btn" onclick="saveInv()" style="flex:1;background:var(--forest);color:#fff;border:none;border-radius:8px;padding:.65rem;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif">
        <i class="fa fa-check"></i> Simpan
      </button>
      <button onclick="closeModal()" style="padding:.65rem 18px;background:#f5f4f2;color:var(--ink-soft);border:none;border-radius:8px;font-size:13px;cursor:pointer;font-family:'DM Sans',sans-serif">Batal</button>
    </div>
  </div>
</div>

<script>
const _csrfT = '{{ csrf_token() }}';
let _data = [], _debounce;

async function load() {
  const s = document.getElementById('search').value.trim();
  const k = document.getElementById('f-kategori').value;
  const c = document.getElementById('f-kondisi').value;
  let url = '/api/inventaris?';
  if (s) url += `search=${encodeURIComponent(s)}&`;
  if (k) url += `kategori=${k}&`;
  if (c) url += `kondisi=${c}&`;

  const r = await fetch(url, { headers: { 'X-CSRF-TOKEN': _csrfT } });
  const json = await r.json();
  if (!json.success) return;
  _data = json.data;
  render();
}

function render() {
  const tbody = document.getElementById('inv-tbody');
  const empty = document.getElementById('inv-empty');
  document.getElementById('inv-count').textContent = _data.length + ' aset';

  // Summary
  document.getElementById('s-total').textContent   = _data.reduce((s, d) => s + (d.jumlah || 1), 0);
  document.getElementById('s-ringan').textContent  = _data.filter(d => d.kondisi === 'rusak_ringan').length;
  document.getElementById('s-berat').textContent   = _data.filter(d => d.kondisi === 'rusak_berat').length;

  if (!_data.length) { tbody.innerHTML = ''; empty.style.display = ''; return; }
  empty.style.display = 'none';

  const kLabel = { kondisi: { baik:'Baik', rusak_ringan:'Rusak Ringan', rusak_berat:'Rusak Berat' } };
  tbody.innerHTML = _data.map(d => `
    <tr style="border-bottom:1px solid var(--border)">
      <td style="padding:11px 16px">
        <div style="font-weight:600;color:var(--ink)">${escHtml(d.nama)}</div>
        ${d.kode ? `<div style="font-size:11px;color:var(--ink-mute);font-family:monospace">${escHtml(d.kode)}</div>` : ''}
      </td>
      <td style="padding:11px 16px;font-size:12px;color:var(--ink-soft);text-transform:capitalize">${escHtml(d.kategori)}</td>
      <td style="padding:11px 16px;text-align:center;font-weight:600;color:var(--ink)">${d.jumlah||1}</td>
      <td style="padding:11px 16px;font-size:12px;color:var(--ink-soft)">${d.lokasi ? escHtml(d.lokasi) : '—'}</td>
      <td style="padding:11px 16px"><span class="inv-kondisi inv-${d.kondisi}">${kLabel.kondisi[d.kondisi]||d.kondisi}</span></td>
      <td style="padding:11px 16px;font-size:12px;color:var(--ink-soft)">${d.harga_beli ? 'Rp '+parseInt(d.harga_beli).toLocaleString('id-ID') : '—'}</td>
      <td style="padding:11px 16px;text-align:right;display:flex;gap:.25rem;justify-content:flex-end">
        <button onclick="openModal(${d.id})" style="background:none;border:none;color:var(--forest);cursor:pointer;font-size:13px;padding:4px 7px;border-radius:6px" title="Edit">
          <i class="fa fa-pencil"></i>
        </button>
        <button onclick="deleteInv(${d.id})" style="background:none;border:none;color:var(--rust);cursor:pointer;font-size:13px;padding:4px 7px;border-radius:6px" title="Hapus">
          <i class="fa fa-trash"></i>
        </button>
      </td>
    </tr>
  `).join('');
}

function debounceLoad() { clearTimeout(_debounce); _debounce = setTimeout(load, 280); }

function openModal(id) {
  const existing = id ? _data.find(d => d.id === id) : null;
  document.getElementById('inv-modal-title').textContent = existing ? 'Edit Aset' : 'Tambah Aset';
  document.getElementById('inv-edit-id').value = existing ? existing.id : '';
  document.getElementById('inv-nama').value     = existing?.nama || '';
  document.getElementById('inv-kode').value     = existing?.kode || '';
  document.getElementById('inv-kategori').value = existing?.kategori || '';
  document.getElementById('inv-jumlah').value   = existing?.jumlah || 1;
  document.getElementById('inv-kondisi').value  = existing?.kondisi || 'baik';
  document.getElementById('inv-lokasi').value   = existing?.lokasi || '';
  document.getElementById('inv-tanggal').value  = existing?.tanggal_beli?.substring(0,10) || '';
  document.getElementById('inv-harga').value    = existing?.harga_beli || '';
  document.getElementById('inv-keterangan').value = existing?.keterangan || '';
  document.getElementById('inv-modal-msg').style.display = 'none';
  document.getElementById('inv-modal').classList.add('open');
}

function closeModal() { document.getElementById('inv-modal').classList.remove('open'); }

async function saveInv() {
  const nama     = document.getElementById('inv-nama').value.trim();
  const kategori = document.getElementById('inv-kategori').value;
  if (!nama)     { showMsg('Nama aset wajib diisi.', false); return; }
  if (!kategori) { showMsg('Pilih kategori.', false); return; }

  const editId = document.getElementById('inv-edit-id').value;
  const btn = document.getElementById('inv-save-btn');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

  const body = {
    nama, kategori,
    kode:         document.getElementById('inv-kode').value.trim() || null,
    jumlah:       parseInt(document.getElementById('inv-jumlah').value) || 1,
    kondisi:      document.getElementById('inv-kondisi').value,
    lokasi:       document.getElementById('inv-lokasi').value.trim() || null,
    tanggal_beli: document.getElementById('inv-tanggal').value || null,
    harga_beli:   parseInt(document.getElementById('inv-harga').value) || null,
    keterangan:   document.getElementById('inv-keterangan').value.trim() || null,
  };

  const url    = editId ? `/api/inventaris/${editId}` : '/api/inventaris';
  const method = editId ? 'PUT' : 'POST';

  const r = await fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfT },
    body: JSON.stringify(body),
  });
  const j = await r.json();
  btn.disabled = false; btn.innerHTML = '<i class="fa fa-check"></i> Simpan';
  if (j.success) { closeModal(); load(); } else { showMsg(j.message || 'Gagal menyimpan.', false); }
}

async function deleteInv(id) {
  if (!confirm('Hapus aset ini dari inventaris?')) return;
  await fetch(`/api/inventaris/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrfT } });
  load();
}

function showMsg(text, ok) {
  const el = document.getElementById('inv-modal-msg');
  el.textContent = text; el.style.display = '';
  el.style.background = ok ? '#E8F4ED' : '#FDECEA';
  el.style.color = ok ? 'var(--forest)' : 'var(--rust)';
}
function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

load();
</script>
@endsection
