@extends('layouts.app')
@section('title', 'Informasi RT 10 — Golden Park 2')
@section('styles')
<style>
.inf-hero{background:linear-gradient(135deg,#0c1e14 0%,#1A3D2B 55%,#2e6647 100%);padding:4rem 0 2.5rem}

/* ── Tabs ─────────────────────────────────────────────────────── */
.inf-tabs{background:#fff;border-bottom:1px solid var(--border);position:sticky;top:0;z-index:10}
.inf-tab-inner{display:flex;gap:0;overflow-x:auto;scrollbar-width:none}
.inf-tab-inner::-webkit-scrollbar{display:none}
.inf-tab{padding:14px 22px;font-size:13px;font-weight:600;color:var(--ink-soft);border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;white-space:nowrap;transition:all .15s;font-family:'DM Sans',sans-serif}
.inf-tab.active{color:var(--forest);border-bottom-color:var(--forest)}
.inf-tab:hover:not(.active){color:var(--ink-mid)}

/* ── Berita cards ─────────────────────────────────────────────── */
.berita-card{display:flex;gap:1.25rem;background:#fff;border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;transition:box-shadow .15s;cursor:pointer}
.berita-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08)}
.berita-cover{width:120px;height:90px;flex-shrink:0;overflow:hidden;border-radius:0}
.berita-cover img{width:100%;height:100%;object-fit:cover;display:block}
.berita-body{padding:1rem;flex:1;min-width:0}
.berita-chip{font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;display:inline-block;margin-bottom:6px}
.chip-pengumuman{background:#e8f5ec;color:#1A3D2B}
.chip-kegiatan  {background:#e4ecfd;color:#1A3060}
.chip-informasi {background:#fdf8e4;color:#5A4A00}
.chip-lainnya   {background:#f0ece8;color:#3A3030}
.berita-judul{font-family:'DM Serif Display',serif;font-size:1rem;color:var(--ink);line-height:1.35;margin-bottom:4px;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
.berita-meta{font-size:11px;color:var(--ink-mute)}
.berita-pinned{font-size:10px;font-weight:700;color:var(--gold);letter-spacing:.04em}

/* ── Tata Tertib ─────────────────────────────────────────────── */
.tt-group{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:1.25rem}
.tt-group-head{background:var(--forest);color:#fff;padding:.75rem 1.25rem;display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em}
.tt-item{border-bottom:1px solid var(--border);padding:1rem 1.25rem}
.tt-item:last-child{border-bottom:none}
.tt-pasal{font-size:10px;font-weight:700;color:var(--forest-light);text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px}
.tt-judul{font-weight:600;font-size:14px;color:var(--ink);margin-bottom:4px}
.tt-isi{font-size:13px;color:var(--ink-soft);line-height:1.65}

/* ── Program Kerja ───────────────────────────────────────────── */
.pk-year-sel{padding:8px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'DM Sans',sans-serif;outline:none;background:#fff;color:var(--ink)}
.pk-group{margin-bottom:2rem}
.pk-group-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--forest);margin-bottom:.75rem;display:flex;align-items:center;gap:8px}
.pk-group-title::after{content:'';flex:1;height:1px;background:var(--border)}
.pk-item{background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem 1.25rem;margin-bottom:.625rem;display:flex;align-items:flex-start;gap:1rem}
.pk-status{flex-shrink:0;padding:3px 10px;border-radius:99px;font-size:10px;font-weight:700;letter-spacing:.04em}
.pk-rencana {background:#f0ece8;color:#5A4A00}
.pk-berjalan{background:#e8f5ec;color:#1A3D2B}
.pk-selesai {background:#e4ecfd;color:#1A3060}
.pk-batal   {background:#fde8e4;color:#7A1A0A}
.pk-nama{font-weight:600;font-size:13px;color:var(--ink);margin-bottom:3px}
.pk-desc{font-size:12px;color:var(--ink-soft)}

/* ── Pengumuman ──────────────────────────────────────────────── */
.peng-item{background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem 1.25rem;margin-bottom:.75rem}
.peng-tgl{font-size:11px;color:var(--ink-mute);margin-bottom:4px}
.peng-judul{font-weight:600;font-size:14px;color:var(--ink);margin-bottom:5px}
.peng-isi{font-size:13px;color:var(--ink-soft);line-height:1.65;white-space:pre-line}


@media(max-width:640px){
  .berita-cover{width:80px}
  .berita-body{padding:.75rem}
  .inf-tab{padding:12px 16px;font-size:12px}
}
</style>
@endsection
@section('content')

<div class="inf-hero">
  <div class="container">
    <div class="fade-in">
      <div style="display:inline-flex;align-items:center;gap:8px;font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,160,48,.75);margin-bottom:.875rem">
        <span style="width:20px;height:2px;background:var(--gold);display:inline-block;border-radius:2px"></span>
        RT 10 Golden Park 2
      </div>
      <h1 style="font-family:'DM Serif Display',serif;font-size:clamp(2rem,4vw,2.7rem);color:#fff;margin:0 0 .5rem;line-height:1.1">Informasi RT</h1>
      <p style="font-size:14px;color:rgba(255,255,255,.5);margin:0">Berita, pengumuman, tata tertib, dan program kerja RT 10.</p>
    </div>
  </div>
</div>

<div class="inf-tabs">
  <div class="container">
    <div class="inf-tab-inner">
      <button class="inf-tab active" onclick="setTab('berita',this)"><i class="fa fa-newspaper" style="margin-right:6px"></i>Berita & Info</button>
      <button class="inf-tab" onclick="setTab('pengumuman',this)"><i class="fa fa-bullhorn" style="margin-right:6px"></i>Pengumuman</button>
      <button class="inf-tab" onclick="setTab('tata-tertib',this)"><i class="fa fa-scale-balanced" style="margin-right:6px"></i>Tata Tertib</button>
      <button class="inf-tab" onclick="setTab('program-kerja',this)"><i class="fa fa-list-check" style="margin-right:6px"></i>Program Kerja</button>
      <button class="inf-tab" onclick="setTab('saran',this)"><i class="fa fa-comments" style="margin-right:6px"></i>Saran &amp; Keluhan</button>
      <button class="inf-tab" onclick="setTab('usulan',this)"><i class="fa fa-lightbulb" style="margin-right:6px"></i>Usulan Pembangunan</button>
    </div>
  </div>
</div>

<main style="padding:2rem 0 5rem;background:var(--warm)">
<div class="container" style="max-width:780px">

  {{-- Berita: list view --}}
  <div id="tab-berita">
    <div id="berita-list" style="display:flex;flex-direction:column;gap:.875rem"></div>
    <div id="berita-empty" style="display:none;text-align:center;padding:3rem;background:#fff;border-radius:var(--radius);border:1px solid var(--border)">
      <i class="fa fa-newspaper" style="font-size:2rem;color:var(--ink-mute);display:block;margin-bottom:10px"></i>
      <div style="color:var(--ink-soft)">Belum ada berita atau informasi.</div>
    </div>
    {{-- Berita: inline detail view --}}
    <div id="berita-detail" style="display:none">
      <button onclick="backToList()" style="display:inline-flex;align-items:center;gap:7px;font-size:13px;font-weight:600;color:var(--forest);background:none;border:none;cursor:pointer;padding:0;margin-bottom:1.25rem">
        <i class="fa fa-arrow-left" style="font-size:11px"></i> Kembali ke daftar
      </button>
      <div style="background:#fff;border-radius:var(--radius);border:1px solid var(--border);overflow:hidden">
        <img id="bd-cover" src="" alt="" style="display:none;width:100%;max-height:260px;object-fit:cover">
        <div style="padding:1.75rem">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:.75rem;flex-wrap:wrap">
            <span id="bd-chip" class="berita-chip"></span>
            <span id="bd-pinned" style="display:none;font-size:10px;font-weight:700;color:var(--gold)"><i class="fa fa-thumbtack" style="font-size:9px"></i> Disematkan</span>
          </div>
          <h2 id="bd-judul" style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--forest);line-height:1.3;margin:0 0 .5rem"></h2>
          <div id="bd-meta" style="font-size:12px;color:var(--ink-mute);margin-bottom:1.5rem;padding-bottom:1.25rem;border-bottom:1px solid var(--border)"></div>
          <div id="bd-isi" style="font-size:14px;color:var(--ink-soft);line-height:1.85;white-space:pre-wrap"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Pengumuman --}}
  <div id="tab-pengumuman" style="display:none">
    <div id="peng-list"></div>
    <div id="peng-empty" style="display:none;text-align:center;padding:3rem;background:#fff;border-radius:var(--radius);border:1px solid var(--border)">
      <i class="fa fa-bullhorn" style="font-size:2rem;color:var(--ink-mute);display:block;margin-bottom:10px"></i>
      <div style="color:var(--ink-soft)">Belum ada pengumuman.</div>
    </div>
  </div>

  {{-- Tata Tertib --}}
  <div id="tab-tata-tertib" style="display:none">
    <div id="tt-list"></div>
    <div id="tt-empty" style="display:none;text-align:center;padding:3rem;background:#fff;border-radius:var(--radius);border:1px solid var(--border)">
      <i class="fa fa-scale-balanced" style="font-size:2rem;color:var(--ink-mute);display:block;margin-bottom:10px"></i>
      <div style="color:var(--ink-soft)">Belum ada tata tertib yang dipublikasikan.</div>
    </div>
  </div>

  {{-- Program Kerja --}}
  <div id="tab-program-kerja" style="display:none">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.25rem">
      <i class="fa fa-calendar-alt" style="color:var(--forest)"></i>
      <span style="font-size:13px;font-weight:600;color:var(--ink-mid)">Tahun</span>
      <select id="pk-year" onchange="loadPk()" class="pk-year-sel"></select>
    </div>
    <div id="pk-list"></div>
    <div id="pk-empty" style="display:none;text-align:center;padding:3rem;background:#fff;border-radius:var(--radius);border:1px solid var(--border)">
      <i class="fa fa-list-check" style="font-size:2rem;color:var(--ink-mute);display:block;margin-bottom:10px"></i>
      <div style="color:var(--ink-soft)">Belum ada program kerja untuk tahun ini.</div>
    </div>
  </div>

  {{-- Saran & Keluhan --}}
  <div id="tab-saran" style="display:none">

    {{-- Submission form --}}
    <div class="card" style="margin-bottom:1.5rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.1rem;color:var(--forest);margin-bottom:.25rem">Kirim Saran atau Keluhan</div>
      <div style="font-size:13px;color:var(--ink-soft);margin-bottom:1.25rem">Pesan kamu akan dibaca dan ditanggapi oleh pengurus RT.</div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.75rem">
        <div>
          <label style="font-size:11px;font-weight:600;color:var(--ink-mid);display:block;margin-bottom:.3rem">Kategori</label>
          <select id="sk-kategori" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--ink)">
            <option value="saran">Saran</option>
            <option value="keluhan">Keluhan</option>
            <option value="pertanyaan">Pertanyaan</option>
          </select>
        </div>
        <div id="sk-nama-wrap">
          <label style="font-size:11px;font-weight:600;color:var(--ink-mid);display:block;margin-bottom:.3rem">Nama Pengirim</label>
          <input id="sk-nama" type="text" placeholder="{{ auth()->check() ? auth()->user()->nama : 'Nama kamu' }}" value="{{ auth()->check() ? auth()->user()->nama : '' }}"
            style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--ink)">
        </div>
      </div>

      <div style="margin-bottom:.75rem">
        <label style="font-size:11px;font-weight:600;color:var(--ink-mid);display:block;margin-bottom:.3rem">Judul</label>
        <input id="sk-judul" type="text" placeholder="Ringkasan singkat pesanmu"
          style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--ink)">
      </div>

      <div style="margin-bottom:.75rem">
        <label style="font-size:11px;font-weight:600;color:var(--ink-mid);display:block;margin-bottom:.3rem">Pesan</label>
        <textarea id="sk-isi" rows="4" placeholder="Tulis pesan selengkapnya di sini..."
          style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--ink);resize:vertical"></textarea>
      </div>

      <div style="margin-bottom:.75rem">
        <label style="font-size:11px;font-weight:600;color:var(--ink-mid);display:block;margin-bottom:.3rem">Nomor WhatsApp <span style="font-weight:400;color:var(--ink-mute)">(opsional, agar bisa dihubungi)</span></label>
        <input id="sk-wa" type="tel" placeholder="08123456789"
          style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:13px;font-family:'DM Sans',sans-serif;color:var(--ink)">
      </div>

      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;cursor:pointer" onclick="toggleSkAnonym()">
        <div id="sk-anonym-toggle" data-checked="0"
          style="width:36px;height:20px;border-radius:99px;background:#e5e5e5;border:1.5px solid var(--border);display:flex;align-items:center;padding:2px;transition:.2s;flex-shrink:0">
          <div id="sk-anonym-knob" style="width:14px;height:14px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .2s"></div>
        </div>
        <span style="font-size:13px;color:var(--ink-mid)">Kirim sebagai anonim</span>
      </div>

      <button onclick="submitSaran()" id="sk-submit-btn" class="btn-primary" style="width:100%;justify-content:center">
        <i class="fa fa-paper-plane"></i> Kirim Pesan
      </button>
      <div id="sk-msg" style="display:none;margin-top:.75rem;padding:.75rem 1rem;border-radius:8px;font-size:13px"></div>
    </div>

    {{-- Own submissions (if logged in) --}}
    @auth
    <div id="sk-mine-wrap">
      <div style="font-size:13px;font-weight:600;color:var(--ink-mid);margin-bottom:.75rem">Pesan Saya</div>
      <div id="sk-mine-list">
        <div style="text-align:center;padding:2rem;color:var(--ink-soft)">Memuat...</div>
      </div>
    </div>
    @endauth

  </div>

  {{-- Usulan Pembangunan --}}
  <div id="tab-usulan" style="display:none">

    <div class="card" style="margin-bottom:1.5rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.1rem;color:var(--forest);margin-bottom:.25rem">Kirim Usulan Pembangunan</div>
      <div style="font-size:13px;color:var(--ink-soft);margin-bottom:1.25rem">Sampaikan ide atau usulan untuk perbaikan dan pengembangan lingkungan RT 10.</div>

      @guest
      <div style="background:var(--gold-pale);border-radius:10px;padding:1rem 1.25rem;font-size:13px;color:var(--gold-dark)">
        <i class="fa fa-lock" style="margin-right:6px"></i>
        <a href="/login" style="color:var(--gold-dark);font-weight:600">Masuk</a> terlebih dahulu untuk mengirim usulan.
      </div>
      @endguest

      @auth
      <div style="margin-bottom:.75rem">
        <label style="font-size:12px;font-weight:600;color:var(--ink-mid);display:block;margin-bottom:.3rem">Judul Usulan <span style="color:var(--rust)">*</span></label>
        <input id="up-judul" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'DM Sans',sans-serif;color:var(--ink);background:#fff" placeholder="Singkat dan jelas, contoh: Perbaikan Lampu Jalan Blok C">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.75rem">
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-mid);display:block;margin-bottom:.3rem">Lokasi</label>
          <input id="up-lokasi" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'DM Sans',sans-serif;color:var(--ink);background:#fff" placeholder="Blok, jalan, atau area">
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--ink-mid);display:block;margin-bottom:.3rem">Prioritas</label>
          <select id="up-prioritas" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'DM Sans',sans-serif;color:var(--ink);background:#fff;cursor:pointer">
            <option value="rendah">Rendah</option>
            <option value="sedang" selected>Sedang</option>
            <option value="tinggi">Tinggi</option>
          </select>
        </div>
      </div>
      <div style="margin-bottom:.75rem">
        <label style="font-size:12px;font-weight:600;color:var(--ink-mid);display:block;margin-bottom:.3rem">Deskripsi <span style="color:var(--rust)">*</span></label>
        <textarea id="up-deskripsi" rows="4" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'DM Sans',sans-serif;color:var(--ink);background:#fff;resize:vertical" placeholder="Jelaskan usulan secara detail: masalah yang ada, solusi yang diusulkan, manfaatnya..."></textarea>
      </div>
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1rem">
        <input type="checkbox" id="up-anonym" style="accent-color:var(--forest);width:15px;height:15px">
        <label for="up-anonym" style="font-size:13px;color:var(--ink-soft);cursor:pointer">Kirim sebagai anonim</label>
      </div>

      <div id="up-msg" style="display:none;font-size:13px;padding:10px 14px;border-radius:8px;margin-bottom:1rem"></div>

      <button id="up-submit-btn" onclick="submitUsulan()" style="background:var(--forest);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 28px;font-size:14px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;width:100%">
        <i class="fa fa-paper-plane"></i> Kirim Usulan
      </button>
      @endauth
    </div>

    @auth
    <div class="card">
      <div style="font-size:14px;font-weight:600;color:var(--ink-mid);margin-bottom:1rem">Usulan Saya</div>
      <div id="up-mine-list"><div style="text-align:center;padding:2rem;color:var(--ink-soft)">Memuat...</div></div>
    </div>
    @endauth

  </div>

</div>
</main>


@endsection
@section('scripts')
<script>
const BULAN_S = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
const KAT_LBL = { pengumuman:'Pengumuman', kegiatan:'Kegiatan', informasi:'Informasi', lainnya:'Lainnya' };
const STATUS_LBL = { rencana:'Rencana', berjalan:'Berjalan', selesai:'Selesai', batal:'Batal' };
let _curTab = 'berita', _loaded = {};

function fmtTgl(d) {
  const dt = new Date(d.slice(0,10) + 'T00:00:00');
  return `${dt.getDate()} ${BULAN_S[dt.getMonth()]} ${dt.getFullYear()}`;
}
function esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function setTab(tab, btn, pushState = true) {
  document.querySelectorAll('.inf-tab').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  else document.querySelectorAll('.inf-tab').forEach(b => {
    if (b.getAttribute('onclick')?.includes(`'${tab}'`)) b.classList.add('active');
  });
  ['berita','pengumuman','tata-tertib','program-kerja','saran','usulan'].forEach(t => {
    document.getElementById('tab-' + t).style.display = t === tab ? '' : 'none';
  });
  _curTab = tab;
  if (pushState) history.replaceState(null, '', '#' + tab);
  if (!_loaded[tab]) loadTab(tab);
}

async function loadTab(tab) {
  _loaded[tab] = true;
  if (tab === 'berita')        await loadBerita();
  if (tab === 'pengumuman')    await loadPengumuman();
  if (tab === 'tata-tertib')   await loadTataTertib();
  if (tab === 'program-kerja') await initPk();
  if (tab === 'saran')         await loadSkMine();
  if (tab === 'usulan')        await loadUsulanMine();
}

// Category placeholder: bg, accent color, inline SVG path
const KAT_PLACEHOLDER = {
  informasi: {
    bg: '#e8f5ec', color: '#2e6647',
    svg: '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>',
  },
  kegiatan: {
    bg: '#e4ecfd', color: '#1A3060',
    svg: '<path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>',
  },
  pengumuman: {
    bg: '#fdf8e4', color: '#7A5C00',
    svg: '<path d="M18 11v2h4v-2h-4zm-2 6.61c.96.71 2.21 1.65 3.2 2.39.4-.53.8-1.07 1.2-1.6-.99-.74-2.24-1.68-3.2-2.4-.4.54-.8 1.08-1.2 1.61zM20.4 5.6c-.4-.53-.8-1.07-1.2-1.6-.99.74-2.24 1.68-3.2 2.4.4.53.8 1.07 1.2 1.6.96-.72 2.21-1.65 3.2-2.4zM4 9c-1.1 0-2 .9-2 2v2c0 1.1.9 2 2 2h1v4h2v-4h1l5 3V6L8 9H4zm11.5 3c0-1.33-.58-2.53-1.5-3.35v6.69c.92-.81 1.5-2.01 1.5-3.34z"/>',
  },
  lainnya: {
    bg: '#f0ece8', color: '#5A4A40',
    svg: '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
  },
};

function beritaPlaceholder(kat) {
  const p = KAT_PLACEHOLDER[kat] || KAT_PLACEHOLDER.lainnya;
  return `<div style="width:100%;height:100%;background:${p.bg};display:flex;align-items:center;justify-content:center">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${p.color}" style="width:36px;height:36px;opacity:.6">${p.svg}</svg>
  </div>`;
}

// ── Berita ─────────────────────────────────────────────────────
async function loadBerita() {
  const r = await fetch('/api/berita');
  const j = await r.json();
  const list = j.success ? j.data : [];
  const el   = document.getElementById('berita-list');
  const emp  = document.getElementById('berita-empty');
  if (!list.length) { el.innerHTML = ''; emp.style.display = ''; return; }
  emp.style.display = 'none';
  el.innerHTML = list.map(b => `
    <div class="berita-card" onclick="openBd(${b.id})">
      <div class="berita-cover">
        ${b.cover_url
          ? `<img src="${esc(b.cover_url)}" alt="" loading="lazy">`
          : beritaPlaceholder(b.kategori)}
      </div>
      <div class="berita-body">
        ${b.is_pinned ? `<div class="berita-pinned" style="display:flex;align-items:center;gap:4px;margin-bottom:4px"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:10px;height:10px"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg> Disematkan</div>` : ''}
        <span class="berita-chip chip-${b.kategori}">${KAT_LBL[b.kategori]||b.kategori}</span>
        <div class="berita-judul">${esc(b.judul)}</div>
        <div class="berita-meta">
          ${b.created_by ? esc(b.created_by.nama||'Admin') + ' &middot; ' : ''}
          ${fmtTgl(b.created_at)}
        </div>
      </div>
    </div>`).join('');
}

let _beritaCache = {};
async function openBd(id) {
  if (!_beritaCache[id]) {
    const r = await fetch('/api/berita/' + id);
    const j = await r.json();
    if (!j.success) return;
    _beritaCache[id] = j.data;
  }
  const b = _beritaCache[id];

  const chip = document.getElementById('bd-chip');
  chip.textContent = KAT_LBL[b.kategori] || b.kategori;
  chip.className   = 'berita-chip chip-' + b.kategori;

  const cover = document.getElementById('bd-cover');
  if (b.cover_url) { cover.src = b.cover_url; cover.style.display = ''; }
  else              { cover.style.display = 'none'; }

  document.getElementById('bd-pinned').style.display = b.is_pinned ? '' : 'none';
  document.getElementById('bd-judul').textContent = b.judul;
  document.getElementById('bd-meta').textContent  =
    (b.created_by ? b.created_by.nama + ' · ' : '') + fmtTgl(b.created_at);
  document.getElementById('bd-isi').textContent = b.isi;

  document.getElementById('berita-list').style.display  = 'none';
  document.getElementById('berita-empty').style.display = 'none';
  document.getElementById('berita-detail').style.display = '';
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function backToList() {
  document.getElementById('berita-detail').style.display = 'none';
  document.getElementById('berita-list').style.display   = '';
  // re-show empty state if needed
  const cards = document.querySelectorAll('#berita-list > *');
  if (!cards.length) document.getElementById('berita-empty').style.display = '';
}

// ── Pengumuman ─────────────────────────────────────────────────
async function loadPengumuman() {
  const r = await fetch('/api/pengumuman');
  const j = await r.json();
  const list = j.success ? j.data : [];
  const el   = document.getElementById('peng-list');
  const emp  = document.getElementById('peng-empty');
  if (!list.length) { el.innerHTML = ''; emp.style.display = ''; return; }
  emp.style.display = 'none';
  el.innerHTML = list.map(p => `
    <div class="peng-item">
      <div class="peng-tgl">${fmtTgl(p.created_at)}</div>
      <div class="peng-judul">${esc(p.judul)}</div>
      <div class="peng-isi">${esc(p.isi)}</div>
    </div>`).join('');
}

// ── Tata Tertib ────────────────────────────────────────────────
async function loadTataTertib() {
  const r = await fetch('/api/tata-tertib');
  const j = await r.json();
  const list = j.success ? j.data : [];
  const el   = document.getElementById('tt-list');
  const emp  = document.getElementById('tt-empty');
  if (!list.length) { el.innerHTML = ''; emp.style.display = ''; return; }
  emp.style.display = 'none';

  // Group by kategori
  const groups = {};
  list.forEach(r => {
    if (!groups[r.kategori]) groups[r.kategori] = [];
    groups[r.kategori].push(r);
  });

  el.innerHTML = Object.entries(groups).map(([kat, items]) => `
    <div class="tt-group">
      <div class="tt-group-head">
        <i class="fa fa-gavel"></i> ${esc(kat)}
      </div>
      ${items.map(item => `
        <div class="tt-item">
          ${item.pasal ? `<div class="tt-pasal">${esc(item.pasal)}</div>` : ''}
          <div class="tt-judul">${esc(item.judul)}</div>
          <div class="tt-isi">${esc(item.isi)}</div>
        </div>`).join('')}
    </div>`).join('');
}

// ── Program Kerja ──────────────────────────────────────────────
async function initPk() {
  const r = await fetch('/api/program-kerja');
  const j = await r.json();
  const sel = document.getElementById('pk-year');
  const years = j.tahun_list || [new Date().getFullYear()];
  sel.innerHTML = years.map(y => `<option value="${y}"${y==j.tahun?' selected':''}>${y}</option>`).join('');
  renderPk(j.data || [], j.tahun);
}

async function loadPk() {
  const tahun = document.getElementById('pk-year').value;
  const r = await fetch('/api/program-kerja?tahun=' + tahun);
  const j = await r.json();
  renderPk(j.data || [], tahun);
}

function renderPk(list, tahun) {
  const el  = document.getElementById('pk-list');
  const emp = document.getElementById('pk-empty');
  if (!list.length) { el.innerHTML = ''; emp.style.display = ''; return; }
  emp.style.display = 'none';

  const groups = {};
  list.forEach(p => {
    if (!groups[p.bidang]) groups[p.bidang] = [];
    groups[p.bidang].push(p);
  });

  el.innerHTML = Object.entries(groups).map(([bidang, items]) => `
    <div class="pk-group">
      <div class="pk-group-title"><i class="fa fa-folder-open"></i> ${esc(bidang)}</div>
      ${items.map(p => `
        <div class="pk-item">
          <span class="pk-status pk-${p.status}">${STATUS_LBL[p.status]||p.status}</span>
          <div>
            <div class="pk-nama">${esc(p.nama)}</div>
            ${p.deskripsi ? `<div class="pk-desc">${esc(p.deskripsi)}</div>` : ''}
          </div>
        </div>`).join('')}
    </div>`).join('');
}

// ── Saran & Keluhan ───────────────────────────────────────────
const _isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
let _skAnonym = false;

function toggleSkAnonym() {
  _skAnonym = !_skAnonym;
  const tog  = document.getElementById('sk-anonym-toggle');
  const knob = document.getElementById('sk-anonym-knob');
  const nameWrap = document.getElementById('sk-nama-wrap');
  tog.style.background  = _skAnonym ? 'var(--forest)' : '#e5e5e5';
  tog.style.borderColor = _skAnonym ? 'var(--forest)' : 'var(--border)';
  knob.style.transform  = _skAnonym ? 'translateX(16px)' : 'translateX(0)';
  nameWrap.style.opacity = _skAnonym ? '.4' : '1';
}

async function submitSaran() {
  const btn   = document.getElementById('sk-submit-btn');
  const msgEl = document.getElementById('sk-msg');
  const judul = document.getElementById('sk-judul').value.trim();
  const isi   = document.getElementById('sk-isi').value.trim();
  const nama  = document.getElementById('sk-nama').value.trim();
  const wa    = document.getElementById('sk-wa').value.trim();
  const kat   = document.getElementById('sk-kategori').value;

  if (judul.length < 3) { showSkMsg('Judul minimal 3 karakter.', false); return; }
  if (isi.length < 10)  { showSkMsg('Isi pesan minimal 10 karakter.', false); return; }
  if (!_isLoggedIn && !_skAnonym && nama.length < 2) {
    showSkMsg('Masukkan nama pengirim atau aktifkan anonim.', false); return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengirim...';

  const r = await fetch('/api/saran-keluhan', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken },
    body: JSON.stringify({ judul, isi, kategori: kat, nama, no_wa: wa, is_anonym: _skAnonym ? 1 : 0 }),
  });
  const j = await r.json();

  btn.disabled = false;
  btn.innerHTML = '<i class="fa fa-paper-plane"></i> Kirim Pesan';

  if (j.success) {
    showSkMsg(j.message, true);
    document.getElementById('sk-judul').value = '';
    document.getElementById('sk-isi').value   = '';
    document.getElementById('sk-wa').value    = '';
    _loaded['saran'] = false;
    if (_isLoggedIn) loadSkMine();
  } else {
    showSkMsg(j.message || 'Terjadi kesalahan.', false);
  }
}

function showSkMsg(msg, ok) {
  const el = document.getElementById('sk-msg');
  el.style.display    = '';
  el.style.background = ok ? '#e8f5ec' : '#fdecea';
  el.style.color      = ok ? 'var(--forest)' : 'var(--rust)';
  el.textContent      = msg;
}

const SK_KAT_LBL    = { saran:'Saran', keluhan:'Keluhan', pertanyaan:'Pertanyaan' };
const SK_STATUS_LBL = { baru:'Baru', dibaca:'Dibaca', diproses:'Diproses', selesai:'Selesai' };
const SK_STATUS_COLOR = { baru:'var(--rust)', dibaca:'var(--gold-dark)', diproses:'var(--forest-light)', selesai:'var(--ink-mute)' };

async function loadSkMine() {
  const wrap = document.getElementById('sk-mine-wrap');
  const el   = document.getElementById('sk-mine-list');
  if (!wrap || !el) return;
  const r = await fetch('/api/saran-keluhan/mine');
  const j = await r.json();
  if (!j.success || !j.data.length) {
    el.innerHTML = '<div style="text-align:center;padding:1.5rem;color:var(--ink-soft);font-size:13px">Belum ada pesan yang kamu kirim.</div>';
    return;
  }
  el.innerHTML = j.data.map(s => {
    const dt   = new Date((s.created_at||'').replace(' ','T'));
    const tgl  = isNaN(dt) ? '' : dt.toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
    const color = SK_STATUS_COLOR[s.status] || 'var(--ink-mute)';
    return `<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:.875rem 1rem;margin-bottom:.5rem">
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.35rem;flex-wrap:wrap">
        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:var(--forest-pale);color:var(--forest)">${SK_KAT_LBL[s.kategori]||s.kategori}</span>
        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:${color}18;color:${color}">${SK_STATUS_LBL[s.status]||s.status}</span>
        <span style="font-size:11px;color:var(--ink-mute);margin-left:auto">${tgl}</span>
      </div>
      <div style="font-size:13px;font-weight:600;color:var(--ink);margin-bottom:.2rem">${esc(s.judul)}</div>
      ${s.tanggapan ? `
        <div style="margin-top:.625rem;background:var(--forest-pale);border-radius:6px;padding:.625rem .875rem">
          <div style="font-size:10px;font-weight:700;color:var(--forest);margin-bottom:.2rem"><i class="fa fa-reply" style="font-size:9px"></i> Tanggapan Pengurus</div>
          <div style="font-size:12.5px;color:var(--forest);white-space:pre-line;line-height:1.6">${esc(s.tanggapan)}</div>
        </div>` : ''}
    </div>`;
  }).join('');
}

// ── Usulan Pembangunan ────────────────────────────────────────
async function submitUsulan() {
  const judul     = document.getElementById('up-judul')?.value.trim();
  const deskripsi = document.getElementById('up-deskripsi')?.value.trim();
  const lokasi    = document.getElementById('up-lokasi')?.value.trim();
  const prioritas = document.getElementById('up-prioritas')?.value;
  const isAnonym  = document.getElementById('up-anonym')?.checked;
  const msg       = document.getElementById('up-msg');

  if (!judul || judul.length < 3)     { showUpMsg('Judul wajib diisi (min. 3 karakter).', false); return; }
  if (!deskripsi || deskripsi.length < 10) { showUpMsg('Deskripsi wajib diisi (min. 10 karakter).', false); return; }

  const btn = document.getElementById('up-submit-btn');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengirim...';

  const r = await fetch('/api/usulan', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken },
    body: JSON.stringify({ judul, deskripsi, lokasi: lokasi || null, prioritas, is_anonym: isAnonym }),
  });
  const j = await r.json();
  btn.disabled = false; btn.innerHTML = '<i class="fa fa-paper-plane"></i> Kirim Usulan';

  if (j.success) {
    showUpMsg('Usulan berhasil dikirim! Pengurus RT akan meninjaunya.', true);
    document.getElementById('up-judul').value = '';
    document.getElementById('up-lokasi').value = '';
    document.getElementById('up-deskripsi').value = '';
    document.getElementById('up-anonym').checked = false;
    _loaded['usulan'] = false;
    loadUsulanMine();
  } else {
    showUpMsg(j.message || 'Gagal mengirim usulan.', false);
  }
}

function showUpMsg(text, ok) {
  const el = document.getElementById('up-msg');
  if (!el) return;
  el.textContent = text; el.style.display = '';
  el.style.background = ok ? '#E8F4ED' : '#FDECEA';
  el.style.color = ok ? 'var(--forest)' : 'var(--rust)';
  if (ok) setTimeout(() => el.style.display = 'none', 5000);
}

async function loadUsulanMine() {
  const wrap = document.getElementById('up-mine-list');
  if (!wrap) return;
  const r = await fetch('/api/usulan/mine');
  if (!r.ok) { wrap.innerHTML = ''; return; }
  const j = await r.json();
  if (!j.success || !j.data.length) {
    wrap.innerHTML = '<div style="text-align:center;padding:1.5rem;color:var(--ink-soft);font-size:13px">Belum ada usulan yang dikirim.</div>';
    return;
  }
  const pLabel = { rendah:'Rendah', sedang:'Sedang', tinggi:'Tinggi' };
  const sLabel = { baru:'Baru', dikaji:'Dikaji', disetujui:'Disetujui', ditolak:'Ditolak', selesai:'Selesai' };
  const sBg    = { baru:'#fdecea', dikaji:'var(--gold-pale)', disetujui:'var(--forest-pale)', ditolak:'#efefed', selesai:'#e8f0fd' };
  const sClr   = { baru:'var(--rust)', dikaji:'var(--gold-dark)', disetujui:'var(--forest)', ditolak:'var(--ink-soft)', selesai:'#2d5aa8' };
  wrap.innerHTML = j.data.map(u => `
    <div style="padding:12px 0;border-bottom:1px solid var(--border)">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem">
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;font-size:14px;color:var(--ink)">${esc(u.judul)}</div>
          <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">${fmtTgl(u.created_at)}${u.lokasi ? ' · '+esc(u.lokasi) : ''}</div>
          ${u.tanggapan ? `<div style="font-size:12px;color:var(--forest);margin-top:6px;padding:7px 10px;background:var(--forest-pale);border-radius:6px">${esc(u.tanggapan)}</div>` : ''}
        </div>
        <span style="font-size:10px;font-weight:700;padding:2px 9px;border-radius:99px;flex-shrink:0;background:${sBg[u.status]};color:${sClr[u.status]}">${sLabel[u.status]||u.status}</span>
      </div>
    </div>
  `).join('');
}

// ── Init: restore tab from URL hash ───────────────────────────
const TABS = ['berita','pengumuman','tata-tertib','program-kerja','saran','usulan'];
const initTab = TABS.includes(location.hash.slice(1)) ? location.hash.slice(1) : 'berita';
setTab(initTab, null, false);

window.addEventListener('popstate', () => {
  const t = TABS.includes(location.hash.slice(1)) ? location.hash.slice(1) : 'berita';
  setTab(t, null, false);
});
</script>
@endsection
