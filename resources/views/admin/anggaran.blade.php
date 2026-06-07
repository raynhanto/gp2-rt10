@extends('layouts.admin')
@section('title', 'Anggaran')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Anggaran & Budget</div>
    </div>
    <button onclick="openForm()" class="btn-primary">+ Tambah Pos</button>
  </div>

  <div style="display:flex;gap:8px;margin-bottom:1.5rem;flex-wrap:wrap" id="kampanye-tabs"></div>
  <div id="anggaran-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
</div>

<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:440px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem">Tambah Pos Anggaran</div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Kampanye</label>
      <select id="form-kampanye" style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;background:#fff"></select>
    </div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Nama pos</label>
      <input type="text" id="form-pos" placeholder="Contoh: Lampu LED (3 unit)"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
    </div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Estimasi (Rp)</label>
      <input type="number" id="form-estimasi" placeholder="1500000"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
    </div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Catatan (opsional)</label>
      <input type="text" id="form-catatan" placeholder=""
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
    </div>
    <div style="display:flex;gap:8px;margin-top:0.5rem">
      <button onclick="saveAnggaran()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="document.getElementById('form-overlay').style.display='none'" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
let allKampanye = [];
let activeKampanye = null;

async function loadAll() {
  const [aRes, kRes] = await Promise.all([fetch('/api/anggaran'), fetch('/api/kampanye')]);
  const aData = await aRes.json();
  const kData = await kRes.json();
  if (!kData.success) return;
  allKampanye = kData.data;

  const tabs = document.getElementById('kampanye-tabs');
  tabs.innerHTML = [{ id:'', judul:'Semua' }, ...allKampanye].map((k,i) => `
    <button onclick="filterKampanye('${k.id||''}')" id="tab-${k.id||'all'}"
      style="padding:7px 16px;border-radius:100px;font-size:13px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif;border:1.5px solid ${i===0?'var(--forest)':'var(--border)'};background:${i===0?'var(--forest)':'#fff'};color:${i===0?'#fff':'var(--ink-soft)'}">
      ${k.judul}
    </button>`).join('');

  const sel = document.getElementById('form-kampanye');
  sel.innerHTML = allKampanye.map(k=>`<option value="${k.id}">${k.judul}</option>`).join('');

  if (aData.success) renderAnggaran(aData.data);
}

function filterKampanye(id) {
  activeKampanye = id || null;
  document.querySelectorAll('[id^="tab-"]').forEach(b => {
    const active = b.id === 'tab-' + (id||'all');
    b.style.background = active?'var(--forest)':'#fff';
    b.style.color = active?'#fff':'var(--ink-soft)';
    b.style.borderColor = active?'var(--forest)':'var(--border)';
  });
  fetch('/api/anggaran'+(id?'?kampanye_id='+id:'')).then(r=>r.json()).then(d=>{ if(d.success) renderAnggaran(d.data); });
}

function renderAnggaran(list) {
  if (!list.length) { document.getElementById('anggaran-list').innerHTML='<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Belum ada pos anggaran.</div>'; return; }
  const byKampanye = {};
  list.forEach(a => {
    if (!byKampanye[a.kampanye_id]) byKampanye[a.kampanye_id] = { judul: a.judul, items: [] };
    byKampanye[a.kampanye_id].items.push(a);
  });
  document.getElementById('anggaran-list').innerHTML = Object.values(byKampanye).map(g => `
    <div class="card" style="margin-bottom:1.5rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.1rem;color:var(--forest);margin-bottom:1rem">${g.judul}</div>
      ${g.items.map(a => {
        const pct = a.estimasi > 0 ? Math.min(100,Math.round(a.realisasi/a.estimasi*100)) : 0;
        return `<div style="margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--border)">
          <div style="display:flex;justify-content:space-between;margin-bottom:6px">
            <span style="font-size:13px;font-weight:500">${a.pos}</span>
            <span style="font-size:12px;color:var(--ink-soft)">${pct}% terpakai</span>
          </div>
          <div class="progress-track" style="margin:0 0 6px"><div class="progress-fill ${pct>=100?'pf-rust':'pf-green'}" style="width:${pct}%"></div></div>
          <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink-soft)">
            <span>Realisasi: Rp ${parseInt(a.realisasi).toLocaleString('id-ID')}</span>
            <span>Estimasi: Rp ${parseInt(a.estimasi).toLocaleString('id-ID')}</span>
          </div>
        </div>`;
      }).join('')}
      <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;padding-top:4px">
        <span style="color:var(--forest)">Total realisasi: Rp ${g.items.reduce((s,a)=>s+parseInt(a.realisasi),0).toLocaleString('id-ID')}</span>
        <span style="color:var(--ink-mid)">Total estimasi: Rp ${g.items.reduce((s,a)=>s+parseInt(a.estimasi),0).toLocaleString('id-ID')}</span>
      </div>
    </div>`).join('');
}

function openForm() {
  if (activeKampanye) document.getElementById('form-kampanye').value = activeKampanye;
  document.getElementById('form-overlay').style.display = 'flex';
}

async function saveAnggaran() {
  const body = {
    kampanye_id: parseInt(document.getElementById('form-kampanye').value),
    pos: document.getElementById('form-pos').value.trim(),
    estimasi: parseInt(document.getElementById('form-estimasi').value),
    catatan: document.getElementById('form-catatan').value.trim() || null
  };
  if (!body.pos || !body.estimasi) { alert('Nama pos dan estimasi wajib diisi.'); return; }
  const res = await fetch('/api/anggaran', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken}, body: JSON.stringify(body) });
  const data = await res.json();
  if (data.success) { document.getElementById('form-overlay').style.display='none'; showToast(data.message); loadAll(); }
  else alert(data.message);
}
loadAll();
</script>
@endsection
