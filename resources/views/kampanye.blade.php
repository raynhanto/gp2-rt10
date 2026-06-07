@extends('layouts.app')
@section('title', 'Kampanye')
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container">
  <div class="fade-in" style="margin-bottom:2.5rem">
    <div class="section-label">Semua Kampanye</div>
    <div class="section-title">Pilih kampanye<br>yang ingin kamu dukung</div>
  </div>

  <div style="display:flex;gap:8px;margin-bottom:2rem;flex-wrap:wrap" id="filters">
    @foreach(['semua'=>'Semua','aktif'=>'Aktif','urgent'=>'Mendesak','selesai'=>'Selesai'] as $val=>$label)
    <button onclick="filterKampanye('{{ $val }}')" id="filter-{{ $val }}"
      style="padding:8px 18px;border-radius:100px;font-size:13px;font-weight:500;cursor:pointer;transition:all 0.15s;font-family:'DM Sans',sans-serif;border:1.5px solid {{ $val==='semua' ? 'var(--forest)' : 'var(--border)' }};background:{{ $val==='semua' ? 'var(--forest)' : '#fff' }};color:{{ $val==='semua' ? '#fff' : 'var(--ink-soft)' }}">
      {{ $label }}
    </button>
    @endforeach
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem" id="kampanye-grid">
    <div style="text-align:center;padding:3rem;color:var(--ink-soft);grid-column:1/-1">Memuat...</div>
  </div>
</div>
</main>
@endsection
@section('scripts')
<script>
let allKampanye = [];
let activeFilter = 'semua';

async function loadKampanye() {
  const res = await fetch('/api/kampanye');
  const data = await res.json();
  if (!data.success) return;
  allKampanye = data.data;
  renderKampanye();
}

function filterKampanye(status) {
  activeFilter = status;
  document.querySelectorAll('[id^="filter-"]').forEach(btn => {
    const isActive = btn.id === 'filter-' + status;
    btn.style.background = isActive ? 'var(--forest)' : '#fff';
    btn.style.color = isActive ? '#fff' : 'var(--ink-soft)';
    btn.style.borderColor = isActive ? 'var(--forest)' : 'var(--border)';
  });
  renderKampanye();
}

const KFILL  = ['pf-green','pf-gold','pf-rust','pf-green'];
const KPH_GR = [
  'linear-gradient(135deg,#1A3D2B 0%,#3D7A56 100%)',
  'linear-gradient(135deg,#9A7818 0%,#C8A030 100%)',
  'linear-gradient(135deg,#B5401A 0%,#D96A4A 100%)',
  'linear-gradient(135deg,#2D5AA8 0%,#5B89D8 100%)',
];

function renderKampanye() {
  const list = activeFilter === 'semua' ? allKampanye : allKampanye.filter(k => k.status === activeFilter);
  const grid = document.getElementById('kampanye-grid');
  if (!list.length) { grid.innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft);grid-column:1/-1">Tidak ada kampanye.</div>'; return; }
  grid.innerHTML = list.map((k,i) => {
    const pct = Math.min(100, Math.round(k.terkumpul / k.target * 100));
    const badgeClass = k.status==='urgent' ? 'badge-urgent' : pct>=90 ? 'badge-nearly' : k.status==='selesai' ? 'badge-done' : 'badge-open';
    const badgeText  = k.status==='urgent' ? 'Mendesak' : pct>=90 ? 'Hampir tercapai' : k.status==='selesai' ? 'Selesai' : 'Berjalan';
    const dl = k.deadline ? new Date(k.deadline).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'}) : null;
    const imgHtml = k.foto_url
      ? '<img src="' + k.foto_url + '" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.45s ease">'
      : '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:' + KPH_GR[i%4] + '"><svg viewBox="0 0 24 24" fill="rgba(255,255,255,0.35)" style="width:56px;height:56px"><path d="M12 2L2 8v14h7v-7h6v7h7V8L12 2z"/></svg></div>';
    return `<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);cursor:pointer;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s"
      onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(0,0,0,0.1)';const img=this.querySelector('img');if(img)img.style.transform='scale(1.05)'"
      onmouseleave="this.style.transform='';this.style.boxShadow='';const img=this.querySelector('img');if(img)img.style.transform=''"
      onclick="location.href='/kampanye/${k.id}'">
      <div style="height:200px;position:relative;overflow:hidden">
        ${imgHtml}
        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(15,35,24,0.4) 0%,transparent 55%);pointer-events:none"></div>
        <span class="badge ${badgeClass}" style="position:absolute;top:12px;right:12px;backdrop-filter:blur(6px)">${badgeText}</span>
      </div>
      <div style="padding:1.125rem 1.25rem 1.375rem">
        <div style="font-family:'DM Serif Display',serif;font-size:1.15rem;color:var(--forest);line-height:1.3;margin-bottom:6px">${k.judul}</div>
        <div style="font-size:13px;color:var(--ink-soft);line-height:1.65;margin-bottom:1rem">${k.deskripsi.substring(0,110)}…</div>
        <div class="progress-track"><div class="progress-fill ${KFILL[i%4]}" style="width:${pct}%"></div></div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:${dl?'0.625rem':'0'}">
          <span style="font-size:14px;font-weight:600;color:var(--forest)">Rp ${parseInt(k.terkumpul).toLocaleString('id-ID')}</span>
          <span style="font-size:12px;color:var(--ink-soft)">${pct}% dari Rp ${parseInt(k.target).toLocaleString('id-ID')}</span>
        </div>
        ${dl ? '<div style="font-size:12px;color:var(--ink-soft)"><i class="fa fa-calendar" style="margin-right:4px;color:var(--gold)"></i>Deadline: ' + dl + '</div>' : ''}
      </div>
    </div>`;
  }).join('');
}
loadKampanye();
</script>
@endsection
