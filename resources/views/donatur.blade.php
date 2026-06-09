@extends('layouts.app')
@section('title', 'Donatur')
@section('styles')
<style>
.rank-row{display:flex;align-items:center;gap:14px;padding:11px 16px;border-radius:var(--radius-sm);transition:background 0.15s}
.rank-row:hover{background:var(--forest-pale)}
.rank-medal{width:28px;text-align:center;font-size:18px;flex-shrink:0}
.rank-num{width:28px;text-align:center;font-size:13px;font-weight:600;color:var(--ink-mute);flex-shrink:0}
.rank-avatar{width:38px;height:38px;border-radius:50%;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.rank-name{flex:1;min-width:0}
.rank-name-text{font-size:14px;font-weight:500;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.rank-name-sub{font-size:11px;color:var(--ink-mute);margin-top:1px}
.rank-amount{text-align:right;flex-shrink:0}
.rank-amount-val{font-size:14px;font-weight:700;color:var(--forest)}
.rank-amount-sub{font-size:11px;color:var(--ink-mute);margin-top:1px}
.rank-bar-wrap{height:3px;background:var(--parchment);border-radius:99px;margin-top:5px;overflow:hidden}
.rank-bar{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--forest-mid),var(--forest-light));transition:width 0.7s cubic-bezier(0.25,0.8,0.25,1)}
.rank-1 .rank-bar{background:linear-gradient(90deg,var(--gold-dark),var(--gold-light))}
.rank-2 .rank-bar{background:linear-gradient(90deg,#888,#bbb)}
.rank-3 .rank-bar{background:linear-gradient(90deg,#A0673A,#C9956A)}
.rank-1 .rank-amount-val{color:var(--gold-dark)}
.rank-2 .rank-amount-val{color:#666}
.rank-3 .rank-amount-val{color:#A0673A}
.rank-divider{height:1px;background:var(--border);margin:2px 0}
.donatur-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem}
.donatur-cols{display:grid;grid-template-columns:3fr 2fr;gap:1.5rem;align-items:start}
.filter-pills{display:flex;gap:8px;overflow-x:auto;padding-bottom:2px;margin-bottom:1.5rem;scrollbar-width:none}
.filter-pills::-webkit-scrollbar{display:none}
.filter-pill{flex-shrink:0;padding:6px 14px;border-radius:100px;border:1.5px solid var(--border);background:#fff;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--ink);cursor:pointer;white-space:nowrap;transition:background 0.15s,color 0.15s,border-color 0.15s}
.filter-pill.active{background:var(--forest);color:#fff;border-color:var(--forest)}
@media(max-width:640px){
  .donatur-stats{gap:0.5rem}
  .donatur-stats>div{padding:1rem 0.75rem}
  .donatur-cols{grid-template-columns:1fr}
}
</style>
@endsection
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container">

  <div class="fade-in" style="margin-bottom:2.5rem">
    <div class="section-label">Donatur</div>
    <div class="section-title">Warga yang sudah<br>bergerak bersama</div>
  </div>

  <div class="donatur-stats">
    <div style="background:var(--cream);border-radius:var(--radius-sm);padding:1.25rem;text-align:center">
      <div style="margin-bottom:5px;color:var(--forest);font-size:18px"><i class="fa-solid fa-users"></i></div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--forest)" id="total-donatur">—</div>
      <div style="font-size:12px;color:var(--ink-soft);margin-top:3px">Donatur Unik</div>
    </div>
    <div style="background:var(--cream);border-radius:var(--radius-sm);padding:1.25rem;text-align:center">
      <div style="margin-bottom:5px;color:var(--forest);font-size:18px"><i class="fa-solid fa-coins"></i></div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--forest)" id="total-terkumpul">—</div>
      <div style="font-size:12px;color:var(--ink-soft);margin-top:3px">Total Terkumpul</div>
    </div>
    <div style="background:var(--cream);border-radius:var(--radius-sm);padding:1.25rem;text-align:center">
      <div style="margin-bottom:5px;color:var(--forest);font-size:18px"><i class="fa-solid fa-bullseye"></i></div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--forest)" id="total-kampanye">—</div>
      <div style="font-size:12px;color:var(--ink-soft);margin-top:3px">Kampanye Aktif</div>
    </div>
  </div>

  <div class="filter-pills" id="filter-pills">
    <button class="filter-pill active" data-id="" onclick="selectPill(this)">Semua kampanye</button>
  </div>

  <div class="donatur-cols">

    {{-- Leaderboard --}}
    <div class="card fade-in" style="padding:0;overflow:hidden">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.25rem 0.875rem;border-bottom:1px solid var(--border)">
        <div style="font-size:15px;font-weight:600;color:var(--ink)"><i class="fa-solid fa-trophy" style="color:var(--gold-dark);margin-right:6px"></i>Papan Peringkat</div>
        <div style="font-size:11px;color:var(--ink-mute);font-weight:500">diurutkan dari terbanyak</div>
      </div>
      <div id="leaderboard-list" style="padding:0.5rem 0">
        <div style="text-align:center;padding:2.5rem;color:var(--ink-soft)">Memuat...</div>
      </div>
    </div>

    {{-- Recent donations --}}
    <div class="card fade-in" style="padding:0;overflow:hidden">
      <div style="padding:1.25rem 1.25rem 0.875rem;border-bottom:1px solid var(--border)">
        <div style="font-size:15px;font-weight:600;color:var(--ink)"><i class="fa-regular fa-clock" style="color:var(--ink-soft);margin-right:6px"></i>Donasi Terbaru</div>
      </div>
      <div id="recent-list" style="padding:0.5rem 0.75rem">
        <div style="text-align:center;padding:2.5rem;color:var(--ink-soft)">Memuat...</div>
      </div>
    </div>

  </div>
</div>
</main>
@endsection
@section('scripts')
<script>
const avatarPalette = [
  {bg:'#E8F4ED',color:'#1C3D2E'},{bg:'#FDF6E4',color:'#7A5C00'},
  {bg:'#E8F0FD',color:'#2D5AA8'},{bg:'#FDECEA',color:'#B5451B'},
  {bg:'#F3EEFF',color:'#5C3B8A'},{bg:'#E5F6FF',color:'#1A6A9A'},
];

function initials(nama) {
  return (nama || '?').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
}

function buildRanking(list) {
  const map = {};
  list.forEach(d => {
    const key = d.nama || 'Donatur Anonim';
    if (!map[key]) map[key] = { nama: key, total: 0, count: 0 };
    map[key].total += parseInt(d.nominal);
    map[key].count++;
  });
  return Object.values(map).sort((a, b) => b.total - a.total);
}

function renderLeaderboard(ranked) {
  const el = document.getElementById('leaderboard-list');
  if (!ranked.length) {
    el.innerHTML = '<div style="text-align:center;padding:2.5rem;color:var(--ink-soft)">Belum ada donasi.</div>';
    return;
  }

  const medalColors = [['#C8A030','#FBF3DC'],['#8A8A8A','#EFEFEF'],['#A0673A','#F5E8DC']];
  const medals = medalColors.map(([c,bg]) => `<div style="width:24px;height:24px;border-radius:50%;background:${bg};border:2px solid ${c};display:flex;align-items:center;justify-content:center;margin:0 auto"><svg viewBox="0 0 24 24" fill="${c}" style="width:12px;height:12px"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div>`);
  const maxTotal = ranked[0].total;

  const LIMIT   = 10;
  const visible = (_lbExpanded || ranked.length <= LIMIT) ? ranked : ranked.slice(0, LIMIT);
  const hasMore = !_lbExpanded && ranked.length > LIMIT;

  el.innerHTML = visible.map((r, i) => {
    const av = avatarPalette[i % avatarPalette.length];
    const ini = initials(r.nama);
    const pct = Math.round((r.total / maxTotal) * 100);
    const rank = i + 1;
    const isMedal = rank <= 3;
    const rankClass = isMedal ? `rank-${rank}` : '';

    const rankBadge = isMedal
      ? `<div class="rank-medal">${medals[i]}</div>`
      : `<div class="rank-num">${rank}</div>`;

    const divider = i === 3 ? '<div class="rank-divider"></div>' : '';

    return `${divider}
    <div class="rank-row ${rankClass}">
      ${rankBadge}
      <div class="rank-avatar" style="background:${av.bg};color:${av.color}">${ini}</div>
      <div class="rank-name">
        <div class="rank-name-text">${r.nama}</div>
        <div class="rank-bar-wrap"><div class="rank-bar" style="width:${pct}%"></div></div>
      </div>
      <div class="rank-amount">
        <div class="rank-amount-val">Rp ${r.total.toLocaleString('id-ID')}</div>
        <div class="rank-amount-sub">${r.count}× donasi</div>
      </div>
    </div>`;
  }).join('');

  if (hasMore) {
    el.insertAdjacentHTML('beforeend', `
      <div style="text-align:center;padding:0.875rem 1rem;border-top:1px solid var(--border)">
        <button onclick="expandLeaderboard()" style="background:none;border:none;font-size:13px;color:var(--forest);font-weight:500;cursor:pointer;padding:0">
          Tampilkan semua ${ranked.length} donatur <i class="fa-solid fa-chevron-down" style="font-size:10px;margin-left:3px"></i>
        </button>
      </div>`);
  }
}

function parseDate(str) {
  // MySQL timestamps use space; iOS Safari requires 'T' separator
  return new Date((str || '').replace(' ', 'T'));
}

function recentItemHtml(d) {
  const dt  = parseDate(d.created_at);
  const tgl = isNaN(dt) ? '—' : dt.toLocaleDateString('id-ID', {day:'numeric', month:'short'});
  const nama = d.nama || 'Donatur Anonim';
  const ini  = initials(nama);
  return `<div style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)">
    <div style="width:30px;height:30px;border-radius:50%;background:var(--forest-pale);color:var(--forest);font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">${ini}</div>
    <div style="flex:1;min-width:0">
      <div style="font-size:12.5px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${nama}</div>
      <div style="font-size:11px;color:var(--ink-mute)">${tgl} · ${d.judul || 'Donasi Umum'}</div>
    </div>
    <div style="font-size:12.5px;font-weight:600;color:var(--forest);white-space:nowrap">Rp ${parseInt(d.nominal).toLocaleString('id-ID')}</div>
  </div>`;
}

function renderRecent(list) {
  const el = document.getElementById('recent-list');
  if (!list.length) {
    el.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-soft)">Belum ada donasi.</div>';
    return;
  }
  el.innerHTML = list.map(recentItemHtml).join('') +
    `<div id="loadMoreWrap" style="text-align:center;padding:0.875rem 0;border-top:1px solid var(--border);display:none">
      <button id="loadMoreBtn" class="btn-secondary" style="font-size:12px;padding:6px 16px" onclick="loadMore()">Muat lebih banyak</button>
    </div>`;
}

function appendRecent(items) {
  const wrap = document.getElementById('loadMoreWrap');
  if (wrap) wrap.insertAdjacentHTML('beforebegin', items.map(recentItemHtml).join(''));
}

let activeKampanyeId = '';
let _currentPage = 1;
let _totalPages  = 1;
let _allLoaded   = [];
let _lbExpanded  = false;

function selectPill(el) {
  document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
  el.classList.add('active');
  activeKampanyeId = el.dataset.id;
  _currentPage = 1;
  _totalPages  = 1;
  _allLoaded   = [];
  _lbExpanded  = false;
  loadDonatur(true);
}

async function loadDonatur(reset = false) {
  const errMsg = '<div style="text-align:center;padding:2.5rem;color:var(--ink-soft)">Gagal memuat data.</div>';
  if (reset) {
    document.getElementById('leaderboard-list').innerHTML = '<div style="text-align:center;padding:2.5rem;color:var(--ink-soft)">Memuat...</div>';
    document.getElementById('recent-list').innerHTML      = '<div style="text-align:center;padding:2.5rem;color:var(--ink-soft)">Memuat...</div>';
  }
  try {
    let url = `/api/donasi/public?per_page=50&page=${_currentPage}`;
    if (activeKampanyeId) url += '&kampanye_id=' + activeKampanyeId;
    const res  = await fetch(url, {credentials:'same-origin'});
    const data = await res.json();
    if (!data.success) {
      document.getElementById('leaderboard-list').innerHTML = errMsg;
      document.getElementById('recent-list').innerHTML = errMsg;
      return;
    }

    _allLoaded  = _allLoaded.concat(data.data);
    _totalPages = data.meta ? data.meta.pages : 1;

    const ranked       = buildRanking(_allLoaded);
    const totalNominal = _allLoaded.reduce((s, d) => s + parseInt(d.nominal), 0);
    const morePages    = _currentPage < _totalPages;

    document.getElementById('total-donatur').textContent   = ranked.length + (morePages ? '+' : '');
    document.getElementById('total-terkumpul').textContent = 'Rp ' + totalNominal.toLocaleString('id-ID');
    renderLeaderboard(ranked);

    if (reset) {
      renderRecent(_allLoaded);
    } else {
      appendRecent(data.data);
    }

    updateLoadMoreBtn();
  } catch(e) {
    document.getElementById('leaderboard-list').innerHTML = errMsg;
    document.getElementById('recent-list').innerHTML = errMsg;
  }
}

function updateLoadMoreBtn() {
  const wrap = document.getElementById('loadMoreWrap');
  const btn  = document.getElementById('loadMoreBtn');
  if (!wrap || !btn) return;
  if (_currentPage >= _totalPages) {
    wrap.style.display = 'none';
  } else {
    wrap.style.display = '';
    btn.disabled = false;
    btn.textContent = 'Muat lebih banyak';
  }
}

function loadMore() {
  const btn = document.getElementById('loadMoreBtn');
  if (btn) { btn.disabled = true; btn.textContent = 'Memuat...'; }
  _currentPage++;
  loadDonatur(false);
}

function expandLeaderboard() {
  _lbExpanded = true;
  renderLeaderboard(buildRanking(_allLoaded));
}

async function loadKampanye() {
  try {
    const res  = await fetch('/api/kampanye', {credentials:'same-origin'});
    const data = await res.json();
    if (!data.success) return;
    document.getElementById('total-kampanye').textContent = data.data.filter(k => k.status === 'aktif' || k.status === 'urgent').length;
    const pills = document.getElementById('filter-pills');
    data.data.forEach(k => {
      const btn = document.createElement('button');
      btn.className = 'filter-pill';
      btn.dataset.id = k.id;
      btn.textContent = k.judul;
      btn.onclick = () => selectPill(btn);
      pills.appendChild(btn);
    });
  } catch(e) {}
}

loadKampanye();
loadDonatur(true);
</script>
@endsection
