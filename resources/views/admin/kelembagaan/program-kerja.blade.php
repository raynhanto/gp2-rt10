@extends('layouts.admin')
@section('title', 'Program Kerja')
@section('content')
<div class="container" style="max-width:1000px">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.25rem">
    <div>
      <div class="section-label">Kelembagaan</div>
      <div class="section-title">Program Kerja</div>
    </div>
    <button onclick="openForm()" class="btn-primary"><i class="fa fa-plus" style="font-size:11px"></i> Tambah Program</button>
  </div>

  <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.25rem">
    <label style="font-size:13px;font-weight:600;color:var(--ink-mid)">Tahun:</label>
    <select id="year-sel" onchange="loadYear()" style="padding:8px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'DM Sans',sans-serif;outline:none;background:#fff">
      @for($y = date('Y')+1; $y >= date('Y')-3; $y--)
      <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
      @endfor
    </select>
    <span style="font-size:12px;color:var(--ink-mute)" id="pk-count"></span>
  </div>

  <div id="pk-list" style="display:flex;flex-direction:column;gap:1.5rem"></div>
  <div id="pk-empty" style="display:none;text-align:center;padding:3rem;background:#fff;border-radius:var(--radius);border:1px solid var(--border);color:var(--ink-soft)">
    <i class="fa fa-list-check" style="font-size:2rem;display:block;margin-bottom:10px;color:var(--ink-mute)"></i>
    Belum ada program kerja untuk tahun ini.
  </div>
</div>

{{-- Modal --}}
<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:520px;max-height:92vh;overflow-y:auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest)" id="form-title">Tambah Program</div>
      <button onclick="closeForm()" style="border:none;background:none;cursor:pointer;font-size:20px;color:var(--ink-mute)"><i class="fa fa-xmark"></i></button>
    </div>
    <input type="hidden" id="f-id">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Tahun *</label>
        <input type="number" id="f-tahun" min="2020" max="2040" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
      </div>
      <div>
        <label class="flabel">Bidang *</label>
        <input type="text" id="f-bidang" placeholder="Cth: Keamanan, Sosial" list="bidang-list" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
        <datalist id="bidang-list"></datalist>
      </div>
      <div style="grid-column:span 2">
        <label class="flabel">Nama Program *</label>
        <input type="text" id="f-nama" placeholder="Nama program kerja" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
      </div>
      <div style="grid-column:span 2">
        <label class="flabel">Deskripsi</label>
        <textarea id="f-deskripsi" rows="3" placeholder="Keterangan singkat..." style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;resize:vertical;font-family:'DM Sans',sans-serif;box-sizing:border-box"></textarea>
      </div>
      <div>
        <label class="flabel">Status</label>
        <select id="f-status" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="rencana">Rencana</option>
          <option value="berjalan">Berjalan</option>
          <option value="selesai">Selesai</option>
          <option value="batal">Batal</option>
        </select>
      </div>
      <div>
        <label class="flabel">Urutan</label>
        <input type="number" id="f-urutan" value="0" min="0" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;box-sizing:border-box">
      </div>
      <div>
        <label class="flabel">Target Mulai</label>
        <input type="date" id="f-mulai" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Target Selesai</label>
        <input type="date" id="f-selesai" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button onclick="save()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeForm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

<style>
.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}
.pk-row{background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);padding:1rem 1.25rem;display:flex;gap:1rem;align-items:flex-start;margin-bottom:.625rem}
.s-badge{display:inline-block;padding:3px 10px;border-radius:99px;font-size:10px;font-weight:700;letter-spacing:.04em;flex-shrink:0}
.s-rencana {background:#f0ece8;color:#5A4A00}
.s-berjalan{background:#e8f5ec;color:#1A3D2B}
.s-selesai {background:#e4ecfd;color:#1A3060}
.s-batal   {background:#fde8e4;color:#7A1A0A}
</style>
@endsection
@section('scripts')
<script>
const _csrfToken = '{{ csrf_token() }}';
const STATUS_LBL = { rencana:'Rencana', berjalan:'Berjalan', selesai:'Selesai', batal:'Batal' };
const BULAN_S = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
let _all = [];

function fmtTgl(d) {
  if (!d) return '—';
  const dt = new Date(d.slice(0,10)+'T00:00:00');
  return `${dt.getDate()} ${BULAN_S[dt.getMonth()]} ${dt.getFullYear()}`;
}
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

async function loadYear() {
  const tahun = document.getElementById('year-sel').value;
  const r = await fetch('/api/program-kerja?tahun=' + tahun);
  const j = await r.json();
  _all = j.success ? j.data : [];
  // Populate bidang datalist
  const bidangs = [...new Set(_all.map(p => p.bidang))];
  document.getElementById('bidang-list').innerHTML = bidangs.map(b => `<option value="${esc(b)}">`).join('');
  renderList();
}

function renderList() {
  const el  = document.getElementById('pk-list');
  const emp = document.getElementById('pk-empty');
  document.getElementById('pk-count').textContent = `${_all.length} program`;
  if (!_all.length) { el.innerHTML = ''; emp.style.display = ''; return; }
  emp.style.display = 'none';

  const groups = {};
  _all.forEach(p => {
    if (!groups[p.bidang]) groups[p.bidang] = [];
    groups[p.bidang].push(p);
  });

  const statusOrder = { berjalan:0, rencana:1, selesai:2, batal:3 };

  el.innerHTML = Object.entries(groups).map(([bidang, items]) => {
    items.sort((a,b) => (statusOrder[a.status]??9) - (statusOrder[b.status]??9) || a.urutan - b.urutan);
    return `
    <div>
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--forest);margin-bottom:.75rem;display:flex;align-items:center;gap:8px">
        <i class="fa fa-folder-open"></i> ${esc(bidang)}
        <span style="flex:1;height:1px;background:var(--border);display:block"></span>
        <span style="font-size:10px;color:var(--ink-mute)">${items.length} program</span>
      </div>
      ${items.map(p => `
        <div class="pk-row">
          <span class="s-badge s-${p.status}">${STATUS_LBL[p.status]||p.status}</span>
          <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:13px;color:var(--ink);margin-bottom:2px">${esc(p.nama)}</div>
            ${p.deskripsi ? `<div style="font-size:12px;color:var(--ink-soft);margin-bottom:4px">${esc(p.deskripsi)}</div>` : ''}
            ${(p.target_mulai||p.target_selesai) ? `<div style="font-size:11px;color:var(--ink-mute)"><i class="fa fa-calendar" style="font-size:10px"></i> ${fmtTgl(p.target_mulai)} — ${fmtTgl(p.target_selesai)}</div>` : ''}
          </div>
          <div style="display:flex;flex-direction:column;gap:5px;flex-shrink:0">
            <button onclick='editProgram(${JSON.stringify(p).replace(/'/g,"&#39;")})' class="btn-secondary" style="padding:6px 12px;font-size:11px"><i class="fa fa-pen"></i></button>
            <button onclick="delProgram(${p.id})" style="padding:6px 12px;font-size:11px;border:1px solid var(--border);border-radius:var(--radius-sm);background:#fff;cursor:pointer;color:var(--rust)"><i class="fa fa-trash"></i></button>
          </div>
        </div>`).join('')}
    </div>`;
  }).join('');
}

function openForm() {
  document.getElementById('f-id').value      = '';
  document.getElementById('f-tahun').value   = document.getElementById('year-sel').value;
  document.getElementById('f-bidang').value  = '';
  document.getElementById('f-nama').value    = '';
  document.getElementById('f-deskripsi').value= '';
  document.getElementById('f-status').value  = 'rencana';
  document.getElementById('f-urutan').value  = '0';
  document.getElementById('f-mulai').value   = '';
  document.getElementById('f-selesai').value = '';
  document.getElementById('form-title').textContent = 'Tambah Program';
  document.getElementById('form-overlay').style.display = 'flex';
}

function closeForm() { document.getElementById('form-overlay').style.display = 'none'; }

function editProgram(p) {
  document.getElementById('f-id').value       = p.id;
  document.getElementById('f-tahun').value    = p.tahun;
  document.getElementById('f-bidang').value   = p.bidang;
  document.getElementById('f-nama').value     = p.nama;
  document.getElementById('f-deskripsi').value= p.deskripsi || '';
  document.getElementById('f-status').value   = p.status;
  document.getElementById('f-urutan').value   = p.urutan;
  document.getElementById('f-mulai').value    = p.target_mulai  ? p.target_mulai.slice(0,10)  : '';
  document.getElementById('f-selesai').value  = p.target_selesai? p.target_selesai.slice(0,10): '';
  document.getElementById('form-title').textContent = 'Edit Program';
  document.getElementById('form-overlay').style.display = 'flex';
}

async function save() {
  const id    = document.getElementById('f-id').value;
  const tahun = parseInt(document.getElementById('f-tahun').value);
  const bidang= document.getElementById('f-bidang').value.trim();
  const nama  = document.getElementById('f-nama').value.trim();
  if (!tahun || !bidang || !nama) { alert('Tahun, bidang, dan nama wajib diisi.'); return; }

  const body = {
    tahun, bidang, nama,
    deskripsi:      document.getElementById('f-deskripsi').value.trim() || null,
    status:         document.getElementById('f-status').value,
    urutan:         parseInt(document.getElementById('f-urutan').value) || 0,
    target_mulai:   document.getElementById('f-mulai').value   || null,
    target_selesai: document.getElementById('f-selesai').value || null,
  };

  const url    = id ? `/api/program-kerja/${id}` : '/api/program-kerja';
  const method = id ? 'PUT' : 'POST';
  const r = await fetch(url, { method, headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken}, body:JSON.stringify(body) });
  const j = await r.json();
  if (j.success) { closeForm(); showToast(j.message); loadYear(); }
  else alert(j.message);
}

async function delProgram(id) {
  if (!confirm('Hapus program kerja ini?')) return;
  const r = await fetch(`/api/program-kerja/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  if (j.success) { showToast(j.message); loadYear(); }
  else alert(j.message);
}

loadYear();
</script>
@endsection
