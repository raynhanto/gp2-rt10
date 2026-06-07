@extends('layouts.app')
@section('title', 'Konfirmasi Pembayaran')
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container" style="max-width:640px">

  <div style="margin-bottom:2rem">
    <a href="/dashboard/riwayat" style="font-size:13px;color:var(--ink-soft)">← Riwayat Donasi</a>
    <div style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--forest);margin-top:6px">Konfirmasi Pembayaran</div>
    <p style="font-size:14px;color:var(--ink-soft);margin-top:6px">Upload screenshot bukti transfer atau QRIS kamu.</p>
  </div>

  <div id="pending-list" style="margin-bottom:1.5rem">
    <div style="text-align:center;padding:2rem;color:var(--ink-soft)">Memuat...</div>
  </div>

  <div class="card" id="upload-form" style="display:none">
    <div style="font-size:15px;font-weight:500;color:var(--ink);margin-bottom:4px">Upload bukti untuk:</div>
    <div style="font-family:'DM Serif Display',serif;font-size:1.1rem;color:var(--forest);margin-bottom:1.25rem" id="selected-kampanye">—</div>

    <div id="drop-zone"
      style="border:2px dashed var(--sand);border-radius:var(--radius);padding:2rem;text-align:center;cursor:pointer;transition:all 0.2s;margin-bottom:1.25rem;background:var(--cream)"
      onclick="document.getElementById('file-input').click()"
      ondragover="event.preventDefault();this.style.borderColor='var(--forest-light)';this.style.background='#F0F7F3'"
      ondragleave="this.style.borderColor='var(--sand)';this.style.background='var(--cream)'"
      ondrop="handleDrop(event)">
      <div id="drop-content">
        <div style="font-size:32px;margin-bottom:8px">📸</div>
        <div style="font-size:14px;font-weight:500;color:var(--ink-mid)">Klik atau drag foto bukti ke sini</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:4px">JPG, PNG, PDF · Maks 5MB</div>
      </div>
      <img id="preview-img" src="" alt="" style="display:none;max-width:100%;max-height:200px;border-radius:var(--radius-sm);object-fit:contain">
    </div>
    <input type="file" id="file-input" accept="image/*,.pdf" style="display:none" onchange="handleFile(this.files[0])">

    <div style="background:var(--gold-pale);border-radius:var(--radius-sm);padding:12px 14px;font-size:13px;color:#7A5C00;margin-bottom:1.25rem;line-height:1.6">
      💡 Pastikan nominal, tanggal, dan nama tujuan terlihat jelas di screenshot.
    </div>

    <div style="display:flex;gap:8px">
      <button onclick="uploadBukti()" class="btn-primary" style="flex:1;justify-content:center" id="upload-btn">
        <i class="fa fa-upload" style="margin-right:6px"></i>Kirim Bukti
      </button>
      <button onclick="cancelUpload()" class="btn-secondary">Batal</button>
    </div>
  </div>

  <div class="card" style="margin-top:1.5rem;background:var(--cream);border:none">
    <div style="font-size:14px;font-weight:500;color:var(--forest);margin-bottom:8px">📱 Belum transfer?</div>
    <p style="font-size:13px;color:var(--ink-soft);line-height:1.7;margin-bottom:1rem">Scan QRIS di halaman <a href="/donasi" style="color:var(--forest-light);font-weight:500">Donasi</a> atau transfer ke rekening pengurus RT. Setelah transfer, upload screenshot di sini.</p>
    <div style="font-size:12px;color:var(--ink-soft)">Pertanyaan? Hubungi pengurus RT via WA.</div>
  </div>

</div>
</main>
@endsection
@section('scripts')
<script>
const _autoSelectId = {{ (int)request('donasi', 0) }};
let selectedDonasi = null;
let selectedFile = null;

async function loadPending() {
  const res = await fetch('/api/user/donasi');
  const data = await res.json();
  if (!data.success) return;

  const pending = data.data.filter(d => d.status === 'pending');

  if (!pending.length) {
    document.getElementById('pending-list').innerHTML = `
      <div class="card" style="text-align:center;padding:2rem">
        <div style="font-size:32px;margin-bottom:8px">✅</div>
        <div style="font-size:15px;font-weight:500;color:var(--forest);margin-bottom:6px">Tidak ada donasi pending</div>
        <div style="font-size:13px;color:var(--ink-soft);margin-bottom:1rem">Semua donasi kamu sudah diverifikasi.</div>
        <a href="/donasi" class="btn-primary" style="display:inline-flex">Donasi Lagi →</a>
      </div>`;
    return;
  }

  document.getElementById('pending-list').innerHTML = `
    <div style="font-size:13px;font-weight:500;color:var(--ink-soft);margin-bottom:8px">Pilih donasi yang ingin dikonfirmasi:</div>
    ${pending.map(d => `
      <div onclick="selectDonasi(${d.id}, '${(d.judul||'Kampanye').replace(/'/g,"\\'")}', ${d.nominal})"
        style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-sm);margin-bottom:8px;cursor:pointer;transition:all 0.15s"
        id="ditem-${d.id}"
        onmouseenter="this.style.borderColor='var(--forest-light)'"
        onmouseleave="this.style.borderColor=selectedDonasi&&selectedDonasi.id==${d.id}?'var(--forest)':'var(--border)'">
        <div>
          <div style="font-size:14px;font-weight:500;color:var(--ink)">${d.judul || 'Kampanye'}</div>
          <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">
            ${new Date(d.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'})}
            ${d.bukti_url ? ' · <span style="color:var(--gold)">Bukti sudah diupload</span>' : ''}
          </div>
        </div>
        <div style="text-align:right;flex-shrink:0">
          <div style="font-size:14px;font-weight:600;color:var(--forest)">Rp ${parseInt(d.nominal).toLocaleString('id-ID')}</div>
          <div style="font-size:11px;color:#7A5C00;background:var(--gold-pale);padding:2px 8px;border-radius:99px;margin-top:3px">Menunggu</div>
        </div>
      </div>`).join('')}`;

  if (_autoSelectId) {
    const target = pending.find(d => d.id === _autoSelectId);
    if (target) selectDonasi(target.id, target.judul || 'Kampanye', target.nominal);
  }
}

function selectDonasi(id, judul, nominal) {
  selectedDonasi = { id, judul, nominal };
  document.querySelectorAll('[id^="ditem-"]').forEach(el => el.style.borderColor = 'var(--border)');
  const el = document.getElementById('ditem-' + id);
  if (el) el.style.borderColor = 'var(--forest)';
  document.getElementById('selected-kampanye').textContent = judul + ' — Rp ' + parseInt(nominal).toLocaleString('id-ID');
  document.getElementById('upload-form').style.display = 'block';
  document.getElementById('upload-form').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function cancelUpload() {
  selectedDonasi = null;
  selectedFile = null;
  document.getElementById('upload-form').style.display = 'none';
  document.getElementById('preview-img').style.display = 'none';
  document.getElementById('drop-content').style.display = 'block';
  document.getElementById('file-input').value = '';
  document.querySelectorAll('[id^="ditem-"]').forEach(el => el.style.borderColor = 'var(--border)');
}

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('drop-zone').style.borderColor = 'var(--sand)';
  document.getElementById('drop-zone').style.background = 'var(--cream)';
  const file = e.dataTransfer.files[0];
  if (file) handleFile(file);
}

function handleFile(file) {
  if (!file) return;
  selectedFile = file;
  if (file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('preview-img').src = e.target.result;
      document.getElementById('preview-img').style.display = 'block';
      document.getElementById('drop-content').style.display = 'none';
    };
    reader.readAsDataURL(file);
  } else {
    document.getElementById('drop-content').innerHTML = `
      <div style="font-size:32px;margin-bottom:8px">📄</div>
      <div style="font-size:14px;font-weight:500;color:var(--forest)">${file.name}</div>
      <div style="font-size:12px;color:var(--ink-soft);margin-top:4px">${(file.size/1024).toFixed(0)} KB</div>`;
  }
}

async function uploadBukti() {
  if (!selectedDonasi) { alert('Pilih donasi terlebih dahulu.'); return; }
  if (!selectedFile)   { alert('Pilih file bukti pembayaran.'); return; }

  const btn = document.getElementById('upload-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin" style="margin-right:6px"></i>Mengirim...';

  const formData = new FormData();
  formData.append('bukti', selectedFile);
  formData.append('_token', _csrfToken);

  try {
    const res = await fetch(`/api/donasi/${selectedDonasi.id}/bukti`, { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      showToast('✅ Bukti berhasil diupload! Menunggu verifikasi pengurus.');
      setTimeout(() => location.href = '/dashboard/riwayat', 2000);
    } else {
      alert(data.message || 'Gagal mengupload.');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-upload" style="margin-right:6px"></i>Kirim Bukti';
    }
  } catch(e) {
    alert('Koneksi gagal. Coba lagi.');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-upload" style="margin-right:6px"></i>Kirim Bukti';
  }
}

loadPending();
</script>
@endsection
