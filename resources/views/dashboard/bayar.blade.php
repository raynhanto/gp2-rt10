@extends('layouts.app')
@section('title', 'Selesaikan Pembayaran')
@section('styles')
<style>
@media(max-width:768px){
  #bayar-summary-grid{grid-template-columns:1fr!important}
  .metode-grid{grid-template-columns:1fr 1fr 1fr!important}
}
</style>
@endsection
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container" style="max-width:600px">

  <div style="margin-bottom:2rem">
    <a href="/dashboard/riwayat" style="font-size:13px;color:var(--ink-soft)">← Riwayat Donasi</a>
    <div style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--forest);margin-top:6px">Selesaikan Pembayaran</div>
    <p style="font-size:14px;color:var(--ink-soft);margin-top:6px">Pilih cara bayar dan upload bukti untuk menyelesaikan donasi kamu.</p>
  </div>

  <div id="bayar-container">
    <div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div>
  </div>

</div>
</main>
@endsection
@section('scripts')
<script>
const _donasiId  = {{ (int) request('donasi', 0) }};
let _donasi      = null;
let _settings    = {};
let selectedFile = null;
let selectedMetode = null;

const METODE = [
  { id: 'qris',     icon: 'fa-solid fa-qrcode',   label: 'QRIS' },
  { id: 'transfer', icon: 'fa-solid fa-building-columns', label: 'Transfer' },
  { id: 'gopay',    icon: 'fa-solid fa-wallet',   label: 'GoPay/OVO' },
];

async function loadBayar() {
  if (!_donasiId) { showError('ID donasi tidak valid.'); return; }
  const [donasiRes, settingsRes] = await Promise.all([
    fetch('/api/user/donasi'),
    fetch('/api/settings'),
  ]);
  const donasiData   = await donasiRes.json();
  const settingsData = await settingsRes.json();
  if (!donasiData.success) { showError('Gagal memuat data donasi.'); return; }
  _donasi   = donasiData.data.find(d => d.id === _donasiId);
  _settings = settingsData.success ? settingsData.data : {};
  if (!_donasi) { showError('Donasi tidak ditemukan.'); return; }
  selectedMetode = _donasi.metode || 'qris';
  renderBayar();
}

function showError(msg) {
  document.getElementById('bayar-container').innerHTML = `
    <div class="card" style="text-align:center;padding:2.5rem">
      <div style="font-size:36px;margin-bottom:10px;color:var(--ink-soft)"><i class="fa-solid fa-circle-exclamation"></i></div>
      <div style="font-size:15px;font-weight:500;color:var(--ink-mid);margin-bottom:1rem">${msg}</div>
      <a href="/dashboard/riwayat" class="btn-secondary" style="display:inline-flex">← Riwayat Donasi</a>
    </div>`;
}

function renderBayar() {
  const d = _donasi;
  const statusLabel = { pending: 'Menunggu Pembayaran', verified: 'Terverifikasi', rejected: 'Ditolak' };
  const statusColor = { pending: '#7A5C00', verified: 'var(--forest)', rejected: 'var(--rust)' };
  const statusBg    = { pending: 'var(--gold-pale)', verified: '#E8F4ED', rejected: '#FDECEA' };
  const tgl = new Date(d.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });

  document.getElementById('bayar-container').innerHTML = `
    <div class="card" style="margin-bottom:1.25rem">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:1rem">
        <div style="font-family:'DM Serif Display',serif;font-size:1.1rem;color:var(--forest);line-height:1.3;flex:1">${d.judul || 'Kampanye'}</div>
        <span style="font-size:11px;padding:3px 11px;border-radius:99px;background:${statusBg[d.status]};color:${statusColor[d.status]};font-weight:600;white-space:nowrap;flex-shrink:0">${statusLabel[d.status] || d.status}</span>
      </div>
      <div id="bayar-summary-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div style="background:var(--cream);border-radius:var(--radius-sm);padding:10px 14px">
          <div style="font-size:11px;color:var(--ink-soft);margin-bottom:3px">Nominal</div>
          <div style="font-size:1.15rem;font-weight:700;color:var(--forest)">Rp ${parseInt(d.nominal).toLocaleString('id-ID')}</div>
        </div>
        <div style="background:var(--cream);border-radius:var(--radius-sm);padding:10px 14px" id="metode-summary-chip">
          <div style="font-size:11px;color:var(--ink-soft);margin-bottom:3px">Metode</div>
          <div style="font-size:13px;font-weight:600;color:var(--ink)" id="metode-summary-label">—</div>
        </div>
      </div>
      <div style="margin-top:10px;font-size:11px;color:var(--ink-mute)">
        <i class="fa fa-clock" style="margin-right:3px"></i>${tgl} &middot; #${d.id}
      </div>
    </div>

    ${d.status === 'pending' ? renderPendingFlow(d) : ''}
    ${d.status === 'verified' ? renderVerified() : ''}
    ${d.status === 'rejected' ? renderRejected(d.catatan_admin) : ''}
  `;

  if (d.status === 'pending') {
    renderMetodeChips(selectedMetode);
    renderPaymentInfo(selectedMetode, d.nominal);
    const fi = document.getElementById('file-input');
    if (fi) fi.addEventListener('change', e => handleFile(e.target.files[0]));
  }
}

function renderPendingFlow(d) {
  const hasBukti = !!d.bukti_url;
  return `
    <!-- Step 1: Pilih metode -->
    <div class="card" style="margin-bottom:1.25rem">
      <div style="font-size:11px;font-weight:700;color:var(--forest);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:1rem">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:var(--forest);color:#fff;border-radius:50%;font-size:10px;margin-right:7px">1</span>Pilih Cara Bayar
      </div>
      <div class="metode-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:1.25rem" id="metode-chips"></div>
      <div id="payment-info"></div>
    </div>

    <!-- Step 2: Upload bukti -->
    <div class="card" id="upload-card">
      <div style="font-size:11px;font-weight:700;color:var(--forest);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:1rem">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:var(--forest);color:#fff;border-radius:50%;font-size:10px;margin-right:7px">2</span>Konfirmasi Pembayaran
      </div>

      ${hasBukti ? `
        <div style="display:flex;align-items:center;gap:10px;background:#E8F4ED;border-radius:var(--radius-sm);padding:12px 14px;margin-bottom:1rem">
          <div style="font-size:18px;color:var(--forest)"><i class="fa-solid fa-circle-check"></i></div>
          <div style="flex:1">
            <div style="font-size:13px;font-weight:600;color:var(--forest)">Bukti sudah diupload</div>
            <div style="font-size:12px;color:var(--ink-soft)">Menunggu verifikasi pengurus RT</div>
          </div>
          <a href="${d.bukti_url}" target="_blank" style="font-size:12px;color:var(--forest-light);font-weight:500;white-space:nowrap">
            <i class="fa fa-image" style="margin-right:3px"></i>Lihat
          </a>
        </div>
        <button onclick="toggleReupload()" id="reupload-btn"
          style="font-size:12px;padding:7px 16px;border-radius:99px;border:1.5px solid var(--border);background:#fff;color:var(--ink-soft);cursor:pointer;font-family:'DM Sans',sans-serif;margin-bottom:1rem">
          Ganti bukti
        </button>
        <div id="upload-form" style="display:none">
      ` : `<div id="upload-form">`}

      <div id="drop-zone"
        style="border:2px dashed var(--sand);border-radius:var(--radius-sm);padding:1.75rem;text-align:center;cursor:pointer;transition:all 0.2s;margin-bottom:1rem;background:var(--cream)"
        onclick="document.getElementById('file-input').click()"
        ondragover="event.preventDefault();this.style.borderColor='var(--forest-light)';this.style.background='#F0F7F3'"
        ondragleave="this.style.borderColor='var(--sand)';this.style.background='var(--cream)'"
        ondrop="handleDrop(event)">
        <div id="drop-content">
          <div style="font-size:28px;margin-bottom:6px;color:var(--ink-soft)"><i class="fa-solid fa-camera"></i></div>
          <div style="font-size:13px;font-weight:500;color:var(--ink-mid)">Klik atau drag foto bukti ke sini</div>
          <div style="font-size:12px;color:var(--ink-soft);margin-top:3px">JPG, PNG, PDF · Maks 5MB</div>
        </div>
        <img id="preview-img" src="" alt="" style="display:none;max-width:100%;max-height:200px;border-radius:var(--radius-sm);object-fit:contain">
      </div>
      <input type="file" id="file-input" accept="image/*,.pdf" style="display:none">

      <div style="background:var(--gold-pale);border-radius:var(--radius-sm);padding:10px 12px;font-size:12px;color:#7A5C00;margin-bottom:1rem;line-height:1.6">
        <i class="fa-solid fa-lightbulb" style="color:var(--gold-dark);margin-right:5px"></i>Pastikan nominal, tanggal, dan nama tujuan terlihat jelas di screenshot.
      </div>

      <div style="display:grid;grid-template-columns:1fr auto;gap:8px">
        <button onclick="uploadBukti()" class="btn-primary" style="justify-content:center" id="upload-btn">
          <i class="fa fa-upload" style="margin-right:6px"></i>Kirim Bukti
        </button>
        <a id="wa-btn" href="#" target="_blank"
          style="display:inline-flex;align-items:center;gap:7px;padding:12px 18px;border-radius:100px;border:1.5px solid #25D366;background:#fff;color:#128C3E;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif;white-space:nowrap;text-decoration:none">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          Chat Admin
        </a>
      </div>

      </div><!-- /upload-form -->
    </div>
  `;
}

function renderMetodeChips(active) {
  const container = document.getElementById('metode-chips');
  if (!container) return;
  container.innerHTML = METODE.map(m => {
    const isActive = m.id === active;
    return `<div onclick="selectMetode('${m.id}')" data-metode="${m.id}"
      style="padding:10px 8px;border:1.5px solid ${isActive ? 'var(--forest)' : 'var(--border)'};border-radius:var(--radius-sm);text-align:center;cursor:pointer;transition:all 0.15s;background:${isActive ? '#F0F7F3' : '#fff'};color:${isActive ? 'var(--forest)' : 'var(--ink-soft)'}">
      <div style="font-size:18px;margin-bottom:4px"><i class="${m.icon}"></i></div>
      <div style="font-size:12px;font-weight:600">${m.label}</div>
    </div>`;
  }).join('');
  updateMetodeSummaryLabel(active);
}

function updateMetodeSummaryLabel(metode) {
  const el = document.getElementById('metode-summary-label');
  if (el) {
    const m = METODE.find(x => x.id === metode);
    el.textContent = m ? m.label : '—';
  }
}

async function selectMetode(metode) {
  if (metode === selectedMetode) return;
  selectedMetode = metode;

  document.querySelectorAll('[data-metode]').forEach(el => {
    const active = el.dataset.metode === metode;
    el.style.borderColor = active ? 'var(--forest)' : 'var(--border)';
    el.style.background  = active ? '#F0F7F3' : '#fff';
    el.style.color       = active ? 'var(--forest)' : 'var(--ink-soft)';
  });
  updateMetodeSummaryLabel(metode);
  renderPaymentInfo(metode, _donasi.nominal);

  await fetch(`/api/donasi/${_donasiId}/metode`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken },
    body: JSON.stringify({ metode })
  });
}

function renderPaymentInfo(metode, nominal) {
  const box = document.getElementById('payment-info');
  if (!box) return;
  const s = _settings;

  if (metode === 'qris') {
    const qrisImg = s.qris_url
      ? `<img src="/${s.qris_url}" alt="QRIS" style="width:120px;height:120px;object-fit:contain;border-radius:var(--radius-sm)">`
      : `<div style="width:120px;height:120px;flex-shrink:0;background:var(--cream);border-radius:var(--radius-sm);border:2px dashed var(--sand);display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:28px;color:var(--ink-soft)"><i class="fa-solid fa-qrcode"></i><div style="font-size:9px;color:var(--ink-soft);margin-top:4px;text-align:center;line-height:1.4">QR Code<br>segera hadir</div></div>`;
    box.innerHTML = `
      <div style="display:flex;gap:1.25rem;align-items:center;padding-top:1rem;border-top:1px solid var(--border)">
        <div style="flex-shrink:0">${qrisImg}</div>
        <div>
          <div style="font-size:13px;font-weight:600;color:var(--ink);margin-bottom:8px">QRIS Universal</div>
          <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px">
            ${['GoPay','OVO','Dana','ShopeePay'].map(a=>`<span style="background:var(--forest-pale);color:var(--forest);padding:2px 9px;border-radius:99px;font-size:11px;font-weight:500">${a}</span>`).join('')}
          </div>
          <div style="font-size:12px;color:var(--ink-soft)">Atau mobile banking manapun</div>
        </div>
      </div>`;
  } else if (metode === 'transfer') {
    const bankName   = s.bank_name    || 'BCA';
    const bankAcct   = s.bank_account || '—';
    const bankHolder = s.bank_holder  || '—';
    box.innerHTML = `
      <div style="padding-top:1rem;border-top:1px solid var(--border)">
        <div style="background:var(--cream);border-radius:var(--radius-sm);padding:12px 14px;margin-bottom:8px">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
            <span style="font-size:11px;font-weight:700;color:var(--forest-mid);letter-spacing:0.05em">${bankName}</span>
            <button onclick="copyNorek('${bankAcct}',this)"
              style="font-size:11px;padding:3px 10px;border-radius:99px;border:1px solid var(--border);background:#fff;color:var(--forest);cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:500">Salin</button>
          </div>
          <div style="font-size:15px;font-weight:700;color:var(--ink);letter-spacing:0.04em;margin-bottom:2px">${bankAcct}</div>
          <div style="font-size:12px;color:var(--ink-soft)">a.n. ${bankHolder}</div>
        </div>
        <div style="background:var(--gold-pale);border-radius:var(--radius-sm);padding:10px 12px;font-size:12px;color:#7A5C00;line-height:1.6">
          <i class="fa fa-triangle-exclamation" style="margin-right:4px"></i>Transfer tepat <strong>Rp ${parseInt(nominal).toLocaleString('id-ID')}</strong> agar verifikasi lebih cepat.
        </div>
      </div>`;
  } else {
    box.innerHTML = `
      <div style="padding-top:1rem;border-top:1px solid var(--border);font-size:13px;color:var(--ink-soft);line-height:1.7">
        Bayar via <strong>GoPay / OVO</strong> ke nomor yang diberikan pengurus RT, atau scan QRIS di atas. Hubungi admin via WhatsApp jika butuh bantuan.
      </div>`;
  }
  updateWaLink();
}

function updateWaLink() {
  const btn = document.getElementById('wa-btn');
  if (!btn || !_donasi) return;
  const waNum = _settings.whatsapp || '';
  const metodeLabel = { qris: 'QRIS', transfer: 'Transfer Bank', gopay: 'GoPay/OVO' };
  const msg = encodeURIComponent(
    `Halo Pengurus GP2 RT10,\n\nSaya ingin konfirmasi donasi:\n` +
    `• Kampanye: ${_donasi.judul || '-'}\n` +
    `• Nominal: Rp ${parseInt(_donasi.nominal).toLocaleString('id-ID')}\n` +
    `• Metode: ${metodeLabel[selectedMetode] || selectedMetode}\n` +
    `• ID Donasi: #${_donasi.id}\n\n` +
    `Mohon bantuannya. Terima kasih!`
  );
  btn.href = waNum ? `https://wa.me/${waNum}?text=${msg}` : '#';
}

function renderVerified() {
  return `
    <div class="card" style="background:linear-gradient(135deg,#E8F4ED,#F2FAF5);border-color:rgba(26,61,43,0.18);text-align:center;padding:2.5rem">
      <div style="font-size:44px;margin-bottom:12px;color:var(--forest)"><i class="fa-solid fa-circle-check"></i></div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest);margin-bottom:6px">Donasi Terverifikasi</div>
      <div style="font-size:13px;color:var(--ink-soft);line-height:1.7;margin-bottom:1.5rem;max-width:300px;margin-left:auto;margin-right:auto">Donasi kamu sudah diterima dan tercatat di kas RT 10. Terima kasih!</div>
      <a href="/dashboard/riwayat" class="btn-primary" style="display:inline-flex">Lihat Riwayat →</a>
    </div>`;
}

function renderRejected(catatan) {
  return `
    <div class="card" style="border-color:rgba(181,64,26,0.2);text-align:center;padding:2.5rem">
      <div style="font-size:44px;margin-bottom:12px;color:var(--rust)"><i class="fa-solid fa-circle-xmark"></i></div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--rust);margin-bottom:8px">Donasi Ditolak</div>
      ${catatan ? `<div style="font-size:13px;background:#FDECEA;color:var(--rust);padding:10px 14px;border-radius:var(--radius-sm);margin-bottom:1rem;line-height:1.6">Catatan pengurus: ${catatan}</div>` : ''}
      <div style="font-size:13px;color:var(--ink-soft);margin-bottom:1.5rem">Silakan buat donasi baru atau hubungi pengurus RT.</div>
      <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
        <a href="/donasi" class="btn-primary" style="display:inline-flex">Donasi Lagi →</a>
        <a href="${_settings.whatsapp ? 'https://wa.me/' + _settings.whatsapp : '#'}" target="_blank"
          style="display:inline-flex;align-items:center;gap:7px;padding:12px 20px;border-radius:100px;border:1.5px solid #25D366;background:#fff;color:#128C3E;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          Chat Admin
        </a>
      </div>
    </div>`;
}

function toggleReupload() {
  const form = document.getElementById('upload-form');
  const btn  = document.getElementById('reupload-btn');
  const open = form.style.display !== 'none';
  form.style.display = open ? 'none' : 'block';
  btn.textContent = open ? 'Ganti bukti' : 'Batal';
}

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('drop-zone').style.borderColor = 'var(--sand)';
  document.getElementById('drop-zone').style.background  = 'var(--cream)';
  handleFile(e.dataTransfer.files[0]);
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
      <div style="font-size:28px;margin-bottom:6px;color:var(--ink-soft)"><i class="fa-solid fa-file"></i></div>
      <div style="font-size:13px;font-weight:500;color:var(--forest)">${file.name}</div>
      <div style="font-size:12px;color:var(--ink-soft);margin-top:4px">${(file.size / 1024).toFixed(0)} KB</div>`;
  }
}

async function uploadBukti() {
  if (!selectedFile) { alert('Pilih file bukti pembayaran terlebih dahulu.'); return; }
  const btn = document.getElementById('upload-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin" style="margin-right:6px"></i>Mengirim...';

  const formData = new FormData();
  formData.append('bukti', selectedFile);
  formData.append('_token', _csrfToken);

  try {
    const res  = await fetch(`/api/donasi/${_donasiId}/bukti`, { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      showToast('Bukti berhasil diupload! Menunggu verifikasi pengurus.');
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

function copyNorek(norek, btn) {
  navigator.clipboard.writeText(norek).then(() => {
    const orig = btn.textContent;
    btn.textContent      = 'Tersalin';
    btn.style.background = 'var(--forest)';
    btn.style.color      = '#fff';
    btn.style.borderColor = 'var(--forest)';
    setTimeout(() => {
      btn.textContent      = orig;
      btn.style.background = '#fff';
      btn.style.color      = 'var(--forest)';
      btn.style.borderColor = 'var(--border)';
    }, 2000);
  });
}

loadBayar();
</script>
@endsection
