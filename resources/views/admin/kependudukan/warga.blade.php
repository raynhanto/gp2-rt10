@extends('layouts.admin')
@section('title', 'Data Warga')
@section('content')
<div class="container">

  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <a href="/admin/kependudukan" style="font-size:13px;color:var(--ink-soft)">← Kependudukan</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Data Warga</div>
    </div>
    <button onclick="openModal()" class="btn-primary">
      <i class="fa fa-plus"></i> Tambah KK
    </button>
  </div>

  {{-- Filter --}}
  <div class="card" style="margin-bottom:1.5rem;padding:1rem 1.25rem">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
      <input id="search-input" type="text" placeholder="Cari nama / NIK / No KK..."
        style="flex:1;min-width:200px;padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none"
        oninput="debounceLoad()">
      <select id="blok-filter" onchange="loadWarga(1)"
        style="padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
        <option value="">Semua Blok</option>
        @foreach(range('A','Z') as $b)
        <option value="{{ $b }}">Blok {{ $b }}</option>
        @endforeach
      </select>
      <button onclick="loadWarga(1)" class="btn-secondary" style="padding:8px 16px;font-size:13px">
        <i class="fa fa-search"></i>
      </button>
    </div>
  </div>

  {{-- Table --}}
  <div class="card" style="padding:0;overflow:hidden">
    <div id="warga-table">
      <div style="padding:3rem;text-align:center;color:var(--ink-soft)">Memuat...</div>
    </div>
    <div id="pagination" style="padding:1rem 1.5rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center"></div>
  </div>

</div>

{{-- Modal Tambah/Edit KK --}}
<div id="modal-bg" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:500;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);width:100%;max-width:560px;max-height:90vh;overflow-y:auto;margin:1rem;box-shadow:var(--shadow-lg)">
    <div style="padding:1.5rem 1.5rem 0;display:flex;justify-content:space-between;align-items:center">
      <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest)" id="modal-title">Tambah Kepala Keluarga</div>
      <button onclick="closeModal()" style="background:none;border:none;cursor:pointer;font-size:18px;color:var(--ink-soft)">✕</button>
    </div>
    <div style="padding:1.5rem">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div style="grid-column:1/-1">
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Nama Lengkap *</label>
          <input id="f-nama" type="text" placeholder="Nama sesuai KTP"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">NIK</label>
          <input id="f-nik" type="text" maxlength="16" placeholder="16 digit"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">No. Kartu Keluarga</label>
          <input id="f-no-kk" type="text" maxlength="16" placeholder="16 digit"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Unit Rumah</label>
          <select id="f-unit" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="">— Pilih Unit —</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Tempat Lahir</label>
          <input id="f-tempat-lahir" type="text"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Tanggal Lahir</label>
          <input id="f-tanggal-lahir" type="date"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Jenis Kelamin</label>
          <select id="f-jk" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="">— Pilih —</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Status Perkawinan</label>
          <select id="f-kawin" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="">— Pilih —</option>
            <option value="belum_kawin">Belum Kawin</option>
            <option value="kawin">Kawin</option>
            <option value="cerai_hidup">Cerai Hidup</option>
            <option value="cerai_mati">Cerai Mati</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Agama</label>
          <select id="f-agama" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="">— Pilih —</option>
            <option value="islam">Islam</option>
            <option value="kristen">Kristen</option>
            <option value="katolik">Katolik</option>
            <option value="hindu">Hindu</option>
            <option value="budha">Buddha</option>
            <option value="konghucu">Konghucu</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Pendidikan</label>
          <select id="f-pendidikan" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="">— Pilih —</option>
            <option value="sd">SD</option><option value="smp">SMP</option>
            <option value="sma">SMA/SMK</option><option value="d3">D3</option>
            <option value="s1">S1</option><option value="s2">S2</option>
            <option value="s3">S3</option><option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Pekerjaan</label>
          <input id="f-pekerjaan" type="text"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">No. WhatsApp</label>
          <input id="f-wa" type="text" placeholder="628xxx"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Status Tinggal</label>
          <select id="f-tinggal" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="tetap">Tetap (Pemilik)</option>
            <option value="kontrak">Kontrak</option>
            <option value="kos">Kos</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div style="grid-column:1/-1">
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Keterangan</label>
          <textarea id="f-keterangan" rows="2" placeholder="Opsional..."
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;resize:vertical"></textarea>
        </div>
      </div>

      <div id="modal-err" style="display:none;margin-top:1rem;padding:10px 14px;background:#FDECEA;border-radius:8px;font-size:13px;color:var(--rust)"></div>

      <div style="display:flex;gap:10px;margin-top:1.5rem;justify-content:flex-end">
        <button onclick="closeModal()" class="btn-secondary" style="padding:10px 22px">Batal</button>
        <button onclick="saveKK()" class="btn-primary" id="save-btn" style="padding:10px 22px">Simpan</button>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script>
let _editId  = null;
let _page    = 1;
let _debounce = null;
const _BLOK  = ['A','B','C','D','E','F','G','H'];

function debounceLoad() {
  clearTimeout(_debounce);
  _debounce = setTimeout(() => loadWarga(1), 400);
}

async function loadWarga(page = 1) {
  _page = page;
  const search = document.getElementById('search-input').value;
  const blok   = document.getElementById('blok-filter').value;
  const params = new URLSearchParams({ page, per_page: 20 });
  if (search) params.set('search', search);
  if (blok)   params.set('blok', blok);

  document.getElementById('warga-table').innerHTML =
    '<div style="padding:3rem;text-align:center;color:var(--ink-soft)">Memuat...</div>';

  try {
    const r = await fetch('/api/kependudukan/kepala?' + params);
    const j = await r.json();
    if (!j.success) { renderError(); return; }
    renderTable(j.data, j.meta);
  } catch(e) { renderError(); }
}

function renderError() {
  document.getElementById('warga-table').innerHTML =
    '<div style="padding:3rem;text-align:center;color:var(--rust)">Gagal memuat data.</div>';
}

const JK = { L: 'Laki-laki', P: 'Perempuan' };
const TINGGAL = { tetap: 'Pemilik', kontrak: 'Kontrak', kos: 'Kos', lainnya: 'Lainnya' };
const TINGGAL_COLOR = { tetap: 'var(--forest)', kontrak: '#2D5AA8', kos: '#C8A030', lainnya: 'var(--ink-soft)' };

function renderTable(data, meta) {
  if (!data.length) {
    document.getElementById('warga-table').innerHTML =
      '<div style="padding:3rem;text-align:center;color:var(--ink-soft)">Tidak ada data ditemukan.</div>';
    document.getElementById('pagination').innerHTML = '';
    return;
  }

  document.getElementById('warga-table').innerHTML = `
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="border-bottom:2px solid var(--border)">
          <th style="padding:10px 20px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft)">Nama</th>
          <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft)">Unit</th>
          <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft)">NIK</th>
          <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft)">Status</th>
          <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft)">Aksi</th>
        </tr>
      </thead>
      <tbody>
        ${data.map(d => `
          <tr style="border-bottom:1px solid var(--border);transition:background 0.15s" onmouseover="this.style.background='var(--warm)'" onmouseout="this.style.background=''">
            <td style="padding:12px 20px">
              <div style="font-weight:600;font-size:13px;color:var(--ink)">${d.nama}</div>
              ${d.no_kk ? `<div style="font-size:11px;color:var(--ink-mute);margin-top:1px">KK: ${d.no_kk}</div>` : ''}
            </td>
            <td style="padding:12px 16px;font-size:13px;color:var(--ink-soft)">
              ${d.unit_rumah ? `Blok ${d.unit_rumah.blok}-${d.unit_rumah.nomor}` : '<span style="color:var(--ink-mute)">—</span>'}
            </td>
            <td style="padding:12px 16px;font-size:13px;color:var(--ink-soft)">${d.nik || '—'}</td>
            <td style="padding:12px 16px">
              <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:100px;background:${TINGGAL_COLOR[d.status_tinggal]}18;color:${TINGGAL_COLOR[d.status_tinggal]}">
                ${TINGGAL[d.status_tinggal] || d.status_tinggal}
              </span>
            </td>
            <td style="padding:12px 16px;text-align:right">
              <a href="/admin/kependudukan/warga/${d.id}" style="font-size:12px;font-weight:600;color:var(--forest);margin-right:12px">Detail</a>
              <button onclick="editKK(${JSON.stringify(d).replace(/"/g,'&quot;')})" style="background:none;border:none;cursor:pointer;font-size:12px;font-weight:600;color:#2D5AA8;margin-right:12px">Edit</button>
              <button onclick="deleteKK(${d.id},'${d.nama}')" style="background:none;border:none;cursor:pointer;font-size:12px;font-weight:600;color:var(--rust)">Hapus</button>
            </td>
          </tr>
        `).join('')}
      </tbody>
    </table>
  `;

  const totalPages = meta.last_page;
  document.getElementById('pagination').innerHTML = totalPages <= 1 ? '' : `
    <div style="font-size:13px;color:var(--ink-soft)">${meta.total} data · Hal ${meta.current_page}/${totalPages}</div>
    <div style="display:flex;gap:6px">
      <button onclick="loadWarga(${meta.current_page - 1})" ${meta.current_page <= 1 ? 'disabled' : ''} class="btn-secondary" style="padding:6px 14px;font-size:12px">← Prev</button>
      <button onclick="loadWarga(${meta.current_page + 1})" ${meta.current_page >= totalPages ? 'disabled' : ''} class="btn-secondary" style="padding:6px 14px;font-size:12px">Next →</button>
    </div>
  `;
}

async function loadUnits() {
  const r = await fetch('/api/kependudukan/units');
  const j = await r.json();
  if (!j.success) return;
  const sel = document.getElementById('f-unit');
  j.data.forEach(u => {
    const opt = document.createElement('option');
    opt.value = u.id;
    opt.textContent = u.label;
    sel.appendChild(opt);
  });
}

function openModal(data = null) {
  _editId = data?.id || null;
  document.getElementById('modal-title').textContent = _editId ? 'Edit Kepala Keluarga' : 'Tambah Kepala Keluarga';
  document.getElementById('f-nama').value          = data?.nama || '';
  document.getElementById('f-nik').value           = data?.nik || '';
  document.getElementById('f-no-kk').value         = data?.no_kk || '';
  document.getElementById('f-unit').value          = data?.unit_rumah_id || '';
  document.getElementById('f-tempat-lahir').value  = data?.tempat_lahir || '';
  document.getElementById('f-tanggal-lahir').value = data?.tanggal_lahir ? data.tanggal_lahir.slice(0,10) : '';
  document.getElementById('f-jk').value            = data?.jenis_kelamin || '';
  document.getElementById('f-kawin').value         = data?.status_perkawinan || '';
  document.getElementById('f-agama').value         = data?.agama || '';
  document.getElementById('f-pendidikan').value    = data?.pendidikan || '';
  document.getElementById('f-pekerjaan').value     = data?.pekerjaan || '';
  document.getElementById('f-wa').value            = data?.no_wa || '';
  document.getElementById('f-tinggal').value       = data?.status_tinggal || 'tetap';
  document.getElementById('f-keterangan').value    = data?.keterangan || '';
  document.getElementById('modal-err').style.display = 'none';
  document.getElementById('modal-bg').style.display = 'flex';
}

function editKK(data) { openModal(data); }

function closeModal() {
  document.getElementById('modal-bg').style.display = 'none';
  _editId = null;
}

async function saveKK() {
  const nama = document.getElementById('f-nama').value.trim();
  if (!nama) { showErr('Nama wajib diisi.'); return; }

  const btn = document.getElementById('save-btn');
  btn.disabled = true; btn.textContent = 'Menyimpan...';

  const body = {
    nama,
    nik:              document.getElementById('f-nik').value.trim() || null,
    no_kk:            document.getElementById('f-no-kk').value.trim() || null,
    unit_rumah_id:    parseInt(document.getElementById('f-unit').value) || null,
    tempat_lahir:     document.getElementById('f-tempat-lahir').value.trim() || null,
    tanggal_lahir:    document.getElementById('f-tanggal-lahir').value || null,
    jenis_kelamin:    document.getElementById('f-jk').value || null,
    status_perkawinan: document.getElementById('f-kawin').value || null,
    agama:            document.getElementById('f-agama').value || null,
    pendidikan:       document.getElementById('f-pendidikan').value || null,
    pekerjaan:        document.getElementById('f-pekerjaan').value.trim() || null,
    no_wa:            document.getElementById('f-wa').value.trim() || null,
    status_tinggal:   document.getElementById('f-tinggal').value,
    keterangan:       document.getElementById('f-keterangan').value.trim() || null,
  };

  try {
    const url    = _editId ? `/api/kependudukan/kepala/${_editId}` : '/api/kependudukan/kepala';
    const method = _editId ? 'PUT' : 'POST';
    const r = await fetch(url, {
      method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window._csrfToken },
      body: JSON.stringify(body),
    });
    const j = await r.json();
    if (!j.success) { showErr(j.message); return; }
    closeModal();
    loadWarga(_page);
    showToast(_editId ? 'Data diperbarui.' : 'KK berhasil ditambahkan.');
  } catch(e) {
    showErr('Koneksi gagal.');
  } finally {
    btn.disabled = false; btn.textContent = 'Simpan';
  }
}

async function deleteKK(id, nama) {
  if (!confirm(`Hapus KK "${nama}"? Semua anggota dan kendaraan ikut terhapus.`)) return;
  const r = await fetch(`/api/kependudukan/kepala/${id}`, {
    method: 'DELETE', headers: { 'X-CSRF-TOKEN': window._csrfToken },
  });
  const j = await r.json();
  if (j.success) { loadWarga(_page); showToast('KK dihapus.'); }
  else alert(j.message);
}

function showErr(msg) {
  const el = document.getElementById('modal-err');
  el.textContent = msg; el.style.display = 'block';
}

loadWarga(1);
loadUnits();

// auto open tambah modal jika ?tambah=1
if (new URLSearchParams(location.search).get('tambah') === '1') openModal();
</script>
@endsection
