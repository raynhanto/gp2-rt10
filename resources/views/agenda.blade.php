@extends('layouts.app')
@section('title', 'Agenda Kegiatan RT 10')
@section('styles')
<style>
/* ── Calendar ────────────────────────────────────────────── */
.cal-shell{background:#fff;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);overflow:hidden;margin-bottom:2.5rem}
.cal-topbar{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.75rem;border-bottom:1px solid var(--border);background:var(--cream)}
.cal-nav{width:38px;height:38px;border-radius:50%;border:1.5px solid rgba(26,61,43,0.18);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--ink-mid);transition:all .15s;flex-shrink:0}
.cal-nav:hover{border-color:var(--forest);background:var(--forest);color:#fff}
.cal-month{font-family:'DM Serif Display',serif;font-size:1.4rem;color:var(--forest)}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr)}
.cal-hdr{text-align:center;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:12px 0 8px;color:var(--ink-mute);border-bottom:1px solid var(--border)}
.cal-hdr.sun{color:var(--rust)}
.cal-cell{min-height:82px;padding:8px 8px 6px;border-right:1px solid rgba(0,0,0,0.04);border-bottom:1px solid rgba(0,0,0,0.04);position:relative;transition:background .12s;vertical-align:top}
.cal-cell:nth-child(7n){border-right:none}
.cal-cell.other .cal-n{color:rgba(0,0,0,0.2)}
.cal-cell.other{background:rgba(0,0,0,0.012)}
.cal-cell.clickable{cursor:pointer}
.cal-cell.clickable:hover{background:var(--forest-pale)}
.cal-cell.is-today .cal-n{background:var(--forest);color:#fff}
.cal-cell.is-sel{background:var(--gold-pale)}
.cal-cell.is-sel .cal-n{color:var(--gold-dark);font-weight:700}
.cal-n{font-size:13px;font-weight:500;color:var(--ink);width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:50%;margin-bottom:4px;transition:background .12s}
.ep{font-size:10px;font-weight:600;padding:2px 7px;border-radius:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;display:block;margin-bottom:3px;line-height:1.5}
.ep-rapat    {background:#dff0e8;color:#12422a}
.ep-kegiatan {background:#fef3d0;color:#8a6100}
.ep-sosial   {background:#fde8e4;color:#a03018}
.ep-olahraga {background:#deeeff;color:#0e4a96}
.ep-lainnya  {background:#ececec;color:#555}
.cal-more{font-size:9.5px;color:var(--ink-mute);margin-top:1px;padding-left:2px}
.cal-legend{display:flex;gap:14px;flex-wrap:wrap;padding:12px 1.75rem;border-top:1px solid var(--border);background:var(--cream)}
.cal-legend-item{display:flex;align-items:center;gap:6px;font-size:11px;color:var(--ink-soft)}
.cal-legend-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}

/* ── Event cards ─────────────────────────────────────────── */
.ecard{background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);display:flex;overflow:hidden;margin-bottom:1rem;transition:box-shadow .18s,transform .18s}
.ecard:hover{box-shadow:var(--shadow-md);transform:translateY(-2px)}
.ecard-stripe{width:4px;flex-shrink:0}
.ecard-date{width:68px;flex-shrink:0;display:flex;flex-direction:column;align-items:center;justify-content:center;background:var(--cream);border-right:1px solid var(--border);padding:.875rem 0;gap:1px}
.ecard-day{font-family:'DM Serif Display',serif;font-size:1.75rem;color:var(--forest);line-height:1}
.ecard-mo{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--forest-light)}
.ecard-wd{font-size:10px;color:var(--ink-mute)}
.ecard.is-today .ecard-date{background:var(--forest)}
.ecard.is-today .ecard-day,.ecard.is-today .ecard-mo,.ecard.is-today .ecard-wd{color:#fff}
.ecard.is-today .ecard-mo{color:rgba(255,255,255,.6)}
.ecard.is-today .ecard-wd{color:rgba(255,255,255,.4)}
.ecard-body{flex:1;padding:1rem 1.25rem;min-width:0}
.kat-chip{font-size:10px;font-weight:700;padding:2px 9px;border-radius:99px;display:inline-block;text-transform:capitalize}
.kc-rapat    {background:#dff0e8;color:#12422a}
.kc-kegiatan {background:#fef3d0;color:#8a6100}
.kc-sosial   {background:#fde8e4;color:#a03018}
.kc-olahraga {background:#deeeff;color:#0e4a96}
.kc-lainnya  {background:#ececec;color:#555}

/* ── Misc ────────────────────────────────────────────────── */
.ftab{padding:7px 18px;border-radius:99px;font-size:12px;font-weight:500;border:1.5px solid var(--border);background:#fff;color:var(--ink-soft);cursor:pointer;transition:all .15s;font-family:'DM Sans',sans-serif;white-space:nowrap}
.ftab.on{background:var(--forest);color:#fff;border-color:var(--forest)}
.stat-pill{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.18);padding:6px 16px;border-radius:99px;font-size:12px;font-weight:500;color:rgba(255,255,255,0.85);backdrop-filter:blur(4px)}
.stat-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}

@media(max-width:640px){
  .cal-cell{min-height:52px;padding:5px 4px 3px}
  .ep{display:none}
  .cal-cell.clickable.has-dot::after{content:'';display:block;width:6px;height:6px;border-radius:50%;background:var(--forest);margin:0 auto}
  .cal-topbar{padding:1rem}
  .cal-legend{padding:10px 1rem}
  .ecard-date{width:58px}
  .ecard-day{font-size:1.4rem}
}
</style>
@endsection
@section('content')

{{-- Hero ──────────────────────────────────────────────────── --}}
<div style="background:linear-gradient(135deg,#0c1e14 0%,#1A3D2B 55%,#2e6647 100%);padding:4.5rem 0 3rem">
  <div class="container">
    <div class="fade-in">
      <div style="display:inline-flex;align-items:center;gap:8px;font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(200,160,48,.75);margin-bottom:.875rem">
        <span style="width:20px;height:2px;background:var(--gold);display:inline-block;border-radius:2px"></span>
        Kelembagaan RT 10
      </div>
      <h1 style="font-family:'DM Serif Display',serif;font-size:clamp(2rem,4vw,2.9rem);color:#fff;margin:0 0 .5rem;line-height:1.1">Agenda Kegiatan</h1>
      <p style="font-size:14px;color:rgba(255,255,255,.5);margin:0 0 1.5rem">Jadwal kegiatan, rapat, dan acara RT 10 Golden Park 2.</p>
      <div style="display:flex;gap:8px;flex-wrap:wrap" id="hero-stats"></div>
    </div>
  </div>
</div>

{{-- Main ──────────────────────────────────────────────────── --}}
<main style="padding:2.5rem 0 5rem;background:var(--warm)">
<div class="container">

  {{-- Full-width Calendar ──────────────────────────────────── --}}
  <div class="cal-shell">
    <div class="cal-topbar">
      <button class="cal-nav" onclick="prevMonth()">‹</button>
      <div class="cal-month" id="cal-title"></div>
      <button class="cal-nav" onclick="nextMonth()">›</button>
    </div>

    <div class="cal-grid" id="cal-grid">
      <div class="cal-hdr sun">Min</div>
      <div class="cal-hdr">Sen</div>
      <div class="cal-hdr">Sel</div>
      <div class="cal-hdr">Rab</div>
      <div class="cal-hdr">Kam</div>
      <div class="cal-hdr">Jum</div>
      <div class="cal-hdr">Sab</div>
    </div>

    <div class="cal-legend">
      <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#12422a"></div>Rapat</div>
      <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#8a6100"></div>Kegiatan</div>
      <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#a03018"></div>Sosial</div>
      <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#0e4a96"></div>Olahraga</div>
      <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#888"></div>Lainnya</div>
      <div style="margin-left:auto;font-size:11px;color:var(--ink-mute)" id="sel-hint"></div>
    </div>
  </div>

  {{-- Events section ───────────────────────────────────────── --}}
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.4rem;color:var(--forest)" id="ev-heading">Semua Agenda</div>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <button class="ftab on" onclick="setKat(this,'')">Semua</button>
      <button class="ftab" onclick="setKat(this,'rapat')">Rapat</button>
      <button class="ftab" onclick="setKat(this,'kegiatan')">Kegiatan</button>
      <button class="ftab" onclick="setKat(this,'sosial')">Sosial</button>
      <button class="ftab" onclick="setKat(this,'olahraga')">Olahraga</button>
    </div>
  </div>

  <div id="ev-panel"></div>

</div>
</main>
@endsection
@section('scripts')
<script>
const BULAN   = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const BULAN_S = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
const HARI_S  = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

let _all = [], _cur = { y:new Date().getFullYear(), m:new Date().getMonth() };
let _sel = null, _kat = '';

function localISO(d){ return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
const TODAY = localISO(new Date());

async function init(){
  const r = await fetch('/api/agenda');
  const j = await r.json();
  _all = j.success ? j.data : [];
  renderStats(); renderCal(); renderEvents();
}

/* ── Stats ──────────────────────────────────────────────── */
function renderStats(){
  const ym   = `${_cur.y}-${String(_cur.m+1).padStart(2,'0')}`;
  const mon  = _all.filter(e=>e.tanggal.slice(0,7)===ym).length;
  const soon = _all.filter(e=>e.tanggal.slice(0,10)>=TODAY).length;
  const tod  = _all.filter(e=>e.tanggal.slice(0,10)===TODAY).length;
  const items=[
    {c:'var(--gold-light)',t:`${mon} agenda bulan ini`},
    {c:'#7dd3a8',t:`${soon} mendatang`},
    ...(tod?[{c:'var(--rust)',t:`${tod} hari ini`}]:[]),
  ];
  document.getElementById('hero-stats').innerHTML=items.map(s=>
    `<div class="stat-pill"><div class="stat-dot" style="background:${s.c}"></div>${s.t}</div>`
  ).join('');
}

/* ── Calendar ───────────────────────────────────────────── */
function renderCal(){
  document.getElementById('cal-title').textContent=`${BULAN[_cur.m]} ${_cur.y}`;

  const grid  = document.getElementById('cal-grid');
  grid.querySelectorAll('.cal-cell').forEach(c=>c.remove());

  const first    = new Date(_cur.y,_cur.m,1);
  const lastDate = new Date(_cur.y,_cur.m+1,0).getDate();
  const startDow = first.getDay();

  const evtMap={};
  _all.forEach(e=>{
    if(_kat&&e.kategori!==_kat) return;
    const d=e.tanggal.slice(0,10);
    (evtMap[d]=evtMap[d]||[]).push(e);
  });

  for(let i=0;i<startDow;i++) grid.appendChild(mkCell(new Date(_cur.y,_cur.m,-startDow+i+1),true,evtMap));
  for(let d=1;d<=lastDate;d++) grid.appendChild(mkCell(new Date(_cur.y,_cur.m,d),false,evtMap));
  const tail=startDow+lastDate; const tr=tail%7===0?0:7-tail%7;
  for(let i=1;i<=tr;i++) grid.appendChild(mkCell(new Date(_cur.y,_cur.m+1,i),true,evtMap));

  const hint=document.getElementById('sel-hint');
  hint.textContent=_sel?'Klik tanggal lagi untuk hapus filter':'Klik tanggal untuk filter agenda';
}

function mkCell(dt,other,evtMap){
  const iso  = localISO(dt);
  const evts = evtMap[iso]||[];
  const cell = document.createElement('div');
  cell.className='cal-cell'
    +(other?' other':'')
    +(iso===TODAY?' is-today':'')
    +(_sel===iso?' is-sel':'')
    +(evts.length&&!other?' clickable':'')
    +(evts.length&&!other?' has-dot':'');

  const num=document.createElement('div');
  num.className='cal-n'; num.textContent=dt.getDate(); cell.appendChild(num);

  if(!other){
    evts.slice(0,2).forEach(e=>{
      const p=document.createElement('div');
      p.className=`ep ep-${e.kategori||'lainnya'}`;
      p.textContent=e.judul; cell.appendChild(p);
    });
    if(evts.length>2){ const m=document.createElement('div'); m.className='cal-more'; m.textContent=`+${evts.length-2} lagi`; cell.appendChild(m); }
    if(evts.length) cell.addEventListener('click',()=>{ _sel=_sel===iso?null:iso; renderCal(); renderEvents(); });
  }
  return cell;
}

function prevMonth(){ _cur.m--; if(_cur.m<0){_cur.m=11;_cur.y--;} _sel=null; renderCal(); renderEvents(); renderStats(); }
function nextMonth(){ _cur.m++; if(_cur.m>11){_cur.m=0;_cur.y++;} _sel=null; renderCal(); renderEvents(); renderStats(); }

/* ── Events ─────────────────────────────────────────────── */
function setKat(btn,kat){
  document.querySelectorAll('.ftab').forEach(b=>b.classList.remove('on'));
  btn.classList.add('on'); _kat=kat;
  renderCal(); renderEvents();
}

function renderEvents(){
  const panel=document.getElementById('ev-panel');
  const head =document.getElementById('ev-heading');
  const curMonStr=`${_cur.y}-${String(_cur.m+1).padStart(2,'0')}`;

  let list=_all.filter(e=>{
    if(_kat&&e.kategori!==_kat) return false;
    if(_sel) return e.tanggal.slice(0,10)===_sel;
    return e.tanggal.slice(0,7)===curMonStr; // filter to current month when no day selected
  });

  list.sort((a,b)=>{
    const da=a.tanggal.slice(0,10),db=b.tanggal.slice(0,10);
    if(da===TODAY&&db!==TODAY) return -1; if(db===TODAY&&da!==TODAY) return 1;
    if(da>=TODAY&&db<TODAY) return -1;    if(db>=TODAY&&da<TODAY) return 1;
    return da<db?-1:1;
  });

  if(_sel){
    const dt=new Date(_sel+'T00:00:00');
    head.textContent=`${HARI_S[dt.getDay()]}, ${dt.getDate()} ${BULAN_S[dt.getMonth()]} ${dt.getFullYear()}`;
  } else head.textContent=`Agenda ${BULAN[_cur.m]} ${_cur.y}`;

  if(!list.length){
    panel.innerHTML=`<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:3rem;text-align:center;color:var(--ink-soft)">
      <div style="font-size:2.2rem;margin-bottom:10px;color:var(--ink-mute)"><i class="fa-regular fa-calendar"></i></div>
      <div style="font-size:14px">${_sel?'Tidak ada agenda pada tanggal ini.':'Tidak ada agenda.'}</div>
    </div>`;
    return;
  }

  const stripeColor={rapat:'var(--forest)',kegiatan:'var(--gold)',sosial:'var(--rust)',olahraga:'#1A5CA8',lainnya:'#9D9080'};

  panel.innerHTML=list.map(e=>{
    const dt     =new Date(e.tanggal.slice(0,10)+'T00:00:00');
    const isToday=e.tanggal.slice(0,10)===TODAY;
    const isPast =e.tanggal.slice(0,10)<TODAY;
    const kat    =e.kategori||'lainnya';
    const waktu  =e.waktu_mulai?e.waktu_mulai.slice(0,5)+(e.waktu_selesai?' – '+e.waktu_selesai.slice(0,5):''):null;

    return `<div class="ecard${isToday?' is-today':''}" style="${isPast?'opacity:.6':''}">
      <div class="ecard-stripe" style="background:${stripeColor[kat]||'#9D9080'}"></div>
      <div class="ecard-date">
        <div class="ecard-day">${dt.getDate()}</div>
        <div class="ecard-mo">${BULAN_S[dt.getMonth()]}</div>
        <div class="ecard-wd">${HARI_S[dt.getDay()]}</div>
      </div>
      <div class="ecard-body">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px">
          <span style="font-weight:600;font-size:15px;color:var(--ink)">${e.judul}</span>
          ${isToday?'<span style="font-size:9px;font-weight:800;padding:2px 9px;border-radius:99px;background:var(--forest);color:#fff;letter-spacing:.05em">HARI INI</span>':''}
          <span class="kat-chip kc-${kat}">${kat}</span>
        </div>
        <div style="display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:var(--ink-soft)">
          ${waktu?`<span><i class="fa-regular fa-clock" style="margin-right:3px"></i>${waktu}</span>`:''}
          ${e.lokasi?`<span><i class="fa-solid fa-location-dot" style="margin-right:3px"></i>${e.lokasi}</span>`:''}
        </div>
        ${e.deskripsi?`<p style="font-size:13px;color:var(--ink-mid);margin:8px 0 0;line-height:1.65;border-top:1px solid var(--border);padding-top:8px">${e.deskripsi}</p>`:''}
      </div>
    </div>`;
  }).join('');
}

init();
</script>
@endsection
