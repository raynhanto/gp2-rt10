@extends('layouts.admin')
@section('title', 'Berita & Informasi')
@section('content')
<div class="container" style="max-width:1100px">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <div class="section-label">Kelembagaan</div>
      <div class="section-title">Berita &amp; Informasi</div>
    </div>
    <button onclick="openForm()" class="btn-primary"><i class="fa fa-plus" style="font-size:11px"></i> Tulis Berita</button>
  </div>

  {{-- Category filter --}}
  <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:1.25rem">
    @foreach([''=>'Semua','informasi'=>'Informasi','kegiatan'=>'Kegiatan','pengumuman'=>'Pengumuman','lainnya'=>'Lainnya'] as $val=>$lbl)
    <button onclick="setKat('{{ $val }}')" data-kat="{{ $val }}" class="cat-tab{{ $val==='' ? ' active' : '' }}">{{ $lbl }}</button>
    @endforeach
  </div>

  <div style="font-size:12px;color:var(--ink-mute);margin-bottom:1rem" id="berita-count"></div>
  <div id="berita-list" style="display:flex;flex-direction:column;gap:.75rem"></div>
  <div id="berita-empty" style="display:none;text-align:center;padding:3rem;background:#fff;border-radius:var(--radius);border:1px solid var(--border);color:var(--ink-soft)">
    <i class="fa fa-newspaper" style="font-size:2rem;display:block;margin-bottom:10px;color:var(--ink-mute)"></i>
    Belum ada berita.
  </div>
</div>

{{-- Modal --}}
<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:620px;max-height:92vh;overflow-y:auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest)" id="form-title">Tulis Berita</div>
      <button onclick="closeForm()" style="border:none;background:none;cursor:pointer;font-size:20px;color:var(--ink-mute)"><i class="fa fa-xmark"></i></button>
    </div>
    <input type="hidden" id="f-id">

    {{-- Cover image --}}
    <div style="margin-bottom:1.25rem">
      <label class="flabel">Foto Cover (opsional)</label>
      <div id="cover-zone" style="border:2px dashed var(--border);border-radius:var(--radius-sm);padding:1.5rem;text-align:center;cursor:pointer;transition:border-color .15s"
           onclick="document.getElementById('f-cover').click()">
        <input type="file" id="f-cover" accept="image/jpeg,image/png,image/webp" style="display:none" onchange="previewCover(this)">
        <div id="cover-preview" style="display:none;position:relative">
          <img id="cover-img" src="" alt="" style="max-width:100%;max-height:140px;border-radius:6px;object-fit:contain">
          <button onclick="clearCover(event)" style="position:absolute;top:-8px;right:-8px;width:22px;height:22px;border-radius:50%;background:var(--rust);color:#fff;border:none;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center"><i class="fa fa-xmark"></i></button>
        </div>
        <div id="cover-placeholder">
          <i class="fa fa-image" style="font-size:1.5rem;color:var(--ink-mute);margin-bottom:6px;display:block"></i>
          <div style="font-size:12px;color:var(--ink-soft)">Klik untuk upload foto cover</div>
        </div>
      </div>
      {{-- current cover in edit mode --}}
      <div id="cover-current" style="display:none;margin-top:8px;font-size:12px;color:var(--ink-soft)">
        Cover saat ini: <img id="cover-current-img" src="" alt="" style="height:32px;border-radius:4px;vertical-align:middle;margin-left:6px">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div style="grid-column:span 2">
        <label class="flabel">Judul *</label>
        <input type="text" id="f-judul" placeholder="Judul berita atau informasi" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
      </div>
      <div>
        <label class="flabel">Kategori</label>
        <select id="f-kategori" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="informasi">Informasi</option>
          <option value="kegiatan">Kegiatan</option>
          <option value="pengumuman">Pengumuman</option>
          <option value="lainnya">Lainnya</option>
        </select>
      </div>
      <div style="display:flex;flex-direction:column;justify-content:flex-end">
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;margin-bottom:8px">
          <input type="checkbox" id="f-pinned" style="accent-color:var(--forest);width:15px;height:15px">
          <span><i class="fa fa-thumbtack" style="color:var(--gold);font-size:11px"></i> Sematkan di atas</span>
        </label>
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
          <input type="checkbox" id="f-published" checked style="accent-color:var(--forest);width:15px;height:15px">
          <span>Publikasikan</span>
        </label>
      </div>
      <div style="grid-column:span 2">
        <label class="flabel">Isi *</label>
        <textarea id="f-isi" rows="8" placeholder="Tulis isi berita di sini..." style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;resize:vertical;font-family:'DM Sans',sans-serif;box-sizing:border-box;line-height:1.65"></textarea>
      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button onclick="save()" class="btn-primary" style="flex:1;justify-content:center" id="save-btn">Simpan</button>
      <button onclick="closeForm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

<style>
.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}
.cat-tab{padding:6px 14px;border:1.5px solid var(--border);border-radius:99px;font-size:12px;font-weight:500;background:#fff;cursor:pointer;color:var(--ink-mid);transition:all .15s}
.cat-tab.active{background:var(--forest);border-color:var(--forest);color:#fff}
.berita-row{background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem 1.25rem;display:flex;gap:1rem;align-items:flex-start}
.chip-informasi {background:#e8f5ec;color:#1A3D2B}
.chip-kegiatan  {background:#e4ecfd;color:#1A3060}
.chip-pengumuman{background:#fdf8e4;color:#5A4A00}
.chip-lainnya   {background:#f0ece8;color:#3A3030}
.bchip{font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;display:inline-block}
</style>
@endsection
@section('scripts')
<script>
const _csrfToken = '{{ csrf_token() }}';
const KAT_LBL = { informasi:'Informasi', kegiatan:'Kegiatan', pengumuman:'Pengumuman', lainnya:'Lainnya' };
const BULAN_S = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
let _all = [], _curKat = '';

function fmtTgl(d) {
  const dt = new Date(d.slice(0,10)+'T00:00:00');
  return `${dt.getDate()} ${BULAN_S[dt.getMonth()]} ${dt.getFullYear()}`;
}
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

async function load() {
  const r = await fetch('/api/berita');
  const j = await r.json();
  _all = j.success ? j.data : [];
  renderList();
}

function setKat(k) {
  _curKat = k;
  document.querySelectorAll('.cat-tab').forEach(b => b.classList.toggle('active', b.dataset.kat === k));
  renderList();
}

function renderList() {
  const filtered = _curKat ? _all.filter(b => b.kategori === _curKat) : _all;
  const el  = document.getElementById('berita-list');
  const emp = document.getElementById('berita-empty');
  document.getElementById('berita-count').textContent = `${filtered.length} berita`;
  if (!filtered.length) { el.innerHTML = ''; emp.style.display = ''; return; }
  emp.style.display = 'none';

  el.innerHTML = filtered.map(b => `
    <div class="berita-row">
      ${b.cover_url ? `<img src="${esc(b.cover_url)}" style="width:80px;height:60px;object-fit:cover;border-radius:6px;flex-shrink:0;border:1px solid var(--border)" alt="">` : ''}
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;flex-wrap:wrap">
          <span class="bchip chip-${b.kategori}">${KAT_LBL[b.kategori]||b.kategori}</span>
          ${b.is_pinned    ? `<span style="font-size:10px;color:var(--gold)"><i class="fa fa-thumbtack"></i> Disematkan</span>` : ''}
          ${!b.is_published? `<span style="font-size:10px;color:var(--ink-mute)"><i class="fa fa-eye-slash"></i> Draft</span>` : ''}
        </div>
        <div style="font-weight:600;font-size:14px;color:var(--ink);margin-bottom:2px">${esc(b.judul)}</div>
        <div style="font-size:11px;color:var(--ink-mute)">${fmtTgl(b.created_at)}</div>
      </div>
      <div style="display:flex;flex-direction:column;gap:5px;flex-shrink:0">
        <button onclick='editBerita(${JSON.stringify(b)})' class="btn-secondary" style="padding:6px 12px;font-size:11px"><i class="fa fa-pen"></i></button>
        <button onclick="delBerita(${b.id})" style="padding:6px 12px;font-size:11px;border:1px solid var(--border);border-radius:var(--radius-sm);background:#fff;cursor:pointer;color:var(--rust)"><i class="fa fa-trash"></i></button>
      </div>
    </div>`).join('');
}

// ── Form ──────────────────────────────────────────────────────
function openForm() {
  document.getElementById('f-id').value         = '';
  document.getElementById('f-judul').value      = '';
  document.getElementById('f-kategori').value   = 'informasi';
  document.getElementById('f-isi').value        = '';
  document.getElementById('f-pinned').checked   = false;
  document.getElementById('f-published').checked= true;
  document.getElementById('form-title').textContent = 'Tulis Berita';
  document.getElementById('cover-current').style.display = 'none';
  clearCover({stopPropagation:()=>{}});
  document.getElementById('form-overlay').style.display = 'flex';
}

function closeForm() { document.getElementById('form-overlay').style.display = 'none'; }

function editBerita(b) {
  document.getElementById('f-id').value         = b.id;
  document.getElementById('f-judul').value      = b.judul;
  document.getElementById('f-kategori').value   = b.kategori || 'informasi';
  document.getElementById('f-isi').value        = b.isi;
  document.getElementById('f-pinned').checked   = !!b.is_pinned;
  document.getElementById('f-published').checked= !!b.is_published;
  document.getElementById('form-title').textContent = 'Edit Berita';
  clearCover({stopPropagation:()=>{}});
  if (b.cover_url) {
    document.getElementById('cover-current').style.display = '';
    document.getElementById('cover-current-img').src = b.cover_url;
  } else {
    document.getElementById('cover-current').style.display = 'none';
  }
  document.getElementById('form-overlay').style.display = 'flex';
}

function previewCover(input) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('cover-img').src = e.target.result;
    document.getElementById('cover-preview').style.display = '';
    document.getElementById('cover-placeholder').style.display = 'none';
  };
  reader.readAsDataURL(input.files[0]);
}

function clearCover(e) {
  e.stopPropagation();
  document.getElementById('f-cover').value = '';
  document.getElementById('cover-preview').style.display = 'none';
  document.getElementById('cover-placeholder').style.display = '';
}

async function save() {
  const id    = document.getElementById('f-id').value;
  const judul = document.getElementById('f-judul').value.trim();
  const isi   = document.getElementById('f-isi').value.trim();
  if (!judul) { alert('Judul wajib diisi.'); return; }
  if (!isi)   { alert('Isi berita wajib diisi.'); return; }

  const fd = new FormData();
  fd.append('judul',        judul);
  fd.append('isi',          isi);
  fd.append('kategori',     document.getElementById('f-kategori').value);
  fd.append('is_pinned',    document.getElementById('f-pinned').checked    ? '1' : '0');
  fd.append('is_published', document.getElementById('f-published').checked ? '1' : '0');
  const coverFile = document.getElementById('f-cover').files[0];
  if (coverFile) fd.append('cover', coverFile);

  const url    = id ? `/api/berita/${id}` : '/api/berita';
  const r      = await fetch(url, { method:'POST', headers:{'X-CSRF-TOKEN':_csrfToken}, body:fd });
  const j      = await r.json();
  if (j.success) { closeForm(); showToast(j.message); load(); }
  else alert(j.message);
}

async function delBerita(id) {
  if (!confirm('Hapus berita ini?')) return;
  const r = await fetch(`/api/berita/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  if (j.success) { showToast(j.message); load(); }
  else alert(j.message);
}

load();
</script>
@endsection
