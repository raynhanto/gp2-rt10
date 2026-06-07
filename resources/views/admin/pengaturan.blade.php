@extends('layouts.admin')

@section('title', 'Pengaturan — Admin')

@section('content')
<style>
.pg-wrap { max-width: 860px; }
.pg-section {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}
.pg-section-title {
  font-size: 13px;
  font-weight: 700;
  color: var(--ink);
  margin-bottom: 1.25rem;
  padding-bottom: .75rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: .5rem;
}
.pg-section-title i { color: var(--primary); font-size: 14px; }
.pg-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.pg-field label { font-size: 12px; font-weight: 600; color: var(--ink-mid); display: block; margin-bottom: .35rem; }
.pg-field input {
  width: 100%;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 6px;
  color: var(--ink);
  font-size: 13px;
  padding: .5rem .75rem;
  box-sizing: border-box;
}
.pg-field input:focus { outline: none; border-color: var(--primary); }
.pg-qris-row { display: flex; gap: 1.5rem; align-items: flex-start; }
.pg-qris-preview {
  width: 140px;
  height: 140px;
  border: 2px dashed var(--border);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
  background: var(--bg);
  cursor: pointer;
  position: relative;
}
.pg-qris-preview img { width: 100%; height: 100%; object-fit: contain; }
.pg-qris-preview .pg-qris-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .5rem;
  color: var(--ink-mute);
  font-size: 12px;
  text-align: center;
  padding: 1rem;
}
.pg-qris-preview .pg-qris-placeholder i { font-size: 28px; }
.pg-qris-preview:hover { border-color: var(--primary); }
.pg-qris-fields { flex: 1; }
.pg-save-row { display: flex; justify-content: flex-end; margin-top: 1.25rem; }
.msg-ok { font-size: 13px; color: var(--success); display: none; align-items: center; gap: .4rem; }
.msg-err { font-size: 13px; color: var(--danger); display: none; }
</style>

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Pengaturan</div>
    <div class="admin-page-sub">Konfigurasi informasi RT, pembayaran, dan QRIS</div>
  </div>
</div>

<div class="pg-wrap">

  {{-- QRIS & Pembayaran --}}
  <div class="pg-section">
    <div class="pg-section-title">
      <i class="fa fa-qrcode"></i> QRIS &amp; Pembayaran
    </div>
    <div class="pg-qris-row">
      <div class="pg-qris-preview" id="qrisBox" onclick="document.getElementById('qrisFile').click()" title="Klik untuk ganti gambar QRIS">
        <div class="pg-qris-placeholder" id="qrisPlaceholder">
          <i class="fa fa-qrcode"></i>
          <span>Belum ada<br>gambar QRIS</span>
        </div>
        <img id="qrisImg" src="" alt="QRIS" style="display:none">
      </div>
      <div class="pg-qris-fields">
        <div style="font-size:12px;color:var(--ink-mid);margin-bottom:1rem">
          Upload gambar QRIS statis untuk ditampilkan di halaman donasi.<br>
          Format: JPG atau PNG, maks 5MB.
        </div>
        <input type="file" id="qrisFile" accept="image/jpeg,image/png" style="display:none" onchange="previewQris(this)">
        <div class="pg-grid" style="grid-template-columns:1fr 1fr 1fr; margin-top:.75rem">
          <div class="pg-field">
            <label>Nama Bank</label>
            <input type="text" id="bank_name" placeholder="BCA">
          </div>
          <div class="pg-field">
            <label>No. Rekening</label>
            <input type="text" id="bank_account" placeholder="1234567890">
          </div>
          <div class="pg-field">
            <label>Atas Nama</label>
            <input type="text" id="bank_holder" placeholder="Kas RT 10 GP2">
          </div>
        </div>
      </div>
    </div>
    <div class="pg-save-row">
      <span class="msg-ok" id="msgPay"><i class="fa fa-circle-check"></i> Tersimpan</span>
      <span class="msg-err" id="msgPayErr"></span>
      <button class="btn-primary" style="margin-left:1rem" onclick="savePayment()">
        <i class="fa fa-floppy-disk"></i> Simpan
      </button>
    </div>
  </div>

  {{-- WhatsApp --}}
  <div class="pg-section">
    <div class="pg-section-title">
      <i class="fa fa-phone"></i> Kontak
    </div>
    <div class="pg-grid">
      <div class="pg-field" style="grid-column:1/2">
        <label>Nomor WhatsApp Pengurus RT</label>
        <input type="text" id="whatsapp" placeholder="6281234567890" style="max-width:280px">
        <div style="font-size:11px;color:var(--ink-mute);margin-top:.3rem">Format internasional tanpa +, contoh: 6281234567890</div>
      </div>
    </div>
    <div class="pg-save-row">
      <span class="msg-ok" id="msgContact"><i class="fa fa-circle-check"></i> Tersimpan</span>
      <span class="msg-err" id="msgContactErr"></span>
      <button class="btn-primary" style="margin-left:1rem" onclick="saveContact()">
        <i class="fa fa-floppy-disk"></i> Simpan
      </button>
    </div>
  </div>

  {{-- Informasi RT --}}
  <div class="pg-section">
    <div class="pg-section-title">
      <i class="fa fa-house"></i> Informasi RT
    </div>
    <div class="pg-grid">
      <div class="pg-field">
        <label>Nama RT</label>
        <input type="text" id="rt_name" placeholder="RT 10 Golden Park 2">
      </div>
      <div class="pg-field">
        <label>Alamat</label>
        <input type="text" id="rt_address" placeholder="Jl. Golden Park 2, Serang, Banten">
      </div>
    </div>
    <div class="pg-save-row">
      <span class="msg-ok" id="msgRt"><i class="fa fa-circle-check"></i> Tersimpan</span>
      <span class="msg-err" id="msgRtErr"></span>
      <button class="btn-primary" style="margin-left:1rem" onclick="saveRt()">
        <i class="fa fa-floppy-disk"></i> Simpan
      </button>
    </div>
  </div>

</div>

<script>
const _csrf = '{{ csrf_token() }}';
let _qrisFile = null;

async function loadSettings() {
  const r = await fetch('/api/settings');
  const j = await r.json();
  if (!j.success) return;
  const s = j.data;
  if (s.qris_url) {
    document.getElementById('qrisImg').src = '/' + s.qris_url;
    document.getElementById('qrisImg').style.display = '';
    document.getElementById('qrisPlaceholder').style.display = 'none';
  }
  ['bank_name','bank_account','bank_holder','whatsapp','rt_name','rt_address'].forEach(k => {
    const el = document.getElementById(k);
    if (el && s[k]) el.value = s[k];
  });
}

function previewQris(input) {
  if (!input.files[0]) return;
  _qrisFile = input.files[0];
  const url = URL.createObjectURL(_qrisFile);
  document.getElementById('qrisImg').src = url;
  document.getElementById('qrisImg').style.display = '';
  document.getElementById('qrisPlaceholder').style.display = 'none';
}

function showMsg(id, ok, errMsg) {
  const okEl  = document.getElementById('msg' + id);
  const errEl = document.getElementById('msg' + id + 'Err');
  if (ok) {
    okEl.style.display = 'flex';
    errEl.style.display = 'none';
    setTimeout(() => okEl.style.display = 'none', 3000);
  } else {
    errEl.textContent = errMsg || 'Gagal menyimpan.';
    errEl.style.display = '';
    okEl.style.display = 'none';
  }
}

async function savePayment() {
  const fd = new FormData();
  fd.append('_token', _csrf);
  fd.append('bank_name',    document.getElementById('bank_name').value.trim());
  fd.append('bank_account', document.getElementById('bank_account').value.trim());
  fd.append('bank_holder',  document.getElementById('bank_holder').value.trim());
  if (_qrisFile) fd.append('qris', _qrisFile);

  const r = await fetch('/api/settings', { method: 'POST', body: fd });
  const j = await r.json();
  showMsg('Pay', j.success, j.message);
  if (j.success && j.data && j.data.qris_url) {
    document.getElementById('qrisImg').src = '/' + j.data.qris_url;
  }
}

async function saveContact() {
  const fd = new FormData();
  fd.append('_token', _csrf);
  fd.append('whatsapp', document.getElementById('whatsapp').value.trim());
  const r = await fetch('/api/settings', { method: 'POST', body: fd });
  const j = await r.json();
  showMsg('Contact', j.success, j.message);
}

async function saveRt() {
  const fd = new FormData();
  fd.append('_token', _csrf);
  fd.append('rt_name',    document.getElementById('rt_name').value.trim());
  fd.append('rt_address', document.getElementById('rt_address').value.trim());
  const r = await fetch('/api/settings', { method: 'POST', body: fd });
  const j = await r.json();
  showMsg('Rt', j.success, j.message);
}

loadSettings();
</script>
@endsection
