@extends('layouts.app')
@section('title', 'Permohonan Surat')
@section('styles')
<style>
.surat-row{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:14px 0;border-bottom:1px solid var(--border)}
.surat-row:last-child{border-bottom:none}
.surat-status{font-size:11px;padding:3px 11px;border-radius:99px;font-weight:600;white-space:nowrap;flex-shrink:0}
.s-menunggu{background:var(--gold-pale);color:#7A5C00}
.s-diproses{background:#e8f0fd;color:#2d5aa8}
.s-selesai{background:#E8F4ED;color:var(--forest)}
.s-ditolak{background:#FDECEA;color:var(--rust)}
.form-label{font-size:12px;font-weight:600;color:var(--ink-mid);margin-bottom:5px;display:block}
.form-input{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'DM Sans',sans-serif;color:var(--ink);background:#fff;transition:border-color .15s}
.form-input:focus{outline:none;border-color:var(--forest)}
textarea.form-input{resize:vertical;min-height:90px}
.form-row{margin-bottom:1.125rem}
#extra-fields{transition:all .25s}
</style>
@endsection
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container" style="max-width:720px">

  <div style="margin-bottom:2rem">
    <a href="/dashboard" style="font-size:13px;color:var(--ink-soft)">← Dashboard</a>
    <div style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--forest);margin-top:6px">Permohonan Surat</div>
    <p style="font-size:14px;color:var(--ink-soft);margin-top:6px">Ajukan permohonan surat keterangan RT secara online.</p>
  </div>

  {{-- Submission form --}}
  <div class="card" style="margin-bottom:2rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.15rem;color:var(--forest);margin-bottom:1.25rem">
      <i class="fa fa-file-circle-plus" style="margin-right:6px;opacity:.7"></i> Ajukan Permohonan Baru
    </div>

    <div class="form-row">
      <label class="form-label">Jenis Surat <span style="color:var(--rust)">*</span></label>
      <select class="form-input" id="f-jenis" onchange="onJenisChange()">
        <option value="">— Pilih jenis surat —</option>
      </select>
    </div>

    <div class="form-row">
      <label class="form-label">Nama (Atas Nama Surat) <span style="color:var(--rust)">*</span></label>
      <input class="form-input" id="f-atas-nama" placeholder="Nama lengkap sesuai KTP" type="text">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
      <div class="form-row">
        <label class="form-label">NIK</label>
        <input class="form-input" id="f-nik" placeholder="16 digit NIK" maxlength="16" type="text">
      </div>
      <div class="form-row">
        <label class="form-label">Alamat</label>
        <input class="form-input" id="f-alamat" placeholder="Alamat lengkap" type="text">
      </div>
    </div>

    <div id="extra-fields"></div>

    <div class="form-row">
      <label class="form-label">Keperluan / Keterangan <span style="color:var(--rust)">*</span></label>
      <textarea class="form-input" id="f-keperluan" placeholder="Jelaskan keperluan surat ini untuk apa..."></textarea>
    </div>

    <div id="form-msg" style="display:none;font-size:13px;padding:10px 14px;border-radius:8px;margin-bottom:1rem"></div>

    <button id="submit-btn" onclick="submitSurat()" style="background:var(--forest);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 28px;font-size:14px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;width:100%">
      <i class="fa fa-paper-plane"></i> Kirim Permohonan
    </button>
  </div>

  {{-- History --}}
  <div>
    <div style="font-family:'DM Serif Display',serif;font-size:1.15rem;color:var(--forest);margin-bottom:1rem">
      <i class="fa fa-clock-rotate-left" style="margin-right:6px;opacity:.7"></i> Riwayat Permohonan
    </div>
    <div class="card" id="history-list">
      <div style="text-align:center;padding:2rem 0;color:var(--ink-mute);font-size:13px">Memuat...</div>
    </div>
  </div>

</div>
</main>

<script>
const _csrfT = '{{ csrf_token() }}';
let _jenisList = [];

const EXTRA_FIELDS = {
  izin_keramaian: [
    { id:'tanggal_acara', label:'Tanggal Acara', type:'date', required:true },
    { id:'nama_acara', label:'Nama / Jenis Acara', type:'text', placeholder:'Contoh: Pernikahan, Ulang Tahun', required:true },
    { id:'perkiraan_tamu', label:'Perkiraan Jumlah Tamu', type:'number', placeholder:'Jumlah orang' },
  ],
  usaha: [
    { id:'nama_usaha', label:'Nama Usaha', type:'text', placeholder:'Nama usaha / toko', required:true },
    { id:'jenis_usaha', label:'Jenis Usaha', type:'text', placeholder:'Contoh: Warung makan, Toko kelontong' },
  ],
  kematian: [
    { id:'nama_almarhum', label:'Nama Almarhum/ah', type:'text', required:true },
    { id:'tanggal_meninggal', label:'Tanggal Meninggal', type:'date', required:true },
    { id:'hubungan', label:'Hubungan dengan Pemohon', type:'text', placeholder:'Contoh: Ayah, Ibu, Suami' },
  ],
  kelahiran: [
    { id:'nama_bayi', label:'Nama Bayi', type:'text', required:true },
    { id:'tanggal_lahir', label:'Tanggal Lahir', type:'date', required:true },
    { id:'nama_ayah', label:'Nama Ayah', type:'text' },
    { id:'nama_ibu', label:'Nama Ibu', type:'text' },
  ],
  pindah_masuk: [
    { id:'asal_daerah', label:'Asal Daerah / Kota', type:'text', required:true },
    { id:'tanggal_pindah', label:'Tanggal Rencana Pindah', type:'date' },
  ],
  pindah_keluar: [
    { id:'tujuan_daerah', label:'Tujuan Daerah / Kota', type:'text', required:true },
    { id:'tanggal_pindah', label:'Tanggal Rencana Pindah', type:'date' },
  ],
};

async function loadJenis() {
  const r = await fetch('/api/surat/jenis');
  const j = await r.json();
  if (!j.success) return;
  _jenisList = j.data;
  const sel = document.getElementById('f-jenis');
  j.data.forEach(({ key, label }) => {
    const opt = document.createElement('option');
    opt.value = key;
    opt.textContent = label;
    sel.appendChild(opt);
  });
}

function onJenisChange() {
  const jenis = document.getElementById('f-jenis').value;
  const extras = EXTRA_FIELDS[jenis] || [];
  const container = document.getElementById('extra-fields');
  if (!extras.length) { container.innerHTML = ''; return; }

  container.innerHTML = extras.map(f => `
    <div class="form-row">
      <label class="form-label">${f.label}${f.required ? ' <span style="color:var(--rust)">*</span>' : ''}</label>
      <input class="form-input" id="extra_${f.id}" type="${f.type}" ${f.placeholder ? `placeholder="${f.placeholder}"` : ''}>
    </div>
  `).join('');
}

async function loadHistory() {
  const r = await fetch('/api/surat/mine', { headers: { 'X-CSRF-TOKEN': _csrfT } });
  const j = await r.json();
  const container = document.getElementById('history-list');
  if (!j.success || !j.data.length) {
    container.innerHTML = '<div style="text-align:center;padding:2rem 0;color:var(--ink-mute);font-size:13px">Belum ada permohonan surat.</div>';
    return;
  }
  container.innerHTML = j.data.map(s => `
    <div class="surat-row">
      <div style="flex:1;min-width:0">
        <div style="font-weight:600;font-size:14px;color:var(--ink)">${escHtml(s.jenis_label)}</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">a.n. ${escHtml(s.atas_nama)}</div>
        <div style="font-size:12px;color:var(--ink-mute);margin-top:2px">${formatDate(s.created_at)}</div>
        ${s.nomor_surat ? `<div style="font-size:12px;color:var(--forest);margin-top:4px"><i class="fa fa-hashtag" style="opacity:.6"></i> ${escHtml(s.nomor_surat)}</div>` : ''}
        ${s.catatan_admin ? `<div style="font-size:12px;color:var(--ink-soft);margin-top:4px;padding:6px 10px;background:var(--gold-pale);border-radius:6px">${escHtml(s.catatan_admin)}</div>` : ''}
      </div>
      <span class="surat-status s-${s.status}">${statusLabel(s.status)}</span>
    </div>
  `).join('');
}

async function submitSurat() {
  const jenis    = document.getElementById('f-jenis').value;
  const atasNama = document.getElementById('f-atas-nama').value.trim();
  const keperluan = document.getElementById('f-keperluan').value.trim();
  const msg = document.getElementById('form-msg');

  if (!jenis)      { showMsg('Pilih jenis surat terlebih dahulu.', false); return; }
  if (!atasNama)   { showMsg('Nama wajib diisi.', false); return; }
  if (keperluan.length < 5) { showMsg('Keperluan wajib diisi (minimal 5 karakter).', false); return; }

  // Collect extra fields
  const extras = EXTRA_FIELDS[jenis] || [];
  const data_tambahan = {};
  for (const f of extras) {
    const el = document.getElementById(`extra_${f.id}`);
    if (!el) continue;
    const v = el.value.trim();
    if (f.required && !v) { showMsg(`${f.label} wajib diisi.`, false); return; }
    if (v) data_tambahan[f.id] = v;
  }

  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengirim...';

  const body = {
    jenis_surat:   jenis,
    atas_nama:     atasNama,
    nik:           document.getElementById('f-nik').value.trim() || null,
    alamat:        document.getElementById('f-alamat').value.trim() || null,
    keperluan,
    data_tambahan: Object.keys(data_tambahan).length ? data_tambahan : null,
  };

  const r = await fetch('/api/surat', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfT },
    body: JSON.stringify(body),
  });
  const j = await r.json();

  btn.disabled = false;
  btn.innerHTML = '<i class="fa fa-paper-plane"></i> Kirim Permohonan';

  if (j.success) {
    showMsg('Permohonan berhasil dikirim! Pengurus RT akan segera memprosesnya.', true);
    // Reset form
    document.getElementById('f-jenis').value = '';
    document.getElementById('f-atas-nama').value = '';
    document.getElementById('f-nik').value = '';
    document.getElementById('f-alamat').value = '';
    document.getElementById('f-keperluan').value = '';
    document.getElementById('extra-fields').innerHTML = '';
    loadHistory();
  } else {
    showMsg(j.message || 'Gagal mengirim permohonan.', false);
  }
}

function showMsg(text, ok) {
  const el = document.getElementById('form-msg');
  el.textContent = text;
  el.style.background = ok ? '#E8F4ED' : '#FDECEA';
  el.style.color = ok ? 'var(--forest)' : 'var(--rust)';
  el.style.display = '';
  if (ok) setTimeout(() => el.style.display = 'none', 5000);
}

function statusLabel(s) {
  return { menunggu:'Menunggu', diproses:'Diproses', selesai:'Selesai', ditolak:'Ditolak' }[s] || s;
}
function formatDate(dt) {
  if (!dt) return '—';
  return new Date(dt).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
}
function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

loadJenis();
loadHistory();
</script>
@endsection
