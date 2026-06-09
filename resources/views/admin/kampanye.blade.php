@extends('layouts.admin')
@section('title', 'Kelola Kampanye')
@section('styles')
<style>
#deskripsi-editor:empty::before{content:attr(data-placeholder);color:var(--ink-mute);pointer-events:none}
.rtb-btn{padding:4px 9px;border:1px solid var(--border);background:#fff;border-radius:5px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--ink-mid);line-height:1.5;transition:background 0.12s}
.rtb-btn:hover{background:var(--forest-pale);color:var(--forest)}
.rtb-sep{width:1px;background:var(--border);margin:0 3px;align-self:stretch}
#foto-zone:hover{border-color:var(--forest-light);background:var(--forest-pale)}
</style>
@endsection
@section('content')
<div class="container">
  <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Kelola Kampanye</div>
    </div>
    <button onclick="openForm()" class="btn-primary">+ Kampanye Baru</button>
  </div>

  <div id="kampanye-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
</div>

<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:620px;margin:1rem;max-height:92vh;overflow-y:auto">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem" id="form-title">Kampanye Baru</div>
    <input type="hidden" id="edit-id">

    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Judul Kampanye</label>
      <input type="text" id="judul" placeholder="Judul kampanye"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;box-sizing:border-box">
    </div>

    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Deskripsi</label>
      <div style="display:flex;gap:3px;align-items:center;padding:6px 8px;background:var(--cream);border:1.5px solid var(--border);border-bottom:none;border-radius:var(--radius-sm) var(--radius-sm) 0 0">
        <button type="button" class="rtb-btn" onclick="fmt('bold')" title="Bold"><b>B</b></button>
        <button type="button" class="rtb-btn" onclick="fmt('italic')" title="Italic"><i>I</i></button>
        <button type="button" class="rtb-btn" onclick="fmt('underline')" title="Underline" style="text-decoration:underline">U</button>
        <div class="rtb-sep"></div>
        <button type="button" class="rtb-btn" onclick="fmt('insertUnorderedList')" title="Bullet list">• List</button>
        <button type="button" class="rtb-btn" onclick="fmt('insertOrderedList')" title="Numbered list">1. List</button>
        <div class="rtb-sep"></div>
        <button type="button" class="rtb-btn" onclick="fmt('removeFormat')" title="Hapus format" style="color:var(--ink-soft)"><i class="fa-solid fa-xmark"></i> Format</button>
      </div>
      <div id="deskripsi-editor" contenteditable="true"
        data-placeholder="Jelaskan kebutuhan dan rencana penggunaan dana..."
        style="width:100%;min-height:110px;padding:11px 14px;border:1.5px solid var(--border);border-top:none;border-radius:0 0 var(--radius-sm) var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;line-height:1.65;box-sizing:border-box"></div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Target</label>
        <div style="position:relative">
          <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:13px;font-weight:600;color:var(--ink-soft);pointer-events:none">Rp</span>
          <input type="text" inputmode="numeric" id="target" placeholder="0" autocomplete="off"
            oninput="onTargetInput(this)"
            style="width:100%;padding:11px 14px 11px 36px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;outline:none;box-sizing:border-box">
        </div>
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Deadline</label>
        <input type="date" id="deadline"
          style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
      </div>
    </div>

    <div style="margin-bottom:1.25rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Status</label>
      <select id="status" style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;background:#fff">
        <option value="aktif">Aktif</option>
        <option value="urgent">Mendesak</option>
        <option value="selesai">Selesai</option>
        <option value="arsip">Arsip</option>
      </select>
    </div>

    <div style="margin-bottom:1.5rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Foto Kampanye</label>
      <div id="foto-zone" onclick="document.getElementById('foto-input').click()"
        style="border:1.5px dashed var(--border);border-radius:var(--radius-sm);padding:1rem;text-align:center;cursor:pointer;background:#fff;transition:all 0.15s;min-height:88px;display:flex;align-items:center;justify-content:center;overflow:hidden;gap:1rem;flex-wrap:wrap">
        <img id="foto-preview" src="" alt="" style="display:none;max-height:140px;max-width:100%;border-radius:8px;object-fit:cover">
        <div id="foto-placeholder" style="color:var(--ink-soft)">
          <div style="font-size:22px;margin-bottom:6px;color:var(--ink-mute)"><i class="fa-solid fa-image"></i></div>
          <div style="font-size:13px;font-weight:500">Klik untuk pilih foto kampanye</div>
          <div style="font-size:11px;color:var(--ink-mute);margin-top:3px">JPG, PNG, WebP · Maks 5MB</div>
        </div>
      </div>
      <input type="file" id="foto-input" accept="image/jpeg,image/png,image/webp" style="display:none" onchange="previewFoto(this)">
      <div id="foto-change-hint" style="display:none;font-size:11px;color:var(--ink-mute);margin-top:5px;text-align:center">Klik foto untuk ganti gambar</div>
    </div>

    <div style="display:flex;gap:8px">
      <button onclick="saveKampanye()" class="btn-primary" id="save-btn" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeForm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
let editMode = false;

function fmtRp(n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function onTargetInput(el) {
  const raw = el.value.replace(/\D/g, '');
  el.value  = raw ? fmtRp(parseInt(raw)) : '';
}

function fmt(cmd) {
  document.getElementById('deskripsi-editor').focus();
  document.execCommand(cmd, false, null);
}

function stripHtml(html) {
  return html.replace(/<[^>]*>/g, '').trim();
}

function previewFoto(input) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = document.getElementById('foto-preview');
    img.src = e.target.result;
    img.style.display = 'block';
    document.getElementById('foto-placeholder').style.display = 'none';
    document.getElementById('foto-change-hint').style.display = 'block';
  };
  reader.readAsDataURL(file);
}

async function loadKampanye() {
  const res = await fetch('/api/kampanye');
  const data = await res.json();
  if (!data.success) return;
  const colors = {urgent:'badge-urgent',aktif:'badge-open',selesai:'badge-done',arsip:'badge-done'};
  const labels = {urgent:'Mendesak',aktif:'Aktif',selesai:'Selesai',arsip:'Arsip'};
  document.getElementById('kampanye-list').innerHTML = `
    <div class="card" style="padding:0;overflow:hidden">
      <table style="width:100%;border-collapse:collapse">
        <thead><tr style="background:var(--cream)">
          ${['','Judul','Target','Terkumpul','Status','Deadline',''].map(h=>`<th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--ink-soft)">${h}</th>`).join('')}
        </tr></thead>
        <tbody>${data.data.map(k => {
          const pct = Math.min(100, Math.round(k.terkumpul / k.target * 100));
          const dl  = k.deadline ? new Date(k.deadline).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'}) : '—';
          const thumb = k.foto_url
            ? `<img src="${k.foto_url}" style="width:40px;height:40px;object-fit:cover;border-radius:7px;display:block">`
            : `<div style="width:40px;height:40px;background:var(--cream);border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:15px;color:var(--forest-mid)"><i class="fa-solid fa-bullhorn"></i></div>`;
          return `<tr style="border-top:1px solid var(--border)">
            <td style="padding:10px 12px 10px 16px">${thumb}</td>
            <td style="padding:10px 16px">
              <div style="font-size:13px;font-weight:500;color:var(--forest)">${k.judul}</div>
              <div style="font-size:11px;color:var(--ink-soft);max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${stripHtml(k.deskripsi).substring(0,64)}…</div>
            </td>
            <td style="padding:10px 16px;font-size:13px">Rp ${parseInt(k.target).toLocaleString('id-ID')}</td>
            <td style="padding:10px 16px">
              <div style="font-size:13px;font-weight:600;color:var(--forest)">Rp ${parseInt(k.terkumpul).toLocaleString('id-ID')}</div>
              <div style="font-size:11px;color:var(--ink-soft)">${pct}%</div>
            </td>
            <td style="padding:10px 16px"><span class="badge ${colors[k.status]||'badge-open'}">${labels[k.status]||k.status}</span></td>
            <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${dl}</td>
            <td style="padding:10px 16px">
              <button onclick='editKampanye(${JSON.stringify(k).replace(/'/g,"&#39;")})' style="padding:6px 14px;border-radius:99px;border:1.5px solid var(--border);background:#fff;font-size:12px;cursor:pointer;font-family:'DM Sans',sans-serif">Edit</button>
            </td>
          </tr>`;
        }).join('')}</tbody>
      </table>
    </div>`;
}

function resetFoto() {
  document.getElementById('foto-input').value = '';
  document.getElementById('foto-preview').src = '';
  document.getElementById('foto-preview').style.display = 'none';
  document.getElementById('foto-placeholder').style.display = 'block';
  document.getElementById('foto-change-hint').style.display = 'none';
}

function openForm() {
  editMode = false;
  document.getElementById('form-title').textContent = 'Kampanye Baru';
  document.getElementById('edit-id').value = '';
  document.getElementById('judul').value = '';
  document.getElementById('deskripsi-editor').innerHTML = '';
  document.getElementById('target').value = '';
  document.getElementById('deadline').value = '';
  document.getElementById('status').value = 'aktif';
  resetFoto();
  document.getElementById('form-overlay').style.display = 'flex';
}

function editKampanye(k) {
  editMode = true;
  document.getElementById('form-title').textContent = 'Edit Kampanye';
  document.getElementById('edit-id').value = k.id;
  document.getElementById('judul').value = k.judul;
  document.getElementById('deskripsi-editor').innerHTML = k.deskripsi || '';
  document.getElementById('target').value = k.target ? fmtRp(parseInt(k.target)) : '';
  document.getElementById('deadline').value = k.deadline ? k.deadline.substring(0, 10) : '';
  document.getElementById('status').value = k.status;
  resetFoto();
  if (k.foto_url) {
    document.getElementById('foto-preview').src = k.foto_url;
    document.getElementById('foto-preview').style.display = 'block';
    document.getElementById('foto-placeholder').style.display = 'none';
    document.getElementById('foto-change-hint').style.display = 'block';
  }
  document.getElementById('form-overlay').style.display = 'flex';
}

function closeForm() { document.getElementById('form-overlay').style.display = 'none'; }

async function saveKampanye() {
  const id   = document.getElementById('edit-id').value;
  const desc = document.getElementById('deskripsi-editor').innerHTML.trim();
  const body = {
    judul:     document.getElementById('judul').value.trim(),
    deskripsi: desc,
    target:    parseInt(document.getElementById('target').value.replace(/\D/g, '') || '0'),
    deadline:  document.getElementById('deadline').value || null,
    status:    document.getElementById('status').value,
  };
  if (!body.judul || !stripHtml(body.deskripsi) || !body.target) {
    alert('Judul, deskripsi, dan target wajib diisi.');
    return;
  }

  const btn = document.getElementById('save-btn');
  btn.disabled = true; btn.textContent = 'Menyimpan…';

  try {
    const url    = editMode ? '/api/kampanye/' + id : '/api/kampanye';
    const method = editMode ? 'PUT' : 'POST';
    const res    = await fetch(url, { method, headers: {'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken}, body: JSON.stringify(body) });
    const data   = await res.json();
    if (!data.success) { alert(data.message); return; }

    const fotoInput = document.getElementById('foto-input');
    if (fotoInput.files.length > 0) {
      const kampanyeId = editMode ? id : data.data.id;
      const fd = new FormData();
      fd.append('foto', fotoInput.files[0]);
      fd.append('_token', _csrfToken);
      const fr   = await fetch('/api/kampanye/' + kampanyeId + '/foto', { method: 'POST', body: fd });
      const fdat = await fr.json();
      if (!fdat.success) showToast('Kampanye disimpan, foto gagal: ' + fdat.message);
    }

    closeForm();
    showToast(data.message);
    loadKampanye();
  } finally {
    btn.disabled = false; btn.textContent = 'Simpan';
  }
}

document.getElementById('form-overlay').addEventListener('click', e => { if (e.target === e.currentTarget) closeForm(); });
loadKampanye();
</script>
@endsection
