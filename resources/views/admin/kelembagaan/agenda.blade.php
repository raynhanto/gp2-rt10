@extends('layouts.admin')
@section('title', 'Agenda RT')
@section('content')
<div class="container" style="max-width:1100px">
  <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <div class="section-label">Kelembagaan</div>
      <div class="section-title">Agenda RT</div>
    </div>
    <button onclick="openForm()" class="btn-primary">+ Tambah Agenda</button>
  </div>

  {{-- Category tabs --}}
  <div id="cat-tabs" style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:1.25rem">
    @foreach([''=>'Semua','rapat'=>'Rapat','kegiatan'=>'Kegiatan','sosial'=>'Sosial','olahraga'=>'Olahraga','lainnya'=>'Lainnya'] as $val=>$lbl)
    <button onclick="setKat('{{ $val }}')" data-kat="{{ $val }}" class="cat-tab {{ $val==='' ? 'active' : '' }}">{{ $lbl }}</button>
    @endforeach
  </div>

  {{-- Layout: Calendar left, list right --}}
  <div style="display:grid;grid-template-columns:340px 1fr;gap:1.5rem;align-items:start">

    {{-- Calendar --}}
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1.25rem;position:sticky;top:80px">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
        <button onclick="changeMonth(-1)" style="background:none;border:none;cursor:pointer;padding:4px 8px;font-size:18px;color:var(--ink-mid)">‹</button>
        <div style="font-weight:600;font-size:15px;color:var(--forest)" id="cal-title"></div>
        <button onclick="changeMonth(1)" style="background:none;border:none;cursor:pointer;padding:4px 8px;font-size:18px;color:var(--ink-mid)">›</button>
      </div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:4px">
        @foreach(['M','S','S','R','K','J','S'] as $h)
        <div style="text-align:center;font-size:10px;font-weight:700;color:var(--ink-mute);padding-bottom:4px">{{ $h }}</div>
        @endforeach
      </div>
      <div id="cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px"></div>

      {{-- Legend --}}
      <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;padding-top:10px;border-top:1px solid var(--border)">
        @foreach(['rapat'=>'Rapat','kegiatan'=>'Kegiatan','sosial'=>'Sosial','olahraga'=>'Olahraga','lainnya'=>'Lainnya'] as $k=>$l)
        <span style="display:flex;align-items:center;gap:4px;font-size:10px;color:var(--ink-mute)">
          <span style="width:8px;height:8px;border-radius:50%;background:var(--dot-{{ $k }})"></span>{{ $l }}
        </span>
        @endforeach
      </div>
    </div>

    {{-- Event List --}}
    <div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
        <div id="list-label" style="font-size:13px;color:var(--ink-soft)">Semua agenda bulan ini</div>
        <select id="f-filter-time" onchange="renderList()" style="padding:7px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none">
          <option value="">Semua</option>
          <option value="mendatang" selected>Akan Datang</option>
          <option value="lalu">Sudah Berlalu</option>
        </select>
      </div>
      <div id="agenda-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
    </div>

  </div>
</div>

{{-- Form Modal --}}
<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:min(560px,calc(100vw - 2rem));max-height:90vh;overflow-y:auto">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem" id="form-title">Tambah Agenda</div>
    <input type="hidden" id="f-id">

    <div style="margin-bottom:1rem">
      <label class="flabel">Judul *</label>
      <input type="text" id="f-judul" placeholder="Cth: Rapat RT Bulan Juli" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Tanggal Mulai *</label>
        <input type="date" id="f-tanggal" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Kategori</label>
        <select id="f-kategori-form" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="rapat">Rapat</option>
          <option value="kegiatan">Kegiatan</option>
          <option value="sosial">Sosial</option>
          <option value="olahraga">Olahraga</option>
          <option value="lainnya">Lainnya</option>
        </select>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Waktu Mulai</label>
        <input type="time" id="f-waktu-mulai" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Waktu Selesai</label>
        <input type="time" id="f-waktu-selesai" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
    </div>

    <div style="margin-bottom:1rem">
      <label class="flabel">Lokasi</label>
      <input type="text" id="f-lokasi" placeholder="Cth: Balai RT, Lapangan Blok A" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
    </div>

    {{-- Recurrence --}}
    <div style="background:#F7F7F5;border-radius:var(--radius-sm);padding:1rem;margin-bottom:1rem">
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:10px">🔁 Pengulangan</div>
      <select id="f-jenis-jadwal" onchange="onJenisChange()" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff;margin-bottom:10px">
        <option value="sekali">Sekali (tanggal tertentu)</option>
        <option value="mingguan">Setiap minggu (hari tertentu)</option>
        <option value="bulanan">Setiap bulan</option>
        <option value="dua_bulanan">Setiap 2 bulan</option>
      </select>
      <div id="section-hari" style="display:none;margin-bottom:10px">
        <label class="flabel">Pilih Hari</label>
        <div style="display:flex;gap:5px;flex-wrap:wrap">
          @foreach(['Min'=>0,'Sen'=>1,'Sel'=>2,'Rab'=>3,'Kam'=>4,'Jum'=>5,'Sab'=>6] as $label=>$val)
          <label class="hari-pill">
            <input type="checkbox" class="cb-hari" value="{{ $val }}"> {{ $label }}
          </label>
          @endforeach
        </div>
      </div>
      <div id="section-akhir" style="display:none">
        <label class="flabel">Berakhir Pada (opsional)</label>
        <input type="date" id="f-tanggal-akhir" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
    </div>

    {{-- Target --}}
    <div style="background:#F7F7F5;border-radius:var(--radius-sm);padding:1rem;margin-bottom:1rem">
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:10px">👥 Target Peserta</div>
      <select id="f-target" onchange="onTargetChange()" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff;margin-bottom:8px">
        <option value="semua">Semua Warga (tampil di kalender publik)</option>
        <option value="staff">Staff RT saja</option>
        <option value="personal">Pilih Pengguna Tertentu</option>
      </select>
      <div id="section-personal" style="display:none">
        <input type="text" id="user-search" oninput="filterUsers()" placeholder="Cari nama atau email..." style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none;margin-bottom:6px;box-sizing:border-box">
        <div style="border:1.5px solid var(--border);border-radius:var(--radius-sm);max-height:150px;overflow-y:auto;background:#fff">
          <div id="user-list-inner"></div>
        </div>
        <div style="font-size:11px;color:var(--ink-mute);margin-top:4px" id="selected-count">0 pengguna dipilih</div>
      </div>
    </div>

    {{-- Notifications --}}
    <div style="background:#F7F7F5;border-radius:var(--radius-sm);padding:1rem;margin-bottom:1.25rem">
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:10px">📬 Kirim Undangan</div>
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;margin-bottom:8px">
        <input type="checkbox" id="f-kirim-wa" style="accent-color:var(--forest);width:15px;height:15px">
        <span>📱 WhatsApp (Fonnte)</span>
      </label>
      <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
        <input type="checkbox" id="f-kirim-email" style="accent-color:var(--forest);width:15px;height:15px">
        <span>✉️ Email</span>
      </label>
    </div>

    <div style="margin-bottom:1.5rem">
      <label class="flabel">Deskripsi</label>
      <textarea id="f-deskripsi" rows="3" placeholder="Keterangan tambahan..." style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;resize:vertical;font-family:'DM Sans',sans-serif;box-sizing:border-box"></textarea>
    </div>

    <div style="display:flex;gap:8px">
      <button onclick="save()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeForm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

<style>
:root {
  --dot-rapat:#1a3d2b; --dot-kegiatan:#856404; --dot-sosial:#C0392B;
  --dot-olahraga:#1A5CA8; --dot-lainnya:#888;
}
.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}
.cat-tab{padding:6px 14px;border:1.5px solid var(--border);border-radius:99px;font-size:12px;font-weight:500;background:#fff;cursor:pointer;color:var(--ink-mid);transition:all .15s}
.cat-tab.active{background:var(--forest);border-color:var(--forest);color:#fff}
.kat-badge{font-size:10px;font-weight:600;padding:2px 9px;border-radius:99px;text-transform:capitalize}
.kat-rapat{background:#EBF3EE;color:var(--forest)}
.kat-kegiatan{background:#FBF3DC;color:var(--gold-dark)}
.kat-sosial{background:#FDECEA;color:var(--rust)}
.kat-olahraga{background:#EAF4FF;color:#1A5CA8}
.kat-lainnya{background:#EFEFED;color:var(--ink-soft)}
.agenda-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem;margin-bottom:.6rem;display:flex;gap:.9rem;align-items:flex-start}
.agenda-date-box{flex-shrink:0;width:46px;text-align:center;background:var(--forest-pale);border-radius:var(--radius-xs);padding:5px 3px}
.agenda-date-day{font-family:'DM Serif Display',serif;font-size:1.4rem;color:var(--forest);line-height:1}
.agenda-date-month{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--forest-light)}
.act-btn{font-size:11px;padding:4px 8px;border-radius:5px;cursor:pointer;border:1px solid;transition:opacity .15s}
.act-btn:hover{opacity:.7}
.hari-pill{display:flex;align-items:center;gap:4px;cursor:pointer;font-size:12px;padding:4px 9px;border:1.5px solid var(--border);border-radius:99px;background:#fff;user-select:none;transition:all .15s}
.hari-pill:has(.cb-hari:checked){background:#EBF3EE;border-color:var(--forest);color:var(--forest)}
.cal-day{aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding-top:4px;border-radius:6px;cursor:pointer;font-size:12px;color:var(--ink-mid);transition:background .12s;position:relative}
.cal-day:hover{background:#f0f0ee}
.cal-day.today{font-weight:700;color:var(--forest)}
.cal-day.today .cal-dn{background:var(--forest);color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center}
.cal-day.selected{background:#EBF3EE}
.cal-day.other-month .cal-dn{color:var(--ink-mute)}
.cal-dots{display:flex;gap:2px;flex-wrap:wrap;justify-content:center;margin-top:2px;max-width:28px}
.cal-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0}
@media(max-width:768px){
  .container>div[style*="grid-template-columns:340px"]{grid-template-columns:1fr}
  .container>div[style*="grid-template-columns:340px"]>div:first-child{position:static}
}
</style>
@endsection
@section('scripts')
<script>
const BULAN  = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const BULAN_S= ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
const HARI_S = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const JADWAL_LBL = { sekali:'Sekali', mingguan:'Mingguan', bulanan:'Bulanan', dua_bulanan:'2 Bulanan' };
const TARGET_LBL = { semua:'Publik', staff:'Staff RT', personal:'Personal' };
const DOT_COLOR  = { rapat:'#1a3d2b', kegiatan:'#856404', sosial:'#C0392B', olahraga:'#1A5CA8', lainnya:'#888' };

let allEvents  = [];
let allUsers   = [];
let curKat     = '';
let curYear    = new Date().getFullYear();
let curMonth   = new Date().getMonth();
let selectedDay = null;

function localToday() {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

function fmtDate(d) {
  const dt = new Date(d.slice(0,10)+'T00:00:00');
  return { day:dt.getDate(), month:BULAN_S[dt.getMonth()], hari:HARI_S[dt.getDay()] };
}
function fmtTime(t){ return t ? t.slice(0,5) : null; }
function katClass(k){ return 'kat-'+(k||'lainnya'); }

// ── Data fetch (expanded, respects category filter) ──────────
async function load() {
  const params = new URLSearchParams();
  if (curKat) params.set('kategori', curKat);
  const r  = await fetch('/api/agenda?'+params.toString());
  const j  = await r.json();
  allEvents = (j.success ? j.data : []).map(e => ({...e, tanggal: e.tanggal.slice(0,10)}));
  renderCalendar();
  renderList();
}

// ── Category tabs ─────────────────────────────────────────────
function setKat(k) {
  curKat = k;
  document.querySelectorAll('.cat-tab').forEach(b => b.classList.toggle('active', b.dataset.kat === k));
  selectedDay = null;
  load();
}

// ── Calendar ──────────────────────────────────────────────────
function changeMonth(dir) {
  curMonth += dir;
  if (curMonth < 0)  { curMonth = 11; curYear--; }
  if (curMonth > 11) { curMonth = 0;  curYear++; }
  selectedDay = null;
  renderCalendar();
  renderList();
}

function renderCalendar() {
  document.getElementById('cal-title').textContent = `${BULAN[curMonth]} ${curYear}`;
  const today    = localToday();
  const firstDay = new Date(curYear, curMonth, 1).getDay();
  const daysIn   = new Date(curYear, curMonth+1, 0).getDate();

  // Group events by date for this month
  const byDate = {};
  allEvents.forEach(e => {
    if (!byDate[e.tanggal]) byDate[e.tanggal] = [];
    byDate[e.tanggal].push(e);
  });

  const grid = document.getElementById('cal-grid');
  grid.innerHTML = '';

  // Prefix blanks from prev month
  const prevDays = new Date(curYear, curMonth, 0).getDate();
  for (let i = firstDay - 1; i >= 0; i--) {
    const cell = document.createElement('div');
    cell.className = 'cal-day other-month';
    cell.innerHTML = `<span class="cal-dn" style="opacity:.3">${prevDays - i}</span>`;
    grid.appendChild(cell);
  }

  for (let d = 1; d <= daysIn; d++) {
    const iso  = `${curYear}-${String(curMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const evs  = byDate[iso] || [];
    const cell = document.createElement('div');
    cell.className = 'cal-day' + (iso === today ? ' today' : '') + (iso === selectedDay ? ' selected' : '');
    cell.onclick   = () => selectDay(iso);

    // Deduplicate dots by category
    const cats = [...new Set(evs.map(e => e.kategori))].slice(0,4);
    const dotsHtml = cats.map(c => `<span class="cal-dot" style="background:${DOT_COLOR[c]||'#888'}"></span>`).join('');

    cell.innerHTML = `<span class="cal-dn">${d}</span>${dotsHtml ? `<div class="cal-dots">${dotsHtml}</div>` : ''}`;
    grid.appendChild(cell);
  }

  // Suffix blanks
  const total   = firstDay + daysIn;
  const rem     = total % 7 === 0 ? 0 : 7 - (total % 7);
  for (let i = 1; i <= rem; i++) {
    const cell = document.createElement('div');
    cell.className = 'cal-day other-month';
    cell.innerHTML = `<span class="cal-dn" style="opacity:.3">${i}</span>`;
    grid.appendChild(cell);
  }
}

function selectDay(iso) {
  selectedDay = selectedDay === iso ? null : iso;
  renderCalendar();
  renderList();
}

// ── Event List ────────────────────────────────────────────────
function renderList() {
  const filter = document.getElementById('f-filter-time').value;
  const today  = localToday();

  let events = allEvents;

  if (selectedDay) {
    events = events.filter(e => e.tanggal === selectedDay);
    const dd = fmtDate(selectedDay);
    document.getElementById('list-label').textContent = `Agenda ${dd.hari}, ${dd.day} ${dd.month}`;
  } else {
    // Filter to current month
    const monthStr = `${curYear}-${String(curMonth+1).padStart(2,'0')}`;
    events = events.filter(e => e.tanggal.startsWith(monthStr));
    document.getElementById('list-label').textContent = `Agenda ${BULAN[curMonth]} ${curYear}`;
  }

  if (filter === 'mendatang') events = events.filter(e => e.tanggal >= today);
  else if (filter === 'lalu')  events = events.filter(e => e.tanggal <  today);

  events.sort((a,b) => a.tanggal.localeCompare(b.tanggal));

  const el = document.getElementById('agenda-list');
  if (!events.length) {
    el.innerHTML = '<div style="text-align:center;padding:2.5rem;color:var(--ink-soft)">Tidak ada agenda.</div>';
    return;
  }

  el.innerHTML = events.map(a => {
    const d      = fmtDate(a.tanggal);
    const waktu  = a.waktu_mulai ? `${fmtTime(a.waktu_mulai)}${a.waktu_selesai ? ' – '+fmtTime(a.waktu_selesai) : ''}` : null;
    const isHari = a.status === 'hari_ini';
    const jadwal = a.jenis_jadwal || 'sekali';
    const target = a.target_audience || 'semua';
    const hariStr = jadwal === 'mingguan' && a.hari_minggu?.length
      ? ' (' + a.hari_minggu.map(h => HARI_S[h]).join(', ') + ')' : '';

    return `<div class="agenda-card" style="${isHari ? 'border-color:var(--forest);box-shadow:0 0 0 2px rgba(26,61,43,0.08)' : ''}">
      <div class="agenda-date-box" style="${isHari ? 'background:var(--forest)' : ''}">
        <div class="agenda-date-day" style="${isHari ? 'color:#fff' : ''}">${d.day}</div>
        <div class="agenda-date-month" style="${isHari ? 'color:rgba(255,255,255,.7)' : ''}">${d.month}</div>
      </div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin-bottom:3px">
          <span style="font-weight:500;font-size:14px">${a.judul}</span>
          ${isHari ? '<span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px;background:var(--forest);color:#fff">HARI INI</span>' : ''}
          <span class="kat-badge ${katClass(a.kategori)}">${a.kategori}</span>
          ${jadwal!=='sekali' ? `<span style="font-size:10px;padding:2px 7px;border-radius:99px;background:#EAF4FF;color:#1A5CA8">🔁 ${JADWAL_LBL[jadwal]}${hariStr}</span>` : ''}
          ${target!=='semua' ? `<span style="font-size:10px;padding:2px 7px;border-radius:99px;background:#FBF3DC;color:#856404">👥 ${TARGET_LBL[target]}</span>` : ''}
        </div>
        <div style="font-size:12px;color:var(--ink-soft);display:flex;gap:10px;flex-wrap:wrap">
          <span>${d.hari}, ${d.day} ${d.month}</span>
          ${waktu ? `<span>🕐 ${waktu}</span>` : ''}
          ${a.lokasi ? `<span>📍 ${a.lokasi}</span>` : ''}
        </div>
        ${a.deskripsi ? `<div style="font-size:12px;color:var(--ink-mid);margin-top:4px;line-height:1.5">${a.deskripsi}</div>` : ''}
      </div>
      <div style="flex-shrink:0;display:flex;flex-direction:column;gap:4px;align-items:flex-end">
        <div style="display:flex;gap:4px">
          <button onclick='editAgenda(${JSON.stringify(a)})' class="act-btn" style="border-color:var(--border);background:#fff;color:var(--ink-mid)">Edit</button>
          <button onclick="delAgenda(${a.id})" class="act-btn" style="border-color:#FDECEA;background:#FDECEA;color:var(--rust)">Hapus</button>
        </div>
        <div style="display:flex;gap:4px">
          <button onclick="sendReminder(${a.id},'wa')" class="act-btn" title="Kirim WA Reminder" style="border-color:#D4EDDA;background:#D4EDDA;color:#1a3d2b;font-size:10px">📱 WA</button>
          <button onclick="sendReminder(${a.id},'email')" class="act-btn" title="Kirim Email Reminder" style="border-color:#D1ECF1;background:#D1ECF1;color:#0C5460;font-size:10px">✉️ Email</button>
        </div>
      </div>
    </div>`;
  }).join('');
}

// ── Form helpers ──────────────────────────────────────────────
function onJenisChange() {
  const v = document.getElementById('f-jenis-jadwal').value;
  document.getElementById('section-hari').style.display  = v === 'mingguan' ? '' : 'none';
  document.getElementById('section-akhir').style.display = v !== 'sekali'   ? '' : 'none';
}

function onTargetChange() {
  const v = document.getElementById('f-target').value;
  document.getElementById('section-personal').style.display = v === 'personal' ? '' : 'none';
  if (v === 'personal' && !allUsers.length) fetchUsers();
}

async function fetchUsers() {
  const r = await fetch('/api/users');
  const j = await r.json();
  if (j.success) { allUsers = j.data || []; renderUserList(allUsers); }
}

function renderUserList(users) {
  const sel  = getSelectedUserIds();
  const wrap = document.getElementById('user-list-inner');
  if (!users.length) {
    wrap.innerHTML = '<div style="padding:8px 12px;color:var(--ink-mute);font-size:12px">Tidak ada pengguna.</div>';
    return;
  }
  wrap.innerHTML = users.map(u => {
    const ck   = sel.includes(u.id) ? 'checked' : '';
    const unit = u.units?.length ? ` · ${u.units[0].blok}${u.units[0].nomor}` : '';
    return `<label style="display:flex;align-items:center;gap:8px;padding:7px 12px;cursor:pointer;font-size:12px;border-bottom:1px solid var(--border)">
      <input type="checkbox" class="cb-user" value="${u.id}" ${ck} onchange="updateSelectedCount()" style="accent-color:var(--forest)">
      <span style="min-width:0"><b>${u.nama||u.email}</b> <span style="color:var(--ink-mute)">${unit}</span></span>
    </label>`;
  }).join('');
  updateSelectedCount();
}

function filterUsers() {
  const q = document.getElementById('user-search').value.toLowerCase();
  renderUserList(q ? allUsers.filter(u => (u.nama||'').toLowerCase().includes(q)||(u.email||'').toLowerCase().includes(q)) : allUsers);
}

function getSelectedUserIds() {
  return [...document.querySelectorAll('.cb-user:checked')].map(cb => parseInt(cb.value));
}

function updateSelectedCount() {
  const el = document.getElementById('selected-count');
  if (el) el.textContent = `${getSelectedUserIds().length} pengguna dipilih`;
}

// ── Open / Close ──────────────────────────────────────────────
function openForm(reset=true) {
  if (reset) {
    ['f-id','f-judul','f-tanggal','f-waktu-mulai','f-waktu-selesai','f-lokasi','f-deskripsi','f-tanggal-akhir']
      .forEach(id => { const el=document.getElementById(id); if(el) el.value=''; });
    document.getElementById('f-kategori-form').value = 'rapat';
    document.getElementById('f-jenis-jadwal').value  = 'sekali';
    document.getElementById('f-target').value        = 'semua';
    document.getElementById('f-kirim-wa').checked    = false;
    document.getElementById('f-kirim-email').checked = false;
    document.querySelectorAll('.cb-hari').forEach(cb => cb.checked = false);
    document.getElementById('form-title').textContent = 'Tambah Agenda';
    onJenisChange(); onTargetChange();
  }
  document.getElementById('form-overlay').style.display = 'flex';
}

function closeForm() { document.getElementById('form-overlay').style.display = 'none'; }

function editAgenda(a) {
  document.getElementById('f-id').value            = a.id;
  document.getElementById('f-judul').value         = a.judul;
  document.getElementById('f-tanggal').value       = a.tanggal.slice(0,10);
  document.getElementById('f-waktu-mulai').value   = a.waktu_mulai  ? a.waktu_mulai.slice(0,5)  : '';
  document.getElementById('f-waktu-selesai').value = a.waktu_selesai? a.waktu_selesai.slice(0,5) : '';
  document.getElementById('f-lokasi').value        = a.lokasi    || '';
  document.getElementById('f-deskripsi').value     = a.deskripsi || '';
  document.getElementById('f-kategori-form').value = a.kategori  || 'lainnya';
  document.getElementById('f-jenis-jadwal').value  = a.jenis_jadwal || 'sekali';
  document.getElementById('f-tanggal-akhir').value = a.tanggal_akhir ? a.tanggal_akhir.slice(0,10) : '';
  document.getElementById('f-target').value        = a.target_audience || 'semua';
  document.getElementById('f-kirim-wa').checked    = false;
  document.getElementById('f-kirim-email').checked = false;
  document.getElementById('form-title').textContent = 'Edit Agenda';

  const hariArr = a.hari_minggu || [];
  document.querySelectorAll('.cb-hari').forEach(cb => { cb.checked = hariArr.includes(parseInt(cb.value)); });

  onJenisChange();
  onTargetChange();

  if (a.target_audience === 'personal') {
    const ids = a.target_user_ids || [];
    setTimeout(() => {
      document.querySelectorAll('.cb-user').forEach(cb => { cb.checked = ids.includes(parseInt(cb.value)); });
      updateSelectedCount();
    }, 150);
  }

  openForm(false);
}

// ── Save ──────────────────────────────────────────────────────
async function save() {
  const id     = document.getElementById('f-id').value;
  const jadwal = document.getElementById('f-jenis-jadwal').value;
  const target = document.getElementById('f-target').value;

  const hariMinggu   = jadwal === 'mingguan'
    ? [...document.querySelectorAll('.cb-hari:checked')].map(cb => parseInt(cb.value)) : null;
  const tanggalAkhir = jadwal !== 'sekali'
    ? (document.getElementById('f-tanggal-akhir').value || null) : null;
  const targetUserIds = target === 'personal' ? getSelectedUserIds() : null;

  const body = {
    judul:           document.getElementById('f-judul').value.trim(),
    tanggal:         document.getElementById('f-tanggal').value,
    waktu_mulai:     document.getElementById('f-waktu-mulai').value   || null,
    waktu_selesai:   document.getElementById('f-waktu-selesai').value || null,
    lokasi:          document.getElementById('f-lokasi').value.trim() || null,
    deskripsi:       document.getElementById('f-deskripsi').value.trim() || null,
    kategori:        document.getElementById('f-kategori-form').value,
    jenis_jadwal:    jadwal,
    hari_minggu:     hariMinggu,
    tanggal_akhir:   tanggalAkhir,
    target_audience: target,
    target_user_ids: targetUserIds,
    kirim_wa:        document.getElementById('f-kirim-wa').checked,
    kirim_email:     document.getElementById('f-kirim-email').checked,
  };

  if (!body.judul || !body.tanggal) { alert('Judul dan tanggal wajib diisi.'); return; }
  if (jadwal === 'mingguan' && (!hariMinggu || !hariMinggu.length)) { alert('Pilih minimal satu hari.'); return; }
  if (target === 'personal' && (!targetUserIds || !targetUserIds.length)) { alert('Pilih minimal satu pengguna.'); return; }

  const r = await fetch(id ? `/api/agenda/${id}` : '/api/agenda', {
    method: id ? 'PUT' : 'POST',
    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': _csrfToken },
    body: JSON.stringify(body),
  });
  const j = await r.json();
  if (j.success) { closeForm(); showToast(j.message); load(); }
  else alert(j.message);
}

// ── Delete ────────────────────────────────────────────────────
async function delAgenda(id) {
  if (!confirm('Hapus agenda ini? Untuk agenda berulang, semua pengulangannya akan dihapus.')) return;
  const r = await fetch(`/api/agenda/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  if (j.success) { showToast(j.message); load(); }
  else alert(j.message);
}

// ── Send Reminder ─────────────────────────────────────────────
async function sendReminder(id, channel) {
  if (!confirm(`Kirim reminder ${channel === 'wa' ? 'WhatsApp' : 'Email'}?`)) return;
  const r = await fetch(`/api/agenda/${id}/reminder`, {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},
    body: JSON.stringify({ channel }),
  });
  const j = await r.json();
  showToast(j.message);
}

// ── Month filter --
document.getElementById('f-filter-time').onchange = renderList;

load();
</script>
@endsection
