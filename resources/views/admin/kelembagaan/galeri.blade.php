@extends('layouts.admin')
@section('title', 'Galeri Foto')
@section('content')
<div class="container" style="max-width:1140px">
  <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;align-items:flex-end;margin-bottom:1.25rem">
    <div>
      <div class="section-label">Kelembagaan</div>
      <div class="section-title">Galeri Foto</div>
    </div>
    <button onclick="openCreate()" class="btn-primary"><i class="fa fa-plus" style="font-size:11px"></i> Buat Album</button>
  </div>

  {{-- Category tabs --}}
  <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:1.25rem">
    @foreach([''=>'Semua','kegiatan'=>'Kegiatan','sosial'=>'Sosial','fasilitas'=>'Fasilitas','dokumentasi'=>'Dokumentasi','lainnya'=>'Lainnya'] as $val=>$lbl)
    <button onclick="setKat('{{ $val }}')" data-kat="{{ $val }}" class="cat-tab{{ $val==='' ? ' active' : '' }}">{{ $lbl }}</button>
    @endforeach
  </div>

  <div style="font-size:12px;color:var(--ink-mute);margin-bottom:1rem" id="album-count"></div>

  <div id="alb-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px"></div>

  <div id="alb-empty" style="display:none;text-align:center;padding:3rem;color:var(--ink-soft);background:#fff;border-radius:var(--radius);border:1px solid var(--border)">
    <i class="fa-regular fa-images" style="font-size:2rem;margin-bottom:10px;display:block;color:var(--ink-mute)"></i>
    Belum ada album.
  </div>
</div>

{{-- ============================================================
     MODAL: Buat Album Baru
     ============================================================ --}}
<div id="create-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:540px;max-height:90vh;overflow-y:auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest)">Buat Album Baru</div>
      <button onclick="closeCreate()" style="border:none;background:none;cursor:pointer;font-size:20px;color:var(--ink-mute);padding:0;line-height:1"><i class="fa fa-xmark"></i></button>
    </div>

    {{-- Multi-file upload zone --}}
    <div id="cz-zone" style="border:2px dashed var(--border);border-radius:var(--radius-sm);padding:2rem;text-align:center;cursor:pointer;margin-bottom:1.25rem;transition:border-color .15s,background .15s"
         onclick="document.getElementById('cz-files').click()"
         ondragover="czDragOver(event)" ondragleave="czDragLeave()" ondrop="czDrop(event)">
      <input type="file" id="cz-files" accept="image/jpeg,image/png,image/webp" multiple style="display:none" onchange="czPreview(this.files)">
      <div id="cz-placeholder">
        <i class="fa fa-cloud-arrow-up" style="font-size:2rem;color:var(--ink-mute);margin-bottom:8px;display:block"></i>
        <div style="font-size:13px;color:var(--ink-soft)">Klik atau drag &amp; drop foto di sini</div>
        <div style="font-size:11px;color:var(--ink-mute);margin-top:4px">JPG, PNG, WebP &mdash; otomatis dikompres &mdash; bisa pilih banyak</div>
      </div>
      <div id="cz-preview" style="display:none">
        <div id="cz-thumbs" style="display:flex;gap:6px;flex-wrap:wrap;justify-content:center;margin-bottom:8px"></div>
        <div style="font-size:12px;color:var(--forest);font-weight:600" id="cz-count"></div>
        <button onclick="czClear(event)" style="margin-top:8px;font-size:11px;color:var(--rust);background:none;border:none;cursor:pointer;text-decoration:underline">Hapus semua</button>
      </div>
    </div>

    {{-- Upload progress (shown during compress + upload) --}}
    <div id="c-progress-area" style="display:none;margin-bottom:1.25rem;padding:12px 14px;background:#f0f7f2;border-radius:var(--radius-sm);border:1px solid #c8e6d0">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px">
        <span style="font-size:12px;font-weight:600;color:var(--forest)" id="c-progress-label">Memproses...</span>
        <span style="font-size:11px;color:var(--ink-mute)" id="c-progress-count"></span>
      </div>
      <div style="height:5px;background:#d4ead9;border-radius:99px;overflow:hidden">
        <div id="c-progress-bar" style="height:100%;background:var(--forest);border-radius:99px;width:0%;transition:width .25s ease"></div>
      </div>
    </div>

    {{-- Form fields --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div style="grid-column:span 2">
        <label class="flabel">Judul Album *</label>
        <input type="text" id="c-judul" placeholder="Contoh: Kerja Bakti RW 05" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
      </div>
      <div>
        <label class="flabel">Tanggal *</label>
        <input type="date" id="c-tanggal" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Kategori</label>
        <select id="c-kategori" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="kegiatan">Kegiatan</option>
          <option value="sosial">Sosial</option>
          <option value="fasilitas">Fasilitas</option>
          <option value="dokumentasi">Dokumentasi</option>
          <option value="lainnya">Lainnya</option>
        </select>
      </div>
      <div style="grid-column:span 2">
        <label class="flabel">Deskripsi</label>
        <textarea id="c-deskripsi" rows="2" placeholder="Keterangan singkat..." style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;resize:vertical;font-family:'DM Sans',sans-serif;box-sizing:border-box"></textarea>
      </div>
    </div>
    <div style="display:flex;gap:1.5rem;margin-bottom:1.5rem">
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
        <input type="checkbox" id="c-featured" style="accent-color:var(--forest);width:15px;height:15px">
        <span><i class="fa fa-star" style="color:var(--gold);font-size:11px"></i> Album Unggulan</span>
      </label>
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
        <input type="checkbox" id="c-public" checked style="accent-color:var(--forest);width:15px;height:15px">
        <span>Tampil di halaman publik</span>
      </label>
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="createAlbum()" class="btn-primary" style="flex:1;justify-content:center" id="c-save-btn">
        <i class="fa fa-upload" style="font-size:11px"></i> Upload Album
      </button>
      <button onclick="closeCreate()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

{{-- ============================================================
     MODAL: Edit Info Album
     ============================================================ --}}
<div id="edit-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:min(480px,calc(100vw - 2rem));max-height:90vh;overflow-y:auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest)">Edit Info Album</div>
      <button onclick="closeEdit()" style="border:none;background:none;cursor:pointer;font-size:20px;color:var(--ink-mute);padding:0;line-height:1"><i class="fa fa-xmark"></i></button>
    </div>
    <input type="hidden" id="e-id">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div style="grid-column:span 2">
        <label class="flabel">Judul Album *</label>
        <input type="text" id="e-judul" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
      </div>
      <div>
        <label class="flabel">Tanggal *</label>
        <input type="date" id="e-tanggal" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Kategori</label>
        <select id="e-kategori" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="kegiatan">Kegiatan</option>
          <option value="sosial">Sosial</option>
          <option value="fasilitas">Fasilitas</option>
          <option value="dokumentasi">Dokumentasi</option>
          <option value="lainnya">Lainnya</option>
        </select>
      </div>
      <div style="grid-column:span 2">
        <label class="flabel">Deskripsi</label>
        <textarea id="e-deskripsi" rows="2" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;resize:vertical;font-family:'DM Sans',sans-serif;box-sizing:border-box"></textarea>
      </div>
    </div>
    <div style="display:flex;gap:1.5rem;margin-bottom:1.5rem">
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
        <input type="checkbox" id="e-featured" style="accent-color:var(--forest);width:15px;height:15px">
        <span><i class="fa fa-star" style="color:var(--gold);font-size:11px"></i> Unggulan</span>
      </label>
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
        <input type="checkbox" id="e-public" style="accent-color:var(--forest);width:15px;height:15px">
        <span>Tampil publik</span>
      </label>
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="saveEdit()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeEdit()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

{{-- ============================================================
     PANEL: Kelola Foto (full-width slide-in)
     ============================================================ --}}
<div id="manage-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:flex-end;justify-content:center;padding:0">
  <div id="manage-panel" style="background:#fff;width:100%;max-width:min(800px,100vw);max-height:88vh;border-radius:var(--radius) var(--radius) 0 0;overflow:hidden;display:flex;flex-direction:column;margin:0 auto">
    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);flex-shrink:0">
      <div>
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--ink-soft);margin-bottom:2px">Kelola Foto</div>
        <div style="font-family:'DM Serif Display',serif;font-size:1.1rem;color:var(--forest)" id="manage-title">—</div>
      </div>
      <button onclick="closeManage()" style="border:none;background:none;cursor:pointer;font-size:20px;color:var(--ink-mute);padding:0;line-height:1"><i class="fa fa-xmark"></i></button>
    </div>
    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;gap:10px;padding:.875rem 1.5rem;border-bottom:1px solid var(--border);flex-shrink:0">
      <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;padding:8px 16px;background:var(--forest);color:#fff;border-radius:var(--radius-sm);font-size:12px;font-weight:600">
        <i class="fa fa-plus"></i> Tambah Foto
        <input type="file" id="m-files" accept="image/jpeg,image/png,image/webp" multiple style="display:none" onchange="addFotos(this.files)">
      </label>
      <span style="font-size:12px;color:var(--ink-mute)" id="manage-count"></span>
    </div>
    {{-- Photo grid --}}
    <div style="flex:1;overflow-y:auto;padding:1.25rem 1.5rem">
      <div id="manage-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px"></div>
      <div id="manage-empty" style="display:none;text-align:center;padding:3rem;color:var(--ink-soft)">
        <i class="fa-regular fa-image" style="font-size:2rem;display:block;margin-bottom:10px;color:var(--ink-mute)"></i>
        Album ini belum memiliki foto.
      </div>
      <div id="manage-uploading" style="display:none;padding:.75rem 0">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px">
          <span style="font-size:12px;font-weight:600;color:var(--forest)" id="m-progress-label">Memproses...</span>
          <span style="font-size:11px;color:var(--ink-mute)" id="m-progress-count"></span>
        </div>
        <div style="height:5px;background:var(--border);border-radius:99px;overflow:hidden">
          <div id="m-progress-bar" style="height:100%;background:var(--forest);border-radius:99px;width:0%;transition:width .25s ease"></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Quick lightbox for admin preview --}}
<div id="qlb" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:400;align-items:center;justify-content:center" onclick="closeQlb(event)">
  <button onclick="closeQlbBtn()" style="position:fixed;top:1.25rem;right:1.5rem;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.12);border:none;cursor:pointer;color:#fff;font-size:18px;display:flex;align-items:center;justify-content:center;z-index:402">
    <i class="fa fa-xmark"></i>
  </button>
  <img id="qlb-img" src="" alt="" style="max-width:88vw;max-height:84vh;object-fit:contain;border-radius:8px">
</div>

<style>
.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}
.cat-tab{padding:6px 14px;border:1.5px solid var(--border);border-radius:99px;font-size:12px;font-weight:500;background:#fff;cursor:pointer;color:var(--ink-mid);transition:all .15s}
.cat-tab.active{background:var(--forest);border-color:var(--forest);color:#fff}

/* Album cards */
.alb-card-a{background:#fff;border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);transition:box-shadow .15s}
.alb-card-a:hover{box-shadow:0 4px 16px rgba(0,0,0,.1)}
.alb-cover-a{position:relative;aspect-ratio:4/3;overflow:hidden;background:#111;cursor:zoom-in}
.alb-cover-a img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s}
.alb-card-a:hover .alb-cover-a img{transform:scale(1.04)}
.cnt-badge{position:absolute;bottom:6px;right:6px;background:rgba(0,0,0,.6);color:#fff;font-size:10px;font-weight:600;padding:3px 9px;border-radius:99px;display:flex;align-items:center;gap:4px;backdrop-filter:blur(4px)}
.feat-badge{position:absolute;top:6px;left:6px;background:var(--gold);color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:99px}
.prv-badge{position:absolute;top:6px;right:6px;background:rgba(0,0,0,.5);color:rgba(255,255,255,.8);font-size:9px;padding:2px 7px;border-radius:99px}
.alb-body-a{padding:12px 14px}
.chip-k{font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;display:inline-block;margin-bottom:5px;text-transform:capitalize}
.chip-kegiatan   {background:#e8f5ec;color:#1A3D2B}
.chip-sosial     {background:#fde8e4;color:#7A1A0A}
.chip-fasilitas  {background:#e4ecfd;color:#1A3060}
.chip-dokumentasi{background:#fdf8e4;color:#5A4A00}
.chip-lainnya    {background:#f0ece8;color:#3A3030}
.alb-title-a{font-weight:600;font-size:13px;color:var(--ink);margin-bottom:3px;line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.alb-date-a{font-size:11px;color:var(--ink-mute);margin-bottom:10px}
.alb-actions{display:flex;gap:5px}
.alb-actions button{flex:1;font-size:11px;padding:5px 0;border:1px solid var(--border);border-radius:5px;background:#fff;cursor:pointer;color:var(--ink-mid);transition:all .15s;display:flex;align-items:center;justify-content:center;gap:4px}
.alb-actions button:hover{background:var(--forest);border-color:var(--forest);color:#fff}
.alb-actions button.del:hover{background:var(--rust);border-color:var(--rust);color:#fff}

/* Manage grid photo card */
.m-photo{position:relative;aspect-ratio:4/3;border-radius:7px;overflow:hidden;background:#111;cursor:zoom-in}
.m-photo img{width:100%;height:100%;object-fit:cover;display:block;transition:opacity .18s}
.m-photo:hover img{opacity:.75}
.m-photo-del{position:absolute;top:4px;right:4px;width:26px;height:26px;border-radius:50%;background:rgba(181,64,26,.85);color:#fff;border:none;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .15s}
.m-photo:hover .m-photo-del{opacity:1}
.m-photo-del:hover{background:var(--rust)}
#cz-zone.drag-over{border-color:var(--forest);background:var(--forest-pale,#f0f7f2)}
</style>
@endsection
@section('scripts')
<script>
const _csrfToken = '{{ csrf_token() }}';
const KAT_LBL = { kegiatan:'Kegiatan', sosial:'Sosial', fasilitas:'Fasilitas', dokumentasi:'Dokumentasi', lainnya:'Lainnya' };

// Re-encode to JPEG at given quality — keeps original dimensions
function compressImage(file, quality = 0.82) {
  return new Promise(resolve => {
    const img = new Image();
    const url = URL.createObjectURL(file);
    img.onload = () => {
      const canvas = document.createElement('canvas');
      canvas.width  = img.naturalWidth;
      canvas.height = img.naturalHeight;
      canvas.getContext('2d').drawImage(img, 0, 0);
      URL.revokeObjectURL(url);
      canvas.toBlob(blob => {
        resolve(new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' }));
      }, 'image/jpeg', quality);
    };
    img.onerror = () => { URL.revokeObjectURL(url); resolve(file); }; // fallback: send original
    img.src = url;
  });
}

async function compressAll(files, onProgress) {
  const out = [];
  for (let i = 0; i < files.length; i++) {
    if (onProgress) onProgress(i + 1, files.length);
    out.push(await compressImage(files[i]));
  }
  return out;
}
const BULAN_S = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];

let _albums = [], _curKat = '', _manageId = null, _manageAlbum = null;
let _czFiles = [];

function fmtTgl(d) {
  const dt = new Date(d.slice(0,10) + 'T00:00:00');
  return `${dt.getDate()} ${BULAN_S[dt.getMonth()]} ${dt.getFullYear()}`;
}
function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Load ──────────────────────────────────────────────────────
async function load() {
  const params = new URLSearchParams();
  if (_curKat) params.set('kategori', _curKat);
  const r = await fetch('/api/galeri?' + params);
  const j = await r.json();
  _albums = j.success ? j.data : [];
  renderGrid();
}

function setKat(k) {
  _curKat = k;
  document.querySelectorAll('.cat-tab').forEach(b => b.classList.toggle('active', b.dataset.kat === k));
  load();
}

// ── Render album grid ─────────────────────────────────────────
function renderGrid() {
  const el    = document.getElementById('alb-grid');
  const empty = document.getElementById('alb-empty');
  const count = document.getElementById('album-count');
  count.textContent = `${_albums.length} album`;

  if (!_albums.length) { el.innerHTML = ''; empty.style.display = ''; return; }
  empty.style.display = 'none';

  el.innerHTML = _albums.map(a => {
    const cover = a.cover_url || '';
    const cnt   = a.fotos_count ?? 0;
    return `
    <div class="alb-card-a">
      <div class="alb-cover-a" onclick="previewCover('${esc(cover)}','${esc(a.judul)}')">
        ${cover
          ? `<img src="${esc(cover)}" alt="${esc(a.judul)}" loading="lazy" onerror="this.parentElement.style.background='#2a2a2a'">`
          : `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.2)"><i class="fa-regular fa-image" style="font-size:2rem"></i></div>`}
        ${a.is_featured ? `<span class="feat-badge"><i class="fa fa-star" style="font-size:9px"></i> Unggulan</span>` : ''}
        ${!a.is_public  ? `<span class="prv-badge"><i class="fa fa-lock" style="font-size:9px"></i></span>` : ''}
        <span class="cnt-badge"><i class="fa fa-images" style="font-size:10px"></i> ${cnt}</span>
      </div>
      <div class="alb-body-a">
        <span class="chip-k chip-${a.kategori}">${KAT_LBL[a.kategori]||a.kategori}</span>
        <div class="alb-title-a">${esc(a.judul)}</div>
        <div class="alb-date-a">${fmtTgl(a.tanggal)}</div>
        <div class="alb-actions">
          <button onclick='openEdit(${JSON.stringify(a)})'><i class="fa fa-pen" style="font-size:10px"></i> Edit</button>
          <button onclick="openManage(${a.id},'${esc(a.judul)}')"><i class="fa fa-images" style="font-size:10px"></i> Foto</button>
          <button class="del" onclick="delAlbum(${a.id})"><i class="fa fa-trash" style="font-size:10px"></i></button>
        </div>
      </div>
    </div>`;
  }).join('');
}

// ── CREATE ALBUM ──────────────────────────────────────────────
function openCreate() {
  _czFiles = [];
  document.getElementById('c-judul').value      = '';
  document.getElementById('c-tanggal').value    = '';
  document.getElementById('c-kategori').value   = 'kegiatan';
  document.getElementById('c-deskripsi').value  = '';
  document.getElementById('c-featured').checked = false;
  document.getElementById('c-public').checked   = true;
  czReset();
  document.getElementById('create-overlay').style.display = 'flex';
}

function closeCreate() { document.getElementById('create-overlay').style.display = 'none'; }

// Drag-drop helpers for create zone
function czDragOver(e) { e.preventDefault(); document.getElementById('cz-zone').classList.add('drag-over'); }
function czDragLeave()  { document.getElementById('cz-zone').classList.remove('drag-over'); }
function czDrop(e) {
  e.preventDefault();
  czDragLeave();
  czPreview(e.dataTransfer.files);
}

function czPreview(fileList) {
  _czFiles = Array.from(fileList);
  if (!_czFiles.length) return;
  document.getElementById('cz-placeholder').style.display = 'none';
  document.getElementById('cz-preview').style.display     = '';
  document.getElementById('cz-count').textContent = `${_czFiles.length} foto dipilih`;
  const thumbsEl = document.getElementById('cz-thumbs');
  thumbsEl.innerHTML = '';
  _czFiles.slice(0, 8).forEach(f => {
    const img = document.createElement('img');
    img.style.cssText = 'width:52px;height:40px;object-fit:cover;border-radius:5px;border:1px solid var(--border)';
    const reader = new FileReader();
    reader.onload = e2 => { img.src = e2.target.result; };
    reader.readAsDataURL(f);
    thumbsEl.appendChild(img);
  });
  if (_czFiles.length > 8) {
    const more = document.createElement('div');
    more.style.cssText = 'width:52px;height:40px;border-radius:5px;background:var(--border);display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--ink-soft)';
    more.textContent = `+${_czFiles.length - 8}`;
    thumbsEl.appendChild(more);
  }
}

function czClear(e) {
  e.stopPropagation();
  _czFiles = [];
  czReset();
}

function czReset() {
  document.getElementById('cz-files').value = '';
  document.getElementById('cz-placeholder').style.display = '';
  document.getElementById('cz-preview').style.display     = 'none';
  document.getElementById('cz-zone').classList.remove('drag-over');
}

async function createAlbum() {
  const judul   = document.getElementById('c-judul').value.trim();
  const tanggal = document.getElementById('c-tanggal').value;
  if (!judul)          { alert('Judul album wajib diisi.'); return; }
  if (!tanggal)        { alert('Tanggal wajib diisi.'); return; }
  if (!_czFiles.length){ alert('Upload minimal 1 foto.'); return; }

  const btn       = document.getElementById('c-save-btn');
  const area      = document.getElementById('c-progress-area');
  const label     = document.getElementById('c-progress-label');
  const countEl   = document.getElementById('c-progress-count');
  const bar       = document.getElementById('c-progress-bar');
  const n         = _czFiles.length;

  const setP = (lbl, cur, total, pStart, pEnd) => {
    label.textContent   = lbl;
    countEl.textContent = total > 0 ? `${cur}/${total} foto` : '';
    bar.style.width     = (pStart + (cur / (total || 1)) * (pEnd - pStart)) + '%';
  };

  btn.disabled = true;
  bar.style.width = '0%';
  area.style.display = '';

  // Phase 1: compress (0 → 50%)
  setP('Mengompresi foto...', 0, n, 0, 50);
  const compressed = await compressAll(_czFiles, (i, tot) => setP('Mengompresi foto...', i, tot, 0, 50));

  // Phase 2: create album metadata
  setP('Membuat album...', 1, 1, 50, 55);
  const metaRes = await fetch('/api/galeri', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken },
    body: JSON.stringify({
      judul, tanggal,
      kategori:    document.getElementById('c-kategori').value,
      deskripsi:   document.getElementById('c-deskripsi').value.trim() || null,
      is_featured: document.getElementById('c-featured').checked,
      is_public:   document.getElementById('c-public').checked,
    }),
  });
  const metaJson = await metaRes.json();
  if (!metaJson.success) {
    btn.disabled = false; area.style.display = 'none';
    alert(metaJson.message); return;
  }
  const albumId = metaJson.data.id;

  // Phase 3: upload one photo per request (55 → 100%)
  let ok = 0;
  for (let i = 0; i < compressed.length; i++) {
    setP('Mengupload foto...', i, compressed.length, 55, 100);
    const fd = new FormData();
    fd.append('fotos[]', compressed[i]);
    try {
      const r = await fetch(`/api/galeri/${albumId}/foto`, { method:'POST', headers:{'X-CSRF-TOKEN':_csrfToken}, body:fd });
      const j = await r.json();
      if (j.success) ok++;
    } catch { /* skip */ }
  }
  setP('Selesai', compressed.length, compressed.length, 55, 100);

  btn.disabled = false;
  area.style.display = 'none';
  closeCreate();
  const msg = ok < compressed.length
    ? `Album dibuat, ${ok}/${compressed.length} foto berhasil diupload.`
    : `Album berhasil dibuat dengan ${ok} foto.`;
  showToast(msg);
  load();
}

// ── EDIT INFO ─────────────────────────────────────────────────
function openEdit(a) {
  document.getElementById('e-id').value       = a.id;
  document.getElementById('e-judul').value    = a.judul;
  document.getElementById('e-tanggal').value  = a.tanggal.slice(0,10);
  document.getElementById('e-kategori').value = a.kategori || 'kegiatan';
  document.getElementById('e-deskripsi').value= a.deskripsi || '';
  document.getElementById('e-featured').checked = !!a.is_featured;
  document.getElementById('e-public').checked   = !!a.is_public;
  document.getElementById('edit-overlay').style.display = 'flex';
}

function closeEdit() { document.getElementById('edit-overlay').style.display = 'none'; }

async function saveEdit() {
  const id     = document.getElementById('e-id').value;
  const judul  = document.getElementById('e-judul').value.trim();
  const tanggal= document.getElementById('e-tanggal').value;
  if (!judul || !tanggal) { alert('Judul dan tanggal wajib diisi.'); return; }

  const r = await fetch(`/api/galeri/${id}`, {
    method:'PUT',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},
    body: JSON.stringify({
      judul, tanggal,
      kategori:    document.getElementById('e-kategori').value,
      deskripsi:   document.getElementById('e-deskripsi').value.trim() || null,
      is_featured: document.getElementById('e-featured').checked,
      is_public:   document.getElementById('e-public').checked,
    }),
  });
  const j = await r.json();
  if (j.success) { closeEdit(); showToast(j.message); load(); }
  else alert(j.message);
}

// ── MANAGE PHOTOS ─────────────────────────────────────────────
async function openManage(id, title) {
  _manageId = id;
  document.getElementById('manage-title').textContent = title;
  document.getElementById('manage-overlay').style.display = 'flex';
  document.body.style.overflow = 'hidden';
  await loadManageFotos();
}

function closeManage() {
  document.getElementById('manage-overlay').style.display = 'none';
  document.body.style.overflow = '';
  _manageId = null;
  load(); // refresh album count
}

async function loadManageFotos() {
  const r = await fetch(`/api/galeri/${_manageId}`);
  const j = await r.json();
  _manageAlbum = j.success ? j.data : null;
  renderManageGrid();
}

function renderManageGrid() {
  const fotos = _manageAlbum?.fotos || [];
  const grid  = document.getElementById('manage-grid');
  const empty = document.getElementById('manage-empty');
  const cnt   = document.getElementById('manage-count');
  cnt.textContent = `${fotos.length} foto`;

  if (!fotos.length) { grid.innerHTML = ''; empty.style.display = ''; return; }
  empty.style.display = 'none';

  grid.innerHTML = fotos.map(f => `
    <div class="m-photo" title="${esc(f.keterangan||'')}">
      <img src="${esc(f.foto_url)}" alt="" loading="lazy" onclick="previewCover('${esc(f.foto_url)}','')">
      <button class="m-photo-del" onclick="deleteFoto(${f.id})" title="Hapus foto ini"><i class="fa fa-xmark"></i></button>
    </div>`).join('');
}

async function addFotos(fileList) {
  if (!fileList.length) return;

  document.getElementById('m-files').value = '';

  const uploading  = document.getElementById('manage-uploading');
  const mLabel     = document.getElementById('m-progress-label');
  const mCount     = document.getElementById('m-progress-count');
  const mBar       = document.getElementById('m-progress-bar');
  const files      = Array.from(fileList);

  const setP = (lbl, cur, total, pStart, pEnd) => {
    mLabel.textContent = lbl;
    mCount.textContent = total > 0 ? `${cur}/${total} foto` : '';
    mBar.style.width   = (pStart + (cur / (total || 1)) * (pEnd - pStart)) + '%';
  };

  mBar.style.width = '0%';
  uploading.style.display = '';

  // Compress phase (0 → 50%)
  setP('Mengompresi foto...', 0, files.length, 0, 50);
  const compressed = await compressAll(files, (i, n) => setP('Mengompresi foto...', i, n, 0, 50));

  // Upload phase (50 → 100%), one at a time
  let ok = 0;
  for (let i = 0; i < compressed.length; i++) {
    setP('Mengupload foto...', i, compressed.length, 50, 100);
    const fd = new FormData();
    fd.append('fotos[]', compressed[i]);
    try {
      const r = await fetch(`/api/galeri/${_manageId}/foto`, { method:'POST', headers:{'X-CSRF-TOKEN':_csrfToken}, body:fd });
      const j = await r.json();
      if (j.success) ok++;
    } catch { /* skip */ }
  }

  uploading.style.display = 'none';
  showToast(`${ok} foto berhasil ditambahkan.`);
  await loadManageFotos();
}

async function deleteFoto(fotoId) {
  if (!confirm('Hapus foto ini dari album?')) return;
  const r = await fetch(`/api/galeri/foto/${fotoId}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  if (j.success) { showToast(j.message); await loadManageFotos(); }
  else alert(j.message);
}

// ── DELETE ALBUM ──────────────────────────────────────────────
async function delAlbum(id) {
  if (!confirm('Hapus album ini beserta semua foto di dalamnya?')) return;
  const r = await fetch(`/api/galeri/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  if (j.success) { showToast(j.message); load(); }
  else alert(j.message);
}

// ── Quick lightbox ────────────────────────────────────────────
function previewCover(url, title) {
  if (!url) return;
  document.getElementById('qlb-img').src = url;
  document.getElementById('qlb').style.display = 'flex';
}
function closeQlbBtn() {
  document.getElementById('qlb').style.display = 'none';
}
function closeQlb(e) { if (e.target === document.getElementById('qlb')) closeQlbBtn(); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeQlbBtn(); } });

load();
</script>
@endsection
