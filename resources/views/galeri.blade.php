@extends('layouts.app')
@section('title', 'Galeri Foto — RT 10 Golden Park 2')
@section('styles')
<style>
/* ── Hero ───────────────────────────────────────────────────── */
.gal-hero{background:linear-gradient(135deg,#0c1e14 0%,#1A3D2B 55%,#2e6647 100%);padding:4.5rem 0 3rem}

/* ── Category tabs ───────────────────────────────────────────── */
.gtab{padding:7px 20px;border-radius:99px;font-size:12px;font-weight:600;border:1.5px solid rgba(255,255,255,.18);background:transparent;color:rgba(255,255,255,.6);cursor:pointer;transition:all .15s;font-family:'DM Sans',sans-serif;letter-spacing:.01em}
.gtab.on{background:#fff;color:var(--forest);border-color:#fff}

/* ── Album grid ──────────────────────────────────────────────── */
.alb-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.alb-card{background:#fff;border-radius:var(--radius);overflow:hidden;cursor:pointer;box-shadow:0 2px 12px rgba(0,0,0,.06);border:1px solid var(--border);transition:transform .2s,box-shadow .2s}
.alb-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.12)}
.alb-cover-wrap{position:relative;aspect-ratio:4/3;overflow:hidden;background:#111}
.alb-cover-wrap img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .35s ease}
.alb-card:hover .alb-cover-wrap img{transform:scale(1.05)}
.alb-count-badge{position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,.6);color:#fff;font-size:11px;font-weight:600;padding:3px 10px;border-radius:99px;display:flex;align-items:center;gap:5px;backdrop-filter:blur(4px)}
.alb-featured-badge{position:absolute;top:8px;left:8px;background:var(--gold);color:#fff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:99px;letter-spacing:.05em}
.alb-body{padding:14px 16px}
.alb-chip{font-size:10px;font-weight:700;padding:2px 9px;border-radius:99px;display:inline-block;margin-bottom:6px;text-transform:capitalize}
.alb-title{font-family:'DM Serif Display',serif;font-size:1.05rem;color:var(--ink);line-height:1.3;margin-bottom:4px}
.alb-date{font-size:11px;color:var(--ink-mute)}

/* ── Chip colors ─────────────────────────────────────────────── */
.chip-kegiatan   {background:#e8f5ec;color:#1A3D2B}
.chip-sosial     {background:#fde8e4;color:#7A1A0A}
.chip-fasilitas  {background:#e4ecfd;color:#1A3060}
.chip-dokumentasi{background:#fdf8e4;color:#5A4A00}
.chip-lainnya    {background:#f0ece8;color:#3A3030}

/* ── Stat pill (hero) ────────────────────────────────────────── */
.stat-pill{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.16);padding:6px 16px;border-radius:99px;font-size:12px;font-weight:500;color:rgba(255,255,255,.8);backdrop-filter:blur(4px)}
.stat-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}

/* ── Empty state ─────────────────────────────────────────────── */
.gal-empty{text-align:center;padding:4rem 2rem;background:#fff;border-radius:var(--radius);border:1px solid var(--border)}

/* ── Lightbox ────────────────────────────────────────────────── */
.lb{position:fixed;inset:0;z-index:500;background:rgba(0,0,0,.93);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .22s}
.lb.open{opacity:1;pointer-events:all}
.lb-inner{position:relative;max-width:min(940px,92vw);width:100%;display:flex;flex-direction:column;align-items:center}
.lb-img-wrap{width:100%;max-height:70vh;display:flex;align-items:center;justify-content:center}
.lb-img-wrap img{max-width:100%;max-height:70vh;object-fit:contain;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,.6);display:block;transition:opacity .18s}
.lb-img-wrap img.loading{opacity:.3}
.lb-info{width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:.9rem 1.2rem;margin-top:12px;backdrop-filter:blur(12px)}
.lb-album-title{font-size:11px;font-weight:600;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px}
.lb-caption{font-size:14px;font-weight:500;color:#fff;line-height:1.4}
.lb-meta{display:flex;align-items:center;gap:10px;margin-top:6px}
.lb-close{position:fixed;top:1.25rem;right:1.5rem;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.1);border:none;cursor:pointer;color:#fff;font-size:18px;display:flex;align-items:center;justify-content:center;transition:background .15s;z-index:502}
.lb-close:hover{background:rgba(255,255,255,.22)}
.lb-arrow{position:fixed;top:50%;transform:translateY(-50%);width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.1);border:none;cursor:pointer;color:#fff;font-size:18px;display:flex;align-items:center;justify-content:center;transition:all .15s;z-index:502}
.lb-arrow:hover{background:rgba(255,255,255,.26)}
.lb-prev{left:1.2rem}
.lb-next{right:1.2rem}
.lb-counter{position:fixed;top:1.35rem;left:50%;transform:translateX(-50%);font-size:12px;color:rgba(255,255,255,.5);background:rgba(0,0,0,.45);padding:4px 14px;border-radius:99px;backdrop-filter:blur(6px);z-index:502}

/* ── Thumbnail strip ─────────────────────────────────────────── */
.lb-thumbs{display:flex;gap:6px;margin-top:10px;overflow-x:auto;max-width:min(940px,92vw);padding-bottom:2px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.2) transparent}
.lb-thumb{width:52px;height:40px;border-radius:5px;object-fit:cover;cursor:pointer;opacity:.45;border:2px solid transparent;transition:opacity .15s,border-color .15s;flex-shrink:0}
.lb-thumb.active{opacity:1;border-color:rgba(255,255,255,.8)}
.lb-thumb:hover:not(.active){opacity:.75}

@media(max-width:900px){.alb-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:560px){
  .alb-grid{grid-template-columns:1fr 1fr;gap:10px}
  .lb-arrow{display:none}
  .lb-thumbs{display:none}
}
</style>
@endsection
@section('content')

<div class="gal-hero">
  <div class="container">
    <div class="fade-in">
      <div style="display:inline-flex;align-items:center;gap:8px;font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,160,48,.75);margin-bottom:.875rem">
        <span style="width:20px;height:2px;background:var(--gold);display:inline-block;border-radius:2px"></span>
        Kelembagaan RT 10
      </div>
      <h1 style="font-family:'DM Serif Display',serif;font-size:clamp(2rem,4vw,2.9rem);color:#fff;margin:0 0 .5rem;line-height:1.1">Galeri Foto</h1>
      <p style="font-size:14px;color:rgba(255,255,255,.5);margin:0 0 1.5rem">Dokumentasi kegiatan dan fasilitas RT 10 Golden Park 2.</p>
      <div style="display:flex;gap:8px;flex-wrap:wrap" id="hero-stats"></div>
    </div>
  </div>
</div>

<div style="background:var(--forest);padding:.65rem 0;position:sticky;top:0;z-index:10;border-bottom:1px solid rgba(0,0,0,.12)">
  <div class="container">
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <button class="gtab on" onclick="setKat(this,'')"        >Semua</button>
      <button class="gtab" onclick="setKat(this,'kegiatan')"   >Kegiatan</button>
      <button class="gtab" onclick="setKat(this,'sosial')"     >Sosial</button>
      <button class="gtab" onclick="setKat(this,'fasilitas')"  >Fasilitas</button>
      <button class="gtab" onclick="setKat(this,'dokumentasi')">Dokumentasi</button>
      <button class="gtab" onclick="setKat(this,'lainnya')"    >Lainnya</button>
    </div>
  </div>
</div>

<main style="padding:2.5rem 0 5rem;background:var(--warm)">
<div class="container">

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest)" id="grid-title">Semua Album</div>
    <div style="font-size:12px;color:var(--ink-mute)" id="grid-count"></div>
  </div>

  <div id="alb-grid" class="alb-grid"></div>

  <div id="alb-empty" class="gal-empty" style="display:none">
    <i class="fa-regular fa-images" style="font-size:2.5rem;color:var(--ink-mute);margin-bottom:12px;display:block"></i>
    <div style="font-size:15px;color:var(--ink-soft)">Belum ada foto untuk kategori ini.</div>
  </div>

</div>
</main>

{{-- Lightbox --}}
<div class="lb" id="lb">
  <button class="lb-close" onclick="closeLb()"><i class="fa fa-xmark"></i></button>
  <div class="lb-counter" id="lb-counter"></div>
  <button class="lb-arrow lb-prev" onclick="lbNav(-1)"><i class="fa fa-chevron-left"></i></button>
  <button class="lb-arrow lb-next" onclick="lbNav(1)"><i class="fa fa-chevron-right"></i></button>
  <div class="lb-inner">
    <div class="lb-img-wrap"><img id="lb-img" src="" alt=""></div>
    <div class="lb-thumbs" id="lb-thumbs"></div>
    <div class="lb-info">
      <div class="lb-album-title" id="lb-album-title"></div>
      <div class="lb-caption" id="lb-caption"></div>
      <div class="lb-meta">
        <span id="lb-chip" class="alb-chip"></span>
        <span style="font-size:11px;color:rgba(255,255,255,.4)" id="lb-date"></span>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script>
const BULAN_S  = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
const KAT_LBL  = { kegiatan:'Kegiatan', sosial:'Sosial', fasilitas:'Fasilitas', dokumentasi:'Dokumentasi', lainnya:'Lainnya' };

let _albums   = [], _filtered = [], _kat = '';
let _lbAlbum  = null, _lbFotos = [], _lbIdx = 0;

function fmtTgl(d) {
  const dt = new Date(d.slice(0,10) + 'T00:00:00');
  return `${dt.getDate()} ${BULAN_S[dt.getMonth()]} ${dt.getFullYear()}`;
}

async function init() {
  const r = await fetch('/api/galeri');
  const j = await r.json();
  _albums = j.success ? j.data : [];
  renderStats();
  render();
}

function renderStats() {
  const totalAlbums = _albums.length;
  const totalFotos  = _albums.reduce((s, a) => s + (a.fotos_count || 0), 0);
  const cats        = new Set(_albums.map(a => a.kategori)).size;
  document.getElementById('hero-stats').innerHTML = [
    { c:'#7dd3a8', t:`${totalAlbums} album` },
    { c:'rgba(255,255,255,.4)', t:`${totalFotos} foto` },
    { c:'var(--gold-light)', t:`${cats} kategori` },
  ].map(s => `<div class="stat-pill"><div class="stat-dot" style="background:${s.c}"></div>${s.t}</div>`).join('');
}

function setKat(btn, kat) {
  document.querySelectorAll('.gtab').forEach(b => b.classList.remove('on'));
  btn.classList.add('on');
  _kat = kat;
  render();
}

function render() {
  _filtered = _kat ? _albums.filter(a => a.kategori === _kat) : _albums;

  const gridEl  = document.getElementById('alb-grid');
  const emptyEl = document.getElementById('alb-empty');
  const titleEl = document.getElementById('grid-title');
  const countEl = document.getElementById('grid-count');

  titleEl.textContent = _kat ? (KAT_LBL[_kat] || _kat) : 'Semua Album';
  countEl.textContent = `${_filtered.length} album`;

  if (!_filtered.length) {
    gridEl.innerHTML = '';
    emptyEl.style.display = '';
    return;
  }
  emptyEl.style.display = 'none';

  gridEl.innerHTML = _filtered.map((a, i) => {
    const cover = a.cover_url || `https://picsum.photos/seed/alb${a.id}/600/400`;
    const cnt   = a.fotos_count || 0;
    return `
    <div class="alb-card" onclick="openAlbum(${a.id})">
      <div class="alb-cover-wrap">
        <img src="${cover}" alt="${esc(a.judul)}" loading="lazy" onerror="this.src='https://picsum.photos/seed/alb${a.id}/600/400'">
        ${a.is_featured ? `<span class="alb-featured-badge"><i class="fa fa-star" style="font-size:9px"></i> Unggulan</span>` : ''}
        <span class="alb-count-badge"><i class="fa fa-images" style="font-size:10px"></i> ${cnt}</span>
      </div>
      <div class="alb-body">
        <span class="alb-chip chip-${a.kategori}">${KAT_LBL[a.kategori] || a.kategori}</span>
        <div class="alb-title">${esc(a.judul)}</div>
        <div class="alb-date">${fmtTgl(a.tanggal)}</div>
      </div>
    </div>`;
  }).join('');
}

// ── Open album → fetch photos → lightbox ──────────────────────
async function openAlbum(id) {
  const r = await fetch(`/api/galeri/${id}`);
  const j = await r.json();
  if (!j.success) return;
  _lbAlbum = j.data;
  _lbFotos = j.data.fotos || [];
  if (!_lbFotos.length) return;
  _lbIdx = 0;
  document.getElementById('lb').classList.add('open');
  document.body.style.overflow = 'hidden';
  renderLb();
  renderThumbs();
}

function renderLb() {
  const foto  = _lbFotos[_lbIdx];
  const album = _lbAlbum;
  const img   = document.getElementById('lb-img');
  img.classList.add('loading');
  img.onload = () => img.classList.remove('loading');
  img.src = foto.foto_url;

  document.getElementById('lb-counter').textContent    = `${_lbIdx + 1} / ${_lbFotos.length}`;
  document.getElementById('lb-album-title').textContent = album.judul;
  document.getElementById('lb-caption').textContent    = foto.keterangan || album.judul;
  const chip = document.getElementById('lb-chip');
  chip.textContent = KAT_LBL[album.kategori] || album.kategori;
  chip.className   = `alb-chip chip-${album.kategori}`;
  document.getElementById('lb-date').textContent = fmtTgl(album.tanggal);

  document.querySelectorAll('.lb-thumb').forEach((t, i) => t.classList.toggle('active', i === _lbIdx));
}

function renderThumbs() {
  const thumbsEl = document.getElementById('lb-thumbs');
  if (_lbFotos.length <= 1) { thumbsEl.innerHTML = ''; return; }
  thumbsEl.innerHTML = _lbFotos.map((f, i) =>
    `<img class="lb-thumb${i === 0 ? ' active' : ''}" src="${f.foto_url}" alt="" onclick="lbGoTo(${i})">`
  ).join('');
}

function lbGoTo(idx) {
  _lbIdx = idx;
  renderLb();
  // scroll thumb into view
  const thumbs = document.querySelectorAll('.lb-thumb');
  if (thumbs[idx]) thumbs[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
}

function lbNav(dir) {
  _lbIdx = (_lbIdx + dir + _lbFotos.length) % _lbFotos.length;
  renderLb();
  const thumbs = document.querySelectorAll('.lb-thumb');
  if (thumbs[_lbIdx]) thumbs[_lbIdx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
}

function closeLb() {
  document.getElementById('lb').classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('lb').addEventListener('click', function(e) {
  if (e.target === this) closeLb();
});

document.addEventListener('keydown', e => {
  const lb = document.getElementById('lb');
  if (!lb.classList.contains('open')) return;
  if (e.key === 'Escape')     closeLb();
  if (e.key === 'ArrowLeft')  lbNav(-1);
  if (e.key === 'ArrowRight') lbNav(1);
});

function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

init();
</script>
@endsection
