@extends('layouts.admin')
@section('title', 'Buku Kas')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <a href="/admin/keuangan" style="font-size:13px;color:var(--ink-soft)">← Keuangan</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Buku Kas</div>
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="openExport()" class="btn-secondary" style="padding:9px 18px;font-size:13px">↓ Export Excel</button>
      <button onclick="openForm()" class="btn-primary">+ Entri Kas</button>
    </div>
  </div>

  {{-- Summary Cards --}}
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
    <div style="background:#E8F4ED;border-radius:var(--radius-sm);padding:1.25rem">
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--forest);opacity:0.7;margin-bottom:4px">Total Masuk</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--forest)" id="s-masuk">—</div>
    </div>
    <div style="background:#FDECEA;border-radius:var(--radius-sm);padding:1.25rem">
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--rust);opacity:0.7;margin-bottom:4px">Total Keluar</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--rust)" id="s-keluar">—</div>
    </div>
    <div style="background:var(--gold-pale);border-radius:var(--radius-sm);padding:1.25rem">
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#7A5C00;opacity:0.7;margin-bottom:4px">Saldo Kas</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:#7A5C00" id="s-saldo">—</div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card" style="padding:1rem 1.25rem;margin-bottom:1rem">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <input type="text" id="f-search" placeholder="Cari keterangan..." style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px;outline:none;width:200px">
      <select id="f-jenis" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none">
        <option value="">Semua Jenis</option>
        <option value="masuk">Masuk</option>
        <option value="keluar">Keluar</option>
      </select>
      <select id="f-kategori" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none">
        <option value="">Semua Kategori</option>
      </select>
      <input type="date" id="f-dari" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
      <input type="date" id="f-sampai" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
      <button onclick="loadKas(1)" class="btn-secondary" style="padding:8px 16px;font-size:13px">Filter</button>
      <button onclick="resetFilter()" style="padding:8px 12px;border:none;background:none;font-size:12px;color:var(--ink-soft);cursor:pointer">Reset</button>
      <select id="f-per-page" onchange="loadKas(1)" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none;margin-left:auto">
        <option value="25">25 / hal</option>
        <option value="50" selected>50 / hal</option>
        <option value="100">100 / hal</option>
      </select>
    </div>
  </div>

  <div id="kas-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
  <div id="kas-pagination" style="display:flex;gap:6px;justify-content:center;align-items:center;padding:1.25rem 0;flex-wrap:wrap"></div>
</div>

{{-- Form Modal --}}
<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:480px;margin:1rem;max-height:90vh;overflow-y:auto">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem" id="form-title">Entri Kas</div>
    <input type="hidden" id="edit-id" value="">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div onclick="setJenis('masuk')" data-jenis="masuk"
        style="padding:12px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:#fff;color:var(--ink-soft);text-align:center;cursor:pointer;font-size:14px;font-weight:500">
        <i class="fa-solid fa-arrow-trend-up" style="margin-right:5px"></i>Masuk
      </div>
      <div onclick="setJenis('keluar')" data-jenis="keluar"
        style="padding:12px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:#fff;color:var(--ink-soft);text-align:center;cursor:pointer;font-size:14px;font-weight:500">
        <i class="fa-solid fa-arrow-trend-down" style="margin-right:5px"></i>Keluar
      </div>
    </div>
    <input type="hidden" id="f-jenis-val" value="masuk">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Tanggal</label>
        <input type="date" id="f-tanggal" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Nominal (Rp)</label>
        <input type="number" id="f-nominal" placeholder="0" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
    </div>

    <div style="margin-bottom:1rem">
      <label class="flabel">Keterangan</label>
      <input type="text" id="f-keterangan" placeholder="Deskripsi transaksi" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">
      <div>
        <label class="flabel">Kategori</label>
        <select id="f-kategori-sel" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="">— Tanpa kategori —</option>
        </select>
      </div>
      <div>
        <label class="flabel">Kampanye (opsional)</label>
        <select id="f-kampanye" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="">— Tidak terkait —</option>
        </select>
      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button onclick="saveKas()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeForm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

{{-- Lampiran Modal --}}
<div id="lampiran-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:480px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest);margin-bottom:1rem">Lampiran Dokumen</div>
    <input type="hidden" id="lampiran-kas-id">
    <div id="lampiran-list" style="margin-bottom:1rem"></div>
    <div style="border:2px dashed var(--border);border-radius:var(--radius-sm);padding:1.5rem;text-align:center;cursor:pointer" onclick="document.getElementById('lampiran-file').click()">
      <div style="font-size:13px;color:var(--ink-soft)">Klik untuk upload (JPG, PNG, PDF, maks 5MB)</div>
      <input type="file" id="lampiran-file" style="display:none" accept=".jpg,.jpeg,.png,.pdf" onchange="uploadLampiran()">
    </div>
    <button onclick="document.getElementById('lampiran-overlay').style.display='none'" class="btn-secondary" style="width:100%;justify-content:center;margin-top:1rem">Tutup</button>
  </div>
</div>

{{-- Export Modal --}}
<div id="export-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:380px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest);margin-bottom:1.5rem">Export Kas Excel</div>
    <div style="margin-bottom:1rem">
      <label class="flabel">Dari Tanggal</label>
      <input type="date" id="exp-dari" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
    </div>
    <div style="margin-bottom:1.5rem">
      <label class="flabel">Sampai Tanggal</label>
      <input type="date" id="exp-sampai" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="doExport()" class="btn-primary" style="flex:1;justify-content:center">Download</button>
      <button onclick="document.getElementById('export-overlay').style.display='none'" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

<style>
.flabel { display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft);margin-bottom:5px }
</style>
@endsection
@section('scripts')
<script>
let currentPage = 1;
let kategoriData = [];
let kampanyeData = [];

function rp(n) { return 'Rp ' + (parseInt(n)||0).toLocaleString('id-ID'); }

async function loadDeps() {
  const [kRes, kaRes] = await Promise.all([fetch('/api/kategori'), fetch('/api/kampanye')]);
  const kj  = await kRes.json();
  const kaj = await kaRes.json();

  if (kj.success) {
    kategoriData = kj.data;
    const opts = '<option value="">— Semua Kategori —</option>' + kj.data.map(k=>`<option value="${k.id}">${k.nama}</option>`).join('');
    document.getElementById('f-kategori').innerHTML = '<option value="">Semua Kategori</option>' + kj.data.map(k=>`<option value="${k.id}">${k.nama}</option>`).join('');
    document.getElementById('f-kategori-sel').innerHTML = '<option value="">— Tanpa kategori —</option>' + kj.data.map(k=>`<option value="${k.id}">${k.nama}</option>`).join('');
  }
  if (kaj.success) {
    kampanyeData = kaj.data;
    document.getElementById('f-kampanye').innerHTML = '<option value="">— Tidak terkait —</option>' + kaj.data.map(k=>`<option value="${k.id}">${k.judul}</option>`).join('');
  }
}

async function loadSummary() {
  const r = await fetch('/api/kas/summary');
  const j = await r.json();
  if (j.success) {
    document.getElementById('s-masuk').textContent  = rp(j.data.masuk);
    document.getElementById('s-keluar').textContent = rp(j.data.keluar);
    document.getElementById('s-saldo').textContent  = rp(j.data.saldo);
  }
}

async function loadKas(page = 1) {
  currentPage = page;
  const params = new URLSearchParams({
    page,
    per_page:    document.getElementById('f-per-page').value,
    search:      document.getElementById('f-search').value,
    jenis:       document.getElementById('f-jenis').value,
    kategori_id: document.getElementById('f-kategori').value,
    dari:        document.getElementById('f-dari').value,
    sampai:      document.getElementById('f-sampai').value,
  });
  Object.keys(Object.fromEntries(params)).forEach(k => { if (!params.get(k)) params.delete(k); });

  document.getElementById('kas-list').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-soft)">Memuat...</div>';
  const r = await fetch('/api/kas?' + params.toString());
  const j = await r.json();

  if (!j.success || !j.data.length) {
    document.getElementById('kas-list').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Tidak ada data.</div>';
    document.getElementById('kas-pagination').innerHTML = '';
    return;
  }

  document.getElementById('kas-list').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">
        ${['Tanggal','Keterangan','Kategori','Kampanye','Jenis','Nominal',''].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}
      </tr></thead>
      <tbody>${j.data.map(k=>{
        const tgl   = (k.tanggal||k.created_at||'').slice(0,10);
        const masuk = k.jenis==='masuk';
        const hasLampiran = k.lampiran && k.lampiran.length > 0;
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft);white-space:nowrap">${tgl}</td>
          <td style="padding:10px 16px;font-size:13px;max-width:240px">
            <div>${k.keterangan}</div>
            ${hasLampiran ? `<div style="font-size:11px;color:var(--ink-soft)"><i class="fa fa-paperclip"></i> ${k.lampiran.length} lampiran</div>` : ''}
          </td>
          <td style="padding:10px 16px">
            ${k.kategori_nama ? `<span style="font-size:11px;padding:2px 8px;border-radius:99px;background:${k.kategori_warna}22;color:${k.kategori_warna};border:1px solid ${k.kategori_warna}55">${k.kategori_nama}</span>` : '<span style="color:var(--ink-mute);font-size:12px">—</span>'}
          </td>
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${k.kampanye_judul||'—'}</td>
          <td style="padding:10px 16px">
            <span style="font-size:11px;padding:2px 9px;border-radius:99px;background:${masuk?'#E8F4ED':'#FDECEA'};color:${masuk?'var(--forest)':'var(--rust)'}">${masuk?'Masuk':'Keluar'}</span>
          </td>
          <td style="padding:10px 16px;font-size:13px;font-weight:600;color:${masuk?'var(--forest)':'var(--rust)'};white-space:nowrap">${masuk?'+':'−'} ${rp(k.nominal)}</td>
          <td style="padding:10px 16px;white-space:nowrap">
            <button onclick='openLampiran(${k.id})' title="Lampiran" style="font-size:11px;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;margin-right:4px"><i class="fa fa-paperclip"></i></button>
            <button onclick='editKas(${JSON.stringify(k)})' style="font-size:11px;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;margin-right:4px">Edit</button>
            ${!k.donasi_id ? `<button onclick='delKas(${k.id})' style="font-size:11px;padding:4px 8px;border:1px solid #FDECEA;border-radius:6px;background:#FDECEA;color:var(--rust);cursor:pointer">Hapus</button>` : ''}
          </td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;

  // Pagination
  const meta = j.meta;
  document.getElementById('kas-pagination').innerHTML = renderPagination(meta.page, meta.pages, meta.total);
}

function renderPagination(cur, total, rowCount) {
  if (total <= 1) return `<span style="font-size:12px;color:var(--ink-mute)">${rowCount} transaksi</span>`;

  const btnStyle = (active) =>
    `padding:6px 11px;border:1.5px solid ${active?'var(--forest)':'var(--border)'};background:${active?'var(--forest)':'#fff'};color:${active?'#fff':'var(--ink-soft)'};border-radius:6px;cursor:${active?'default':'pointer'};font-size:13px;font-family:'DM Sans',sans-serif;`;
  const navStyle = (disabled) =>
    `padding:6px 11px;border:1.5px solid var(--border);background:#fff;color:${disabled?'var(--border)':'var(--ink-soft)'};border-radius:6px;cursor:${disabled?'default':'pointer'};font-size:13px;font-family:'DM Sans',sans-serif;`;

  const pages = [];
  // Always show: first, last, current ± 2
  const show = new Set([1, total, cur, cur-1, cur-2, cur+1, cur+2].filter(p => p >= 1 && p <= total));
  const sorted = [...show].sort((a,b) => a-b);

  let html = `<span style="font-size:12px;color:var(--ink-mute);margin-right:4px">${rowCount} transaksi</span>`;
  html += `<button onclick="loadKas(${cur-1})" ${cur===1?'disabled':''} style="${navStyle(cur===1)}">&#8249;</button>`;

  let prev = 0;
  for (const p of sorted) {
    if (p - prev > 1) html += `<span style="padding:6px 4px;color:var(--ink-mute);font-size:13px">…</span>`;
    const active = p === cur;
    html += `<button onclick="${active?'':('loadKas('+p+')')}" style="${btnStyle(active)}">${p}</button>`;
    prev = p;
  }

  html += `<button onclick="loadKas(${cur+1})" ${cur===total?'disabled':''} style="${navStyle(cur===total)}">&#8250;</button>`;
  return html;
}

function resetFilter() {
  ['f-search','f-dari','f-sampai'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('f-jenis').value = '';
  document.getElementById('f-kategori').value = '';
  loadKas(1);
}

function setJenis(v) {
  document.getElementById('f-jenis-val').value = v;
  document.querySelectorAll('[data-jenis]').forEach(el => {
    const sel = el.dataset.jenis === v;
    el.style.border  = `1.5px solid ${sel ? (v==='masuk'?'var(--forest)':'var(--rust)') : 'var(--border)'}`;
    el.style.background = sel ? (v==='masuk'?'#F0F7F3':'#FFF0EE') : '#fff';
    el.style.color    = sel ? (v==='masuk'?'var(--forest)':'var(--rust)') : 'var(--ink-soft)';
  });
}

function openForm() {
  document.getElementById('edit-id').value = '';
  document.getElementById('form-title').textContent = 'Entri Kas Manual';
  document.getElementById('f-tanggal').value = new Date().toISOString().slice(0,10);
  document.getElementById('f-nominal').value = '';
  document.getElementById('f-keterangan').value = '';
  document.getElementById('f-kategori-sel').value = '';
  document.getElementById('f-kampanye').value = '';
  setJenis('masuk');
  document.getElementById('form-overlay').style.display = 'flex';
}
function closeForm() { document.getElementById('form-overlay').style.display = 'none'; }

function editKas(k) {
  document.getElementById('edit-id').value = k.id;
  document.getElementById('form-title').textContent = 'Edit Entri Kas';
  document.getElementById('f-tanggal').value = (k.tanggal||k.created_at||'').slice(0,10);
  document.getElementById('f-nominal').value = k.nominal;
  document.getElementById('f-keterangan').value = k.keterangan;
  document.getElementById('f-kategori-sel').value = k.kategori_id||'';
  document.getElementById('f-kampanye').value = k.kampanye_id||'';
  setJenis(k.jenis);
  document.getElementById('form-overlay').style.display = 'flex';
}

async function saveKas() {
  const id   = document.getElementById('edit-id').value;
  const body = {
    jenis:       document.getElementById('f-jenis-val').value,
    tanggal:     document.getElementById('f-tanggal').value,
    nominal:     parseInt(document.getElementById('f-nominal').value),
    keterangan:  document.getElementById('f-keterangan').value.trim(),
    kategori_id: document.getElementById('f-kategori-sel').value || null,
    kampanye_id: document.getElementById('f-kampanye').value || null,
  };
  if (!body.keterangan || !body.nominal) { alert('Keterangan dan nominal wajib diisi.'); return; }

  const url = id ? `/api/kas/${id}` : '/api/kas';
  const method = id ? 'PUT' : 'POST';
  const r = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken }, body: JSON.stringify(body) });
  const j = await r.json();
  if (j.success) { closeForm(); showToast(j.message); loadKas(currentPage); loadSummary(); }
  else alert(j.message);
}

async function delKas(id) {
  if (!confirm('Hapus entri kas ini?')) return;
  const r = await fetch(`/api/kas/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrfToken } });
  const j = await r.json();
  if (j.success) { showToast(j.message); loadKas(currentPage); loadSummary(); }
  else alert(j.message);
}

async function openLampiran(kasId) {
  document.getElementById('lampiran-kas-id').value = kasId;
  document.getElementById('lampiran-overlay').style.display = 'flex';
  await refreshLampiranList(kasId);
}

async function refreshLampiranList(kasId) {
  const r = await fetch(`/api/lampiran?type=kas&id=${kasId}`);
  const j = await r.json();
  const list = document.getElementById('lampiran-list');
  if (!j.success || !j.data.length) {
    list.innerHTML = '<div style="color:var(--ink-soft);font-size:13px;margin-bottom:0.5rem">Belum ada lampiran.</div>';
    return;
  }
  list.innerHTML = j.data.map(l => {
    const isImg = l.mime.startsWith('image/');
    return `<div style="display:flex;align-items:center;gap:10px;padding:8px;border:1px solid var(--border);border-radius:8px;margin-bottom:6px">
      <a href="${l.url}" target="_blank" style="flex:1;font-size:13px;color:var(--ink-mid);text-decoration:underline;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
        <i class="fa ${isImg?'fa-image':'fa-file-pdf'}" style="color:var(--forest);margin-right:6px"></i>${l.nama_asli}
      </a>
      <span style="font-size:11px;color:var(--ink-mute)">${l.ukuran_kb}KB</span>
      <button onclick="delLampiran(${l.id},${kasId})" style="font-size:11px;padding:3px 8px;border:1px solid #FDECEA;border-radius:5px;background:#FDECEA;color:var(--rust);cursor:pointer"><i class="fa-solid fa-xmark"></i></button>
    </div>`;
  }).join('');
}

async function uploadLampiran() {
  const kasId = document.getElementById('lampiran-kas-id').value;
  const file  = document.getElementById('lampiran-file').files[0];
  if (!file) return;
  const fd = new FormData();
  fd.append('file', file);
  fd.append('type', 'kas');
  fd.append('id', kasId);
  fd.append('_token', _csrfToken);
  const r = await fetch('/api/lampiran', { method: 'POST', body: fd });
  const j = await r.json();
  if (j.success) { showToast(j.message); await refreshLampiranList(kasId); loadKas(currentPage); }
  else alert(j.message);
  document.getElementById('lampiran-file').value = '';
}

async function delLampiran(lampiranId, kasId) {
  if (!confirm('Hapus lampiran ini?')) return;
  const r = await fetch(`/api/lampiran/${lampiranId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrfToken } });
  const j = await r.json();
  if (j.success) { showToast(j.message); await refreshLampiranList(kasId); }
  else alert(j.message);
}

function openExport() { document.getElementById('export-overlay').style.display = 'flex'; }
function doExport() {
  const dari   = document.getElementById('exp-dari').value;
  const sampai = document.getElementById('exp-sampai').value;
  const params = new URLSearchParams();
  if (dari)   params.set('dari', dari);
  if (sampai) params.set('sampai', sampai);
  window.location = '/api/laporan/export/kas?' + params.toString();
}

// Init
loadDeps().then(() => { loadSummary(); loadKas(1); });
document.getElementById('f-search').addEventListener('keydown', e => { if (e.key === 'Enter') loadKas(1); });
</script>
@endsection
