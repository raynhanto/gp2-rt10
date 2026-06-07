@extends('layouts.admin')
@section('title', 'Detail KK')
@section('content')
<div class="container" style="max-width:860px">

  <div style="margin-bottom:2rem">
    <a href="/admin/kependudukan/warga" style="font-size:13px;color:var(--ink-soft)">← Data Warga</a>
    <div id="kk-header" style="margin-top:8px">
      <div style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--forest)">Memuat...</div>
    </div>
  </div>

  {{-- KK Info Card --}}
  <div class="card" style="margin-bottom:1.5rem">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
      <div style="font-size:14px;font-weight:600">Kepala Keluarga</div>
      <button onclick="openEditKK()" class="btn-secondary" style="padding:7px 16px;font-size:12px">
        <i class="fa fa-pen"></i> Edit
      </button>
    </div>
    <div id="kk-info" style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem 2rem">
      <div style="color:var(--ink-mute);font-size:13px">Memuat...</div>
    </div>
  </div>

  {{-- Anggota Keluarga --}}
  <div class="card" style="margin-bottom:1.5rem">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
      <div style="font-size:14px;font-weight:600">Anggota Keluarga</div>
      <button onclick="openAnggotaModal()" class="btn-primary" style="padding:7px 16px;font-size:12px">
        <i class="fa fa-plus"></i> Tambah
      </button>
    </div>
    <div id="anggota-list">
      <div style="text-align:center;padding:2rem;color:var(--ink-mute);font-size:13px">Memuat...</div>
    </div>
  </div>

  {{-- Kendaraan --}}
  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
      <div style="font-size:14px;font-weight:600">Kendaraan</div>
      <button onclick="openKendaraanModal()" class="btn-primary" style="padding:7px 16px;font-size:12px">
        <i class="fa fa-plus"></i> Tambah
      </button>
    </div>
    <div id="kendaraan-list">
      <div style="text-align:center;padding:2rem;color:var(--ink-mute);font-size:13px">Memuat...</div>
    </div>
  </div>

</div>

{{-- Modal Anggota --}}
<div id="anggota-modal-bg" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:500;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;margin:1rem;box-shadow:var(--shadow-lg)">
    <div style="padding:1.5rem 1.5rem 0;display:flex;justify-content:space-between;align-items:center">
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest)" id="anggota-modal-title">Tambah Anggota</div>
      <button onclick="closeAnggotaModal()" style="background:none;border:none;cursor:pointer;font-size:18px;color:var(--ink-soft)"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div style="padding:1.5rem">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div style="grid-column:1/-1">
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Nama *</label>
          <input id="a-nama" type="text" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Hubungan *</label>
          <select id="a-hubungan" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="">— Pilih —</option>
            <option value="istri">Istri</option><option value="suami">Suami</option>
            <option value="anak">Anak</option><option value="menantu">Menantu</option>
            <option value="cucu">Cucu</option><option value="orang_tua">Orang Tua</option>
            <option value="mertua">Mertua</option><option value="saudara">Saudara</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">NIK</label>
          <input id="a-nik" type="text" maxlength="16" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Jenis Kelamin</label>
          <select id="a-jk" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="">— Pilih —</option>
            <option value="L">Laki-laki</option><option value="P">Perempuan</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Tempat Lahir</label>
          <input id="a-tempat" type="text" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Tanggal Lahir</label>
          <input id="a-tgl" type="date" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Pekerjaan</label>
          <input id="a-kerja" type="text" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Pendidikan</label>
          <select id="a-didik" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="">— Pilih —</option>
            <option value="sd">SD</option><option value="smp">SMP</option>
            <option value="sma">SMA/SMK</option><option value="d3">D3</option>
            <option value="s1">S1</option><option value="s2">S2</option>
            <option value="s3">S3</option><option value="lainnya">Lainnya</option>
          </select>
        </div>
      </div>
      <div id="anggota-err" style="display:none;margin-top:1rem;padding:10px 14px;background:#FDECEA;border-radius:8px;font-size:13px;color:var(--rust)"></div>
      <div style="display:flex;gap:10px;margin-top:1.5rem;justify-content:flex-end">
        <button onclick="closeAnggotaModal()" class="btn-secondary" style="padding:10px 22px">Batal</button>
        <button onclick="saveAnggota()" class="btn-primary" id="anggota-save-btn" style="padding:10px 22px">Simpan</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Kendaraan --}}
<div id="kend-modal-bg" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:500;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);width:100%;max-width:480px;margin:1rem;box-shadow:var(--shadow-lg)">
    <div style="padding:1.5rem 1.5rem 0;display:flex;justify-content:space-between;align-items:center">
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest)" id="kend-modal-title">Tambah Kendaraan</div>
      <button onclick="closeKendaraanModal()" style="background:none;border:none;cursor:pointer;font-size:18px;color:var(--ink-soft)"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div style="padding:1.5rem">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Jenis *</label>
          <select id="k-jenis" style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            <option value="">— Pilih —</option>
            <option value="motor">Motor</option><option value="mobil">Mobil</option>
            <option value="sepeda">Sepeda</option><option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Merek *</label>
          <input id="k-merek" type="text" placeholder="Honda, Toyota, dll"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Model</label>
          <input id="k-model" type="text" placeholder="Beat, Avanza, dll"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Warna</label>
          <input id="k-warna" type="text"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Plat Nomor</label>
          <input id="k-plat" type="text" placeholder="B 1234 ABC"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-soft)">Tahun</label>
          <input id="k-tahun" type="number" min="1980" max="2030"
            style="width:100%;margin-top:4px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none">
        </div>
      </div>
      <div id="kend-err" style="display:none;margin-top:1rem;padding:10px 14px;background:#FDECEA;border-radius:8px;font-size:13px;color:var(--rust)"></div>
      <div style="display:flex;gap:10px;margin-top:1.5rem;justify-content:flex-end">
        <button onclick="closeKendaraanModal()" class="btn-secondary" style="padding:10px 22px">Batal</button>
        <button onclick="saveKendaraan()" class="btn-primary" id="kend-save-btn" style="padding:10px 22px">Simpan</button>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script>
const _kkId = {{ (int) request()->route('id') }};
let _kkData = null;
let _editAnggotaId = null;
let _editKendId    = null;

const HUBUNGAN_LABEL = {
  istri:'Istri', suami:'Suami', anak:'Anak', menantu:'Menantu',
  cucu:'Cucu', orang_tua:'Orang Tua', mertua:'Mertua', saudara:'Saudara', lainnya:'Lainnya'
};
const JENIS_ICON = { motor:'<i class="fa-solid fa-motorcycle"></i>', mobil:'<i class="fa-solid fa-car"></i>', sepeda:'<i class="fa-solid fa-bicycle"></i>', lainnya:'<i class="fa-solid fa-van-shuttle"></i>' };

async function loadKK() {
  try {
    const r = await fetch(`/api/kependudukan/kepala/${_kkId}`);
    const j = await r.json();
    if (!j.success) return;
    _kkData = j.data;
    renderKKHeader(j.data);
    renderKKInfo(j.data);
    renderAnggota(j.data.anggota || []);
    renderKendaraan(j.data.kendaraan || []);
  } catch(e) { console.error(e); }
}

function renderKKHeader(d) {
  const unit = d.unit_rumah ? `Blok ${d.unit_rumah.blok}-${d.unit_rumah.nomor}` : '';
  document.getElementById('kk-header').innerHTML = `
    <div style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--forest)">${d.nama}</div>
    ${unit ? `<div style="font-size:13px;color:var(--ink-soft);margin-top:4px">${unit}</div>` : ''}
  `;
  document.title = d.nama + ' — Admin';
}

function infoRow(label, val) {
  if (!val) return '';
  return `<div>
    <div style="font-size:11px;font-weight:600;color:var(--ink-mute);text-transform:uppercase;letter-spacing:0.06em">${label}</div>
    <div style="font-size:13px;color:var(--ink);margin-top:2px">${val}</div>
  </div>`;
}

const AGM = {islam:'Islam',kristen:'Kristen',katolik:'Katolik',hindu:'Hindu',budha:'Buddha',konghucu:'Konghucu'};
const DIDIK = {sd:'SD',smp:'SMP',sma:'SMA/SMK',d3:'D3',s1:'S1',s2:'S2',s3:'S3',lainnya:'Lainnya'};
const KAWIN = {belum_kawin:'Belum Kawin',kawin:'Kawin',cerai_hidup:'Cerai Hidup',cerai_mati:'Cerai Mati'};
const JK = { L:'Laki-laki', P:'Perempuan' };

function renderKKInfo(d) {
  const tgl = d.tanggal_lahir
    ? new Date(d.tanggal_lahir).toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'})
    : null;
  document.getElementById('kk-info').innerHTML = `
    ${infoRow('NIK', d.nik)}
    ${infoRow('No. KK', d.no_kk)}
    ${infoRow('Jenis Kelamin', JK[d.jenis_kelamin])}
    ${infoRow('Tempat / Tgl Lahir', d.tempat_lahir && tgl ? d.tempat_lahir + ', ' + tgl : (d.tempat_lahir || tgl))}
    ${infoRow('Agama', AGM[d.agama])}
    ${infoRow('Pendidikan', DIDIK[d.pendidikan])}
    ${infoRow('Pekerjaan', d.pekerjaan)}
    ${infoRow('No. WA', d.no_wa)}
    ${infoRow('Status Kawin', KAWIN[d.status_perkawinan])}
    ${infoRow('Status Tinggal', d.status_tinggal)}
    ${d.keterangan ? `<div style="grid-column:1/-1">${infoRow('Keterangan', d.keterangan)}</div>` : ''}
  `;
}

function renderAnggota(list) {
  if (!list.length) {
    document.getElementById('anggota-list').innerHTML =
      '<div style="text-align:center;padding:1.5rem;color:var(--ink-mute);font-size:13px">Belum ada anggota keluarga.</div>';
    return;
  }
  document.getElementById('anggota-list').innerHTML = list.map(a => {
    const tgl = a.tanggal_lahir
      ? new Date(a.tanggal_lahir).toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'})
      : null;
    return `
      <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:36px;height:36px;border-radius:50%;background:var(--forest-pale);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--forest);flex-shrink:0">
            ${a.nama.charAt(0).toUpperCase()}
          </div>
          <div>
            <div style="font-weight:600;font-size:13px">${a.nama}</div>
            <div style="font-size:11px;color:var(--ink-soft);margin-top:1px">
              ${HUBUNGAN_LABEL[a.hubungan] || a.hubungan}
              ${a.jenis_kelamin ? ' · ' + JK[a.jenis_kelamin] : ''}
              ${tgl ? ' · ' + tgl : ''}
            </div>
          </div>
        </div>
        <div style="display:flex;gap:12px">
          <button onclick="editAnggota(${JSON.stringify(a).replace(/"/g,'&quot;')})" style="background:none;border:none;cursor:pointer;font-size:12px;font-weight:600;color:#2D5AA8">Edit</button>
          <button onclick="deleteAnggota(${a.id},'${a.nama}')" style="background:none;border:none;cursor:pointer;font-size:12px;font-weight:600;color:var(--rust)">Hapus</button>
        </div>
      </div>
    `;
  }).join('');
}

function renderKendaraan(list) {
  if (!list.length) {
    document.getElementById('kendaraan-list').innerHTML =
      '<div style="text-align:center;padding:1.5rem;color:var(--ink-mute);font-size:13px">Belum ada kendaraan.</div>';
    return;
  }
  document.getElementById('kendaraan-list').innerHTML = list.map(k => `
    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
      <div style="display:flex;align-items:center;gap:12px">
        <div style="font-size:18px;color:var(--ink-soft)">${JENIS_ICON[k.jenis] || '<i class="fa-solid fa-car"></i>'}</div>
        <div>
          <div style="font-weight:600;font-size:13px">${k.merek}${k.model ? ' ' + k.model : ''}${k.tahun ? ' ('+k.tahun+')' : ''}</div>
          <div style="font-size:11px;color:var(--ink-soft);margin-top:1px">
            ${k.plat_nomor || '—'} · ${k.warna || '—'}
          </div>
        </div>
      </div>
      <div style="display:flex;gap:12px">
        <button onclick="editKendaraan(${JSON.stringify(k).replace(/"/g,'&quot;')})" style="background:none;border:none;cursor:pointer;font-size:12px;font-weight:600;color:#2D5AA8">Edit</button>
        <button onclick="deleteKendaraan(${k.id})" style="background:none;border:none;cursor:pointer;font-size:12px;font-weight:600;color:var(--rust)">Hapus</button>
      </div>
    </div>
  `).join('');
}

// ── Anggota Modal ────────────────────────────────────────────

function openAnggotaModal(data = null) {
  _editAnggotaId = data?.id || null;
  document.getElementById('anggota-modal-title').textContent = _editAnggotaId ? 'Edit Anggota' : 'Tambah Anggota';
  document.getElementById('a-nama').value     = data?.nama || '';
  document.getElementById('a-hubungan').value = data?.hubungan || '';
  document.getElementById('a-nik').value      = data?.nik || '';
  document.getElementById('a-jk').value       = data?.jenis_kelamin || '';
  document.getElementById('a-tempat').value   = data?.tempat_lahir || '';
  document.getElementById('a-tgl').value      = data?.tanggal_lahir ? data.tanggal_lahir.slice(0,10) : '';
  document.getElementById('a-kerja').value    = data?.pekerjaan || '';
  document.getElementById('a-didik').value    = data?.pendidikan || '';
  document.getElementById('anggota-err').style.display = 'none';
  document.getElementById('anggota-modal-bg').style.display = 'flex';
}

function editAnggota(data) { openAnggotaModal(data); }
function closeAnggotaModal() { document.getElementById('anggota-modal-bg').style.display = 'none'; }

async function saveAnggota() {
  const nama     = document.getElementById('a-nama').value.trim();
  const hubungan = document.getElementById('a-hubungan').value;
  if (!nama)     { document.getElementById('anggota-err').textContent = 'Nama wajib diisi.'; document.getElementById('anggota-err').style.display = 'block'; return; }
  if (!hubungan) { document.getElementById('anggota-err').textContent = 'Hubungan wajib dipilih.'; document.getElementById('anggota-err').style.display = 'block'; return; }

  const btn = document.getElementById('anggota-save-btn');
  btn.disabled = true; btn.textContent = 'Menyimpan...';

  const body = {
    nama, hubungan,
    nik:           document.getElementById('a-nik').value.trim() || null,
    jenis_kelamin: document.getElementById('a-jk').value || null,
    tempat_lahir:  document.getElementById('a-tempat').value.trim() || null,
    tanggal_lahir: document.getElementById('a-tgl').value || null,
    pekerjaan:     document.getElementById('a-kerja').value.trim() || null,
    pendidikan:    document.getElementById('a-didik').value || null,
  };

  try {
    const url    = _editAnggotaId ? `/api/kependudukan/anggota/${_editAnggotaId}` : `/api/kependudukan/kepala/${_kkId}/anggota`;
    const method = _editAnggotaId ? 'PUT' : 'POST';
    const r = await fetch(url, {
      method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window._csrfToken },
      body: JSON.stringify(body),
    });
    const j = await r.json();
    if (!j.success) { document.getElementById('anggota-err').textContent = j.message; document.getElementById('anggota-err').style.display = 'block'; return; }
    closeAnggotaModal();
    loadKK();
    showToast('Anggota disimpan.');
  } catch(e) { document.getElementById('anggota-err').textContent = 'Koneksi gagal.'; document.getElementById('anggota-err').style.display = 'block'; }
  finally { btn.disabled = false; btn.textContent = 'Simpan'; }
}

async function deleteAnggota(id, nama) {
  if (!confirm(`Hapus anggota "${nama}"?`)) return;
  const r = await fetch(`/api/kependudukan/anggota/${id}`, {
    method: 'DELETE', headers: { 'X-CSRF-TOKEN': window._csrfToken },
  });
  const j = await r.json();
  if (j.success) { loadKK(); showToast('Anggota dihapus.'); }
  else alert(j.message);
}

// ── Kendaraan Modal ──────────────────────────────────────────

function openKendaraanModal(data = null) {
  _editKendId = data?.id || null;
  document.getElementById('kend-modal-title').textContent = _editKendId ? 'Edit Kendaraan' : 'Tambah Kendaraan';
  document.getElementById('k-jenis').value = data?.jenis || '';
  document.getElementById('k-merek').value = data?.merek || '';
  document.getElementById('k-model').value = data?.model || '';
  document.getElementById('k-warna').value = data?.warna || '';
  document.getElementById('k-plat').value  = data?.plat_nomor || '';
  document.getElementById('k-tahun').value = data?.tahun || '';
  document.getElementById('kend-err').style.display = 'none';
  document.getElementById('kend-modal-bg').style.display = 'flex';
}

function editKendaraan(data) { openKendaraanModal(data); }
function closeKendaraanModal() { document.getElementById('kend-modal-bg').style.display = 'none'; }

async function saveKendaraan() {
  const jenis = document.getElementById('k-jenis').value;
  const merek = document.getElementById('k-merek').value.trim();
  if (!jenis || !merek) {
    document.getElementById('kend-err').textContent = 'Jenis dan merek wajib diisi.';
    document.getElementById('kend-err').style.display = 'block'; return;
  }

  const btn = document.getElementById('kend-save-btn');
  btn.disabled = true; btn.textContent = 'Menyimpan...';

  const body = {
    jenis, merek,
    model:      document.getElementById('k-model').value.trim() || null,
    warna:      document.getElementById('k-warna').value.trim() || null,
    plat_nomor: document.getElementById('k-plat').value.trim() || null,
    tahun:      parseInt(document.getElementById('k-tahun').value) || null,
  };

  try {
    const url    = _editKendId ? `/api/kependudukan/kendaraan/${_editKendId}` : `/api/kependudukan/kepala/${_kkId}/kendaraan`;
    const method = _editKendId ? 'PUT' : 'POST';
    const r = await fetch(url, {
      method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window._csrfToken },
      body: JSON.stringify(body),
    });
    const j = await r.json();
    if (!j.success) { document.getElementById('kend-err').textContent = j.message; document.getElementById('kend-err').style.display = 'block'; return; }
    closeKendaraanModal();
    loadKK();
    showToast('Kendaraan disimpan.');
  } catch(e) { document.getElementById('kend-err').textContent = 'Koneksi gagal.'; document.getElementById('kend-err').style.display = 'block'; }
  finally { btn.disabled = false; btn.textContent = 'Simpan'; }
}

async function deleteKendaraan(id) {
  if (!confirm('Hapus kendaraan ini?')) return;
  const r = await fetch(`/api/kependudukan/kendaraan/${id}`, {
    method: 'DELETE', headers: { 'X-CSRF-TOKEN': window._csrfToken },
  });
  const j = await r.json();
  if (j.success) { loadKK(); showToast('Kendaraan dihapus.'); }
}

function openEditKK() {
  if (_kkData) location.href = `/admin/kependudukan/warga?edit=${_kkId}`;
}

loadKK();
</script>
@endsection
