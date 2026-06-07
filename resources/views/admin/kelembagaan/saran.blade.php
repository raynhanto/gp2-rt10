@extends('layouts.admin')
@section('title', 'Saran & Keluhan — Admin')
@section('content')
<style>
.sk-filters { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1.25rem; }
.sk-filter-pill { padding:5px 14px; border-radius:99px; border:1.5px solid var(--border); background:#fff; font-size:12px; font-weight:500; cursor:pointer; color:var(--ink-soft); font-family:'DM Sans',sans-serif; transition:.15s; }
.sk-filter-pill.active { background:var(--forest); color:#fff; border-color:var(--forest); }

.sk-row { background:var(--card); border:1px solid var(--border); border-radius:10px; padding:1rem 1.25rem; margin-bottom:.625rem; display:grid; grid-template-columns:1fr auto; gap:.75rem; align-items:start; cursor:pointer; transition:box-shadow .15s; }
.sk-row:hover { box-shadow:0 2px 10px rgba(0,0,0,.07); }
.sk-row.is-baru { border-left:3px solid var(--rust); }
.sk-row.is-dibaca { border-left:3px solid var(--gold); }
.sk-row.is-diproses { border-left:3px solid var(--forest-light); }
.sk-row.is-selesai { border-left:3px solid rgba(26,61,43,.25); }

.sk-badge { display:inline-block; font-size:10px; font-weight:700; padding:2px 9px; border-radius:99px; letter-spacing:.03em; }
.sk-badge-saran { background:#e8f5ec; color:var(--forest); }
.sk-badge-keluhan { background:#fdecea; color:var(--rust); }
.sk-badge-pertanyaan { background:#e8f0fd; color:#2d5aa8; }

.sk-status-badge { font-size:10px; font-weight:700; padding:2px 9px; border-radius:99px; white-space:nowrap; }
.sk-status-baru { background:#fdecea; color:var(--rust); }
.sk-status-dibaca { background:var(--gold-pale); color:var(--gold-dark); }
.sk-status-diproses { background:var(--forest-pale); color:var(--forest); }
.sk-status-selesai { background:#efefed; color:var(--ink-soft); }

/* Detail panel */
.sk-detail { display:none; position:fixed; top:0; right:0; bottom:0; width:440px; background:#fff; border-left:1px solid var(--border); box-shadow:-8px 0 32px rgba(0,0,0,.12); z-index:300; flex-direction:column; overflow:hidden; }
.sk-detail.open { display:flex; }
.sk-detail-header { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); flex-shrink:0; display:flex; align-items:flex-start; gap:.75rem; }
.sk-detail-header-info { flex:1; min-width:0; }
.sk-detail-body { flex:1; overflow-y:auto; padding:1.25rem 1.5rem; }
.sk-detail-footer { padding:1rem 1.5rem; border-top:1px solid var(--border); flex-shrink:0; }
.sk-field-label { font-size:11px; font-weight:600; color:var(--ink-mid); margin-bottom:.3rem; text-transform:uppercase; letter-spacing:.06em; }
.sk-field-val { font-size:13px; color:var(--ink); line-height:1.65; white-space:pre-line; }
.sk-tanggapan-box { background:var(--forest-pale); border-radius:8px; padding:1rem; margin-top:1rem; }
.sk-tanggapan-label { font-size:11px; font-weight:700; color:var(--forest); margin-bottom:.4rem; display:flex; align-items:center; gap:.4rem; }
.sk-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:299; }
.sk-overlay.open { display:block; }
.sk-status-sel { border:1.5px solid var(--border); border-radius:8px; padding:.5rem .75rem; font-size:13px; font-family:'DM Sans',sans-serif; color:var(--ink); background:#fff; width:100%; margin-bottom:.75rem; }
</style>

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Saran &amp; Keluhan</div>
    <div class="admin-page-sub">Pesan dari warga — klik baris untuk membuka detail &amp; balas</div>
  </div>
  <div style="display:flex;align-items:center;gap:.5rem">
    <span id="sk-count" style="font-size:12px;color:var(--ink-mute)"></span>
  </div>
</div>

{{-- Filters --}}
<div class="sk-filters">
  <button class="sk-filter-pill active" data-filter="" onclick="setFilter(this,'')">Semua</button>
  <button class="sk-filter-pill" data-filter="status:baru" onclick="setFilter(this,'status:baru')">
    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--rust);margin-right:4px;vertical-align:middle"></span>Baru
  </button>
  <button class="sk-filter-pill" data-filter="status:dibaca" onclick="setFilter(this,'status:dibaca')">
    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--gold);margin-right:4px;vertical-align:middle"></span>Dibaca
  </button>
  <button class="sk-filter-pill" data-filter="status:diproses" onclick="setFilter(this,'status:diproses')">
    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--forest-light);margin-right:4px;vertical-align:middle"></span>Diproses
  </button>
  <button class="sk-filter-pill" data-filter="status:selesai" onclick="setFilter(this,'status:selesai')">
    <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--ink-mute);margin-right:4px;vertical-align:middle"></span>Selesai
  </button>
  <div style="width:1px;background:var(--border);margin:0 4px"></div>
  <button class="sk-filter-pill" data-filter="kategori:saran" onclick="setFilter(this,'kategori:saran')">Saran</button>
  <button class="sk-filter-pill" data-filter="kategori:keluhan" onclick="setFilter(this,'kategori:keluhan')">Keluhan</button>
  <button class="sk-filter-pill" data-filter="kategori:pertanyaan" onclick="setFilter(this,'kategori:pertanyaan')">Pertanyaan</button>
</div>

{{-- List --}}
<div id="sk-list">
  <div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div>
</div>

{{-- Detail panel overlay --}}
<div class="sk-overlay" id="sk-overlay" onclick="closeDetail()"></div>

{{-- Detail panel --}}
<div class="sk-detail" id="sk-detail">
  <div class="sk-detail-header">
    <div class="sk-detail-header-info">
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.4rem;flex-wrap:wrap">
        <span id="dp-kat-badge"></span>
        <span id="dp-status-badge"></span>
      </div>
      <div id="dp-judul" style="font-family:'DM Serif Display',serif;font-size:1.1rem;color:var(--forest);line-height:1.3"></div>
      <div id="dp-meta" style="font-size:11px;color:var(--ink-mute);margin-top:.3rem"></div>
    </div>
    <button onclick="closeDetail()" style="flex-shrink:0;background:none;border:none;cursor:pointer;color:var(--ink-soft);font-size:18px;padding:2px"><i class="fa fa-xmark"></i></button>
  </div>

  <div class="sk-detail-body">
    <div class="sk-field-label">Pesan</div>
    <div id="dp-isi" class="sk-field-val" style="margin-bottom:1.25rem"></div>

    <div id="dp-wa-wrap" style="display:none;margin-bottom:1.25rem">
      <div class="sk-field-label">Kontak WhatsApp</div>
      <div id="dp-wa" class="sk-field-val"></div>
    </div>

    <div id="dp-tanggapan-wrap" style="display:none">
      <div class="sk-tanggapan-box">
        <div class="sk-tanggapan-label">
          <i class="fa fa-reply" style="font-size:11px"></i> Tanggapan Admin
        </div>
        <div id="dp-tanggapan" style="font-size:13px;color:var(--forest);white-space:pre-line;line-height:1.65"></div>
        <div id="dp-tanggapan-meta" style="font-size:11px;color:var(--forest-light);margin-top:.4rem"></div>
      </div>
    </div>
  </div>

  <div class="sk-detail-footer">
    <div class="sk-field-label" style="margin-bottom:.5rem">Ubah Status</div>
    <select id="dp-status-sel" class="sk-status-sel" onchange="updateStatus()">
      <option value="baru">Baru</option>
      <option value="dibaca">Dibaca</option>
      <option value="diproses">Diproses</option>
      <option value="selesai">Selesai</option>
    </select>

    <div class="sk-field-label" style="margin-bottom:.5rem">Tanggapan</div>
    <textarea id="dp-tanggapan-input" rows="3"
      placeholder="Tulis tanggapan untuk warga (opsional)..."
      style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:.6rem .75rem;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--ink);resize:vertical;box-sizing:border-box;margin-bottom:.75rem"></textarea>

    <div style="display:flex;gap:.5rem;align-items:center">
      <button class="btn-primary" style="flex:1;justify-content:center" onclick="saveDetail()">
        <i class="fa fa-floppy-disk"></i> Simpan
      </button>
      <button onclick="deleteSaran()" style="padding:10px 14px;border-radius:100px;border:1.5px solid var(--border);background:#fff;color:var(--rust);cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif">
        <i class="fa fa-trash"></i>
      </button>
    </div>
    <div id="dp-msg" style="font-size:12px;margin-top:.5rem;display:none"></div>
  </div>
</div>

<script>
const _csrf = '{{ csrf_token() }}';
let _all = [], _curFilter = '', _curId = null;

const KAT_LBL    = { saran:'Saran', keluhan:'Keluhan', pertanyaan:'Pertanyaan' };
const KAT_CLS    = { saran:'sk-badge-saran', keluhan:'sk-badge-keluhan', pertanyaan:'sk-badge-pertanyaan' };
const STATUS_LBL = { baru:'Baru', dibaca:'Dibaca', diproses:'Diproses', selesai:'Selesai' };
const STATUS_CLS = { baru:'sk-status-baru', dibaca:'sk-status-dibaca', diproses:'sk-status-diproses', selesai:'sk-status-selesai' };

function fmtTgl(s) {
  const d = new Date((s||'').replace(' ','T'));
  if (isNaN(d)) return '—';
  return d.toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'}) + ' ' +
         d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
}

function setFilter(el, filter) {
  document.querySelectorAll('.sk-filter-pill').forEach(p => p.classList.remove('active'));
  el.classList.add('active');
  _curFilter = filter;
  renderList();
}

function filteredList() {
  if (!_curFilter) return _all;
  const [key, val] = _curFilter.split(':');
  return _all.filter(s => s[key] === val);
}

function renderList() {
  const list = filteredList();
  const el = document.getElementById('sk-list');
  document.getElementById('sk-count').textContent = `${list.length} pesan`;

  if (!list.length) {
    el.innerHTML = `<div style="text-align:center;padding:3rem;color:var(--ink-soft);background:var(--card);border-radius:10px;border:1px solid var(--border)">Tidak ada pesan.</div>`;
    return;
  }

  el.innerHTML = list.map(s => {
    const nama = s.is_anonym ? 'Anonim' : (s.nama_display || '—');
    const tgl  = fmtTgl(s.created_at);
    const hasTanggapan = !!s.tanggapan;
    return `<div class="sk-row is-${s.status}" onclick="openDetail(${s.id})">
      <div>
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.35rem;flex-wrap:wrap">
          <span class="sk-badge ${KAT_CLS[s.kategori]}">${KAT_LBL[s.kategori]}</span>
          <span class="sk-status-badge ${STATUS_CLS[s.status]}">${STATUS_LBL[s.status]}</span>
          ${hasTanggapan ? '<span style="font-size:10px;color:var(--forest-light);font-weight:600"><i class="fa fa-reply" style="font-size:9px"></i> Sudah dibalas</span>' : ''}
        </div>
        <div style="font-size:14px;font-weight:600;color:var(--ink);margin-bottom:.25rem">${esc(s.judul)}</div>
        <div style="font-size:12px;color:var(--ink-mute)">${esc(nama)} &middot; ${tgl}</div>
      </div>
      <div>
        <i class="fa fa-chevron-right" style="font-size:12px;color:var(--ink-mute)"></i>
      </div>
    </div>`;
  }).join('');
}

function openDetail(id) {
  const s = _all.find(x => x.id === id);
  if (!s) return;
  _curId = id;

  const nama = s.is_anonym ? '<em>Anonim</em>' : esc(s.nama_display || '—');
  const email = s.user?.email ? ` · ${s.user.email}` : '';

  document.getElementById('dp-kat-badge').className = `sk-badge ${KAT_CLS[s.kategori]}`;
  document.getElementById('dp-kat-badge').textContent = KAT_LBL[s.kategori];
  document.getElementById('dp-status-badge').className = `sk-status-badge ${STATUS_CLS[s.status]}`;
  document.getElementById('dp-status-badge').textContent = STATUS_LBL[s.status];
  document.getElementById('dp-judul').textContent = s.judul;
  document.getElementById('dp-meta').innerHTML = `${nama}${email} &middot; ${fmtTgl(s.created_at)}`;
  document.getElementById('dp-isi').textContent = s.isi;
  document.getElementById('dp-status-sel').value = s.status;
  document.getElementById('dp-tanggapan-input').value = s.tanggapan || '';
  document.getElementById('dp-msg').style.display = 'none';

  const waWrap = document.getElementById('dp-wa-wrap');
  if (s.no_wa) {
    document.getElementById('dp-wa').innerHTML = `<a href="https://wa.me/${s.no_wa}" target="_blank" style="color:var(--forest)">${esc(s.no_wa)}</a>`;
    waWrap.style.display = '';
  } else {
    waWrap.style.display = 'none';
  }

  const tanggapanWrap = document.getElementById('dp-tanggapan-wrap');
  if (s.tanggapan) {
    document.getElementById('dp-tanggapan').textContent = s.tanggapan;
    document.getElementById('dp-tanggapan-meta').textContent =
      `oleh ${s.tanggapan_oleh_nama || 'Admin'} · ${fmtTgl(s.tanggapan_at)}`;
    tanggapanWrap.style.display = '';
  } else {
    tanggapanWrap.style.display = 'none';
  }

  document.getElementById('sk-detail').classList.add('open');
  document.getElementById('sk-overlay').classList.add('open');

  // Mark as dibaca if still baru
  if (s.status === 'baru') {
    fetch(`/api/saran-keluhan/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
      body: JSON.stringify({ status: 'dibaca' }),
    }).then(() => {
      s.status = 'dibaca';
      document.getElementById('dp-status-sel').value = 'dibaca';
      document.getElementById('dp-status-badge').className = `sk-status-badge ${STATUS_CLS['dibaca']}`;
      document.getElementById('dp-status-badge').textContent = STATUS_LBL['dibaca'];
      renderList();
    });
  }
}

function closeDetail() {
  document.getElementById('sk-detail').classList.remove('open');
  document.getElementById('sk-overlay').classList.remove('open');
  _curId = null;
}

async function updateStatus() {
  if (!_curId) return;
  const status = document.getElementById('dp-status-sel').value;
  const s = _all.find(x => x.id === _curId);
  if (!s) return;

  const r = await fetch(`/api/saran-keluhan/${_curId}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
    body: JSON.stringify({ status }),
  });
  const j = await r.json();
  if (j.success) {
    s.status = status;
    document.getElementById('dp-status-badge').className = `sk-status-badge ${STATUS_CLS[status]}`;
    document.getElementById('dp-status-badge').textContent = STATUS_LBL[status];
    renderList();
  }
}

async function saveDetail() {
  if (!_curId) return;
  const status    = document.getElementById('dp-status-sel').value;
  const tanggapan = document.getElementById('dp-tanggapan-input').value.trim();
  const msgEl     = document.getElementById('dp-msg');

  const r = await fetch(`/api/saran-keluhan/${_curId}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
    body: JSON.stringify({ status, tanggapan }),
  });
  const j = await r.json();

  msgEl.style.display = '';
  msgEl.style.color   = j.success ? 'var(--forest)' : 'var(--rust)';
  msgEl.textContent   = j.message;

  if (j.success) {
    const s = _all.find(x => x.id === _curId);
    if (s) {
      s.status    = status;
      s.tanggapan = tanggapan || null;
    }
    renderList();
    setTimeout(() => { msgEl.style.display = 'none'; }, 2500);
  }
}

async function deleteSaran() {
  if (!_curId || !confirm('Hapus pesan ini?')) return;
  const r = await fetch(`/api/saran-keluhan/${_curId}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': _csrf },
  });
  const j = await r.json();
  if (j.success) {
    _all = _all.filter(x => x.id !== _curId);
    closeDetail();
    renderList();
  }
}

function esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function load() {
  const r = await fetch('/api/saran-keluhan');
  const j = await r.json();
  if (!j.success) return;
  _all = j.data;
  renderList();
}

load();
</script>
@endsection
