@extends('layouts.admin')
@section('title', 'Pengeluaran')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <a href="/admin/keuangan" style="font-size:13px;color:var(--ink-soft)">← Keuangan</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Pengeluaran</div>
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="openExport()" class="btn-secondary" style="padding:9px 18px;font-size:13px">↓ Export Excel</button>
      <button onclick="openForm()" class="btn-primary">+ Catat Pengeluaran</button>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card" style="padding:1rem 1.25rem;margin-bottom:1rem">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <input type="text" id="f-search" placeholder="Cari keterangan..." style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none;width:200px">
      <select id="f-kategori" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none">
        <option value="">Semua Kategori</option>
      </select>
      <select id="f-kampanye" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none">
        <option value="">Semua Kampanye</option>
      </select>
      <input type="date" id="f-dari" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
      <input type="date" id="f-sampai" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
      <button onclick="loadData(1)" class="btn-secondary" style="padding:8px 16px;font-size:13px">Filter</button>
      <button onclick="resetFilter()" style="padding:8px 12px;border:none;background:none;font-size:12px;color:var(--ink-soft);cursor:pointer">Reset</button>
    </div>
  </div>

  <div id="data-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
  <div id="data-pagination" style="display:flex;gap:8px;justify-content:center;padding:1rem 0"></div>
</div>

{{-- Form Modal --}}
<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:500px;margin:1rem;max-height:92vh;overflow-y:auto">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem" id="form-title">Catat Pengeluaran</div>
    <input type="hidden" id="edit-id">

    <div style="margin-bottom:1rem">
      <label class="flabel">Keterangan</label>
      <input type="text" id="f-keterangan" placeholder="Deskripsi pengeluaran" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Nominal (Rp)</label>
        <input type="number" id="f-nominal" placeholder="0" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Tanggal</label>
        <input type="date" id="f-tanggal" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Kategori</label>
        <select id="f-kategori-sel" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="">— Tanpa kategori —</option>
        </select>
      </div>
      <div>
        <label class="flabel">Kampanye (opsional)</label>
        <select id="f-kampanye-sel" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="">— Tidak terkait —</option>
        </select>
      </div>
    </div>

    <div style="margin-bottom:1rem">
      <label class="flabel">Pos Anggaran (opsional)</label>
      <select id="f-anggaran" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
        <option value="">— Pilih pos anggaran —</option>
      </select>
    </div>

    <div id="upload-section" style="margin-bottom:1.5rem">
      <label class="flabel">Lampiran Bukti (opsional)</label>
      <div style="border:2px dashed var(--border);border-radius:var(--radius-sm);padding:1rem;text-align:center;cursor:pointer" onclick="document.getElementById('f-bukti').click()">
        <div style="font-size:12px;color:var(--ink-soft)" id="bukti-label">Klik untuk upload bukti (JPG, PNG, PDF)</div>
        <input type="file" id="f-bukti" style="display:none" accept=".jpg,.jpeg,.png,.pdf" onchange="document.getElementById('bukti-label').textContent=this.files[0]?.name||'Klik untuk upload'">
      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button onclick="savePengeluaran()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeForm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

{{-- Lampiran Modal --}}
<div id="lampiran-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:480px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest);margin-bottom:1rem">Lampiran Pengeluaran</div>
    <input type="hidden" id="lampiran-id">
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
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:360px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest);margin-bottom:1.5rem">Export Pengeluaran</div>
    <div style="margin-bottom:1rem">
      <label class="flabel">Tahun</label>
      <input type="number" id="exp-tahun" value="{{ date('Y') }}" min="2020" max="2030" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="doExport()" class="btn-primary" style="flex:1;justify-content:center">Download</button>
      <button onclick="document.getElementById('export-overlay').style.display='none'" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>
<style>.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}</style>
@endsection
@section('scripts')
<script>
let currentPage = 1;
let anggaranData = [];

function rp(n) { return 'Rp '+(parseInt(n)||0).toLocaleString('id-ID'); }

async function loadDeps() {
  const [kRes, kaRes, aRes] = await Promise.all([fetch('/api/kategori'), fetch('/api/kampanye'), fetch('/api/anggaran')]);
  const kj  = await kRes.json();
  const kaj = await kaRes.json();
  const aj  = await aRes.json();

  if (kj.success) {
    document.getElementById('f-kategori').innerHTML   = '<option value="">Semua Kategori</option>' + kj.data.map(k=>`<option value="${k.id}">${k.nama}</option>`).join('');
    document.getElementById('f-kategori-sel').innerHTML = '<option value="">— Tanpa kategori —</option>' + kj.data.map(k=>`<option value="${k.id}">${k.nama}</option>`).join('');
  }
  if (kaj.success) {
    const opts = '<option value="">— Semua —</option>' + kaj.data.map(k=>`<option value="${k.id}">${k.judul}</option>`).join('');
    document.getElementById('f-kampanye').innerHTML   = '<option value="">Semua Kampanye</option>' + kaj.data.map(k=>`<option value="${k.id}">${k.judul}</option>`).join('');
    document.getElementById('f-kampanye-sel').innerHTML = '<option value="">— Tidak terkait —</option>' + kaj.data.map(k=>`<option value="${k.id}">${k.judul}</option>`).join('');
  }
  if (aj.success) {
    anggaranData = aj.data;
    document.getElementById('f-anggaran').innerHTML  = '<option value="">— Pilih pos anggaran —</option>' + aj.data.map(a=>`<option value="${a.id}">${a.pos}${a.kampanye?.judul?' ('+a.kampanye.judul+')':''}</option>`).join('');
  }
}

async function loadData(page = 1) {
  currentPage = page;
  const params = new URLSearchParams({ page,
    search:      document.getElementById('f-search').value,
    kategori_id: document.getElementById('f-kategori').value,
    kampanye_id: document.getElementById('f-kampanye').value,
    dari:        document.getElementById('f-dari').value,
    sampai:      document.getElementById('f-sampai').value,
  });
  Object.keys(Object.fromEntries(params)).forEach(k => { if (!params.get(k)) params.delete(k); });

  document.getElementById('data-list').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-soft)">Memuat...</div>';
  const r = await fetch('/api/pengeluaran?' + params.toString());
  const j = await r.json();

  if (!j.success || !j.data.length) {
    document.getElementById('data-list').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Tidak ada data.</div>';
    document.getElementById('data-pagination').innerHTML = '';
    return;
  }

  const total = j.data.reduce((s,d)=>s+parseInt(d.nominal),0);
  document.getElementById('data-list').innerHTML = `
    <div style="text-align:right;font-size:13px;color:var(--rust);font-weight:600;margin-bottom:6px">Total: ${rp(total)}</div>
    <div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">
        ${['Tanggal','Keterangan','Kategori','Kampanye','Nominal',''].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}
      </tr></thead>
      <tbody>${j.data.map(p=>{
        const hL = p.lampiran && p.lampiran.length > 0;
        const buktiUrl = p.bukti_url;
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft);white-space:nowrap">${p.tanggal}</td>
          <td style="padding:10px 16px;font-size:13px;max-width:220px">
            <div>${p.keterangan}</div>
            ${hL?`<div style="font-size:11px;color:var(--ink-soft)"><i class="fa fa-paperclip"></i> ${p.lampiran.length} lampiran</div>`:''}
            ${buktiUrl&&!hL?`<a href="${buktiUrl}" target="_blank" style="font-size:11px;color:var(--forest)"><i class="fa fa-image"></i> Bukti</a>`:''}
          </td>
          <td style="padding:10px 16px">
            ${p.kategori_nama?`<span style="font-size:11px;padding:2px 8px;border-radius:99px;background:${p.kategori_warna}22;color:${p.kategori_warna};border:1px solid ${p.kategori_warna}55">${p.kategori_nama}</span>`:'<span style="color:var(--ink-mute);font-size:12px">—</span>'}
          </td>
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${p.kampanye_judul||'—'}</td>
          <td style="padding:10px 16px;font-size:13px;font-weight:600;color:var(--rust);white-space:nowrap">− ${rp(p.nominal)}</td>
          <td style="padding:10px 16px;white-space:nowrap">
            <button onclick='openLampiran(${p.id})' title="Lampiran" style="font-size:11px;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;margin-right:4px"><i class="fa fa-paperclip"></i></button>
            <button onclick='editP(${JSON.stringify(p)})' style="font-size:11px;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;margin-right:4px">Edit</button>
            <button onclick='delP(${p.id})' style="font-size:11px;padding:4px 8px;border:1px solid #FDECEA;border-radius:6px;background:#FDECEA;color:var(--rust);cursor:pointer">Hapus</button>
          </td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;

  const meta = j.meta;
  let pagHtml = '';
  if (meta.pages > 1) {
    for (let p = 1; p <= meta.pages; p++) {
      const active = p === meta.page;
      pagHtml += `<button onclick="loadData(${p})" style="padding:6px 12px;border:1.5px solid ${active?'var(--forest)':'var(--border)'};background:${active?'var(--forest)':'#fff'};color:${active?'#fff':'var(--ink-soft)'};border-radius:6px;cursor:pointer;font-size:13px">${p}</button>`;
    }
    pagHtml = `<span style="font-size:13px;color:var(--ink-soft)">${meta.total} pengeluaran</span>` + pagHtml;
  }
  document.getElementById('data-pagination').innerHTML = pagHtml;
}

function resetFilter() {
  ['f-search','f-dari','f-sampai'].forEach(id => document.getElementById(id).value='');
  document.getElementById('f-kategori').value = '';
  document.getElementById('f-kampanye').value = '';
  loadData(1);
}

function openForm() {
  document.getElementById('edit-id').value = '';
  document.getElementById('form-title').textContent = 'Catat Pengeluaran';
  document.getElementById('f-keterangan').value = '';
  document.getElementById('f-nominal').value = '';
  document.getElementById('f-tanggal').value = new Date().toISOString().slice(0,10);
  document.getElementById('f-kategori-sel').value = '';
  document.getElementById('f-kampanye-sel').value = '';
  document.getElementById('f-anggaran').value = '';
  document.getElementById('bukti-label').textContent = 'Klik untuk upload bukti (JPG, PNG, PDF)';
  document.getElementById('f-bukti').value = '';
  document.getElementById('upload-section').style.display = 'block';
  document.getElementById('form-overlay').style.display = 'flex';
}
function closeForm() { document.getElementById('form-overlay').style.display = 'none'; }

function editP(p) {
  document.getElementById('edit-id').value = p.id;
  document.getElementById('form-title').textContent = 'Edit Pengeluaran';
  document.getElementById('f-keterangan').value = p.keterangan;
  document.getElementById('f-nominal').value = p.nominal;
  document.getElementById('f-tanggal').value = p.tanggal;
  document.getElementById('f-kategori-sel').value = p.kategori_id||'';
  document.getElementById('f-kampanye-sel').value = p.kampanye_id||'';
  document.getElementById('f-anggaran').value = p.anggaran_id||'';
  document.getElementById('upload-section').style.display = 'none';
  document.getElementById('form-overlay').style.display = 'flex';
}

async function savePengeluaran() {
  const id = document.getElementById('edit-id').value;
  const fd = new FormData();
  fd.append('keterangan',  document.getElementById('f-keterangan').value.trim());
  fd.append('nominal',     document.getElementById('f-nominal').value);
  fd.append('tanggal',     document.getElementById('f-tanggal').value);
  fd.append('kategori_id', document.getElementById('f-kategori-sel').value);
  fd.append('kampanye_id', document.getElementById('f-kampanye-sel').value);
  fd.append('anggaran_id', document.getElementById('f-anggaran').value);
  fd.append('_token', _csrfToken);
  const bukti = document.getElementById('f-bukti').files[0];
  if (bukti) fd.append('bukti', bukti);

  if (!fd.get('keterangan') || !fd.get('nominal')) { alert('Keterangan dan nominal wajib diisi.'); return; }

  let r;
  if (id) {
    const body = { keterangan: fd.get('keterangan'), nominal: parseInt(fd.get('nominal')), tanggal: fd.get('tanggal'),
                   kategori_id: fd.get('kategori_id')||null, kampanye_id: fd.get('kampanye_id')||null, anggaran_id: fd.get('anggaran_id')||null };
    r = await fetch(`/api/pengeluaran/${id}`, { method:'PUT', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken}, body: JSON.stringify(body) });
  } else {
    r = await fetch('/api/pengeluaran', { method:'POST', body: fd });
  }
  const j = await r.json();
  if (j.success) { closeForm(); showToast(j.message); loadData(currentPage); }
  else alert(j.message);
}

async function delP(id) {
  if (!confirm('Hapus pengeluaran ini?')) return;
  const r = await fetch(`/api/pengeluaran/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  if (j.success) { showToast(j.message); loadData(currentPage); }
  else alert(j.message);
}

async function openLampiran(pId) {
  document.getElementById('lampiran-id').value = pId;
  document.getElementById('lampiran-overlay').style.display = 'flex';
  await refreshLampiran(pId);
}
async function refreshLampiran(pId) {
  const r = await fetch(`/api/lampiran?type=pengeluaran&id=${pId}`);
  const j = await r.json();
  const list = document.getElementById('lampiran-list');
  if (!j.success || !j.data.length) { list.innerHTML = '<div style="color:var(--ink-soft);font-size:13px;margin-bottom:.5rem">Belum ada lampiran.</div>'; return; }
  list.innerHTML = j.data.map(l=>{
    const isImg=l.mime.startsWith('image/');
    return `<div style="display:flex;align-items:center;gap:10px;padding:8px;border:1px solid var(--border);border-radius:8px;margin-bottom:6px">
      <a href="${l.url}" target="_blank" style="flex:1;font-size:13px;color:var(--ink-mid);text-decoration:underline;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
        <i class="fa ${isImg?'fa-image':'fa-file-pdf'}" style="color:var(--forest);margin-right:6px"></i>${l.nama_asli}</a>
      <span style="font-size:11px;color:var(--ink-mute)">${l.ukuran_kb}KB</span>
      <button onclick="delLampiran(${l.id},${pId})" style="font-size:11px;padding:3px 8px;border:1px solid #FDECEA;border-radius:5px;background:#FDECEA;color:var(--rust);cursor:pointer">✕</button>
    </div>`}).join('');
}
async function uploadLampiran() {
  const pId = document.getElementById('lampiran-id').value;
  const file = document.getElementById('lampiran-file').files[0];
  if (!file) return;
  const fd = new FormData(); fd.append('file',file); fd.append('type','pengeluaran'); fd.append('id',pId); fd.append('_token',_csrfToken);
  const r = await fetch('/api/lampiran',{method:'POST',body:fd});
  const j = await r.json();
  if (j.success) { showToast(j.message); await refreshLampiran(pId); loadData(currentPage); }
  else alert(j.message);
  document.getElementById('lampiran-file').value='';
}
async function delLampiran(lid, pId) {
  if (!confirm('Hapus lampiran ini?')) return;
  const r = await fetch(`/api/lampiran/${lid}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':_csrfToken}});
  const j = await r.json();
  if (j.success) { showToast(j.message); await refreshLampiran(pId); }
  else alert(j.message);
}

function openExport() { document.getElementById('export-overlay').style.display='flex'; }
function doExport() {
  const tahun = document.getElementById('exp-tahun').value;
  window.location = '/api/laporan/export/pengeluaran?tahun=' + tahun;
}

loadDeps().then(() => loadData(1));
document.getElementById('f-search').addEventListener('keydown', e => { if(e.key==='Enter') loadData(1); });
</script>
@endsection
