@extends('layouts.app')
@section('title', 'Iuran Bulanan')
@section('styles')
<style>
.tagihan-row{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border)}
.tagihan-row:last-child{border-bottom:none}
.status-badge{font-size:11px;padding:3px 11px;border-radius:99px;font-weight:600;white-space:nowrap}
.status-belum{background:var(--gold-pale);color:#7A5C00}
.status-pending{background:#FFF3CD;color:#856404}
.status-lunas{background:#E8F4ED;color:var(--forest)}
.status-dispensasi{background:#F3F3F3;color:var(--ink-soft)}
.status-rejected{background:#FDECEA;color:var(--rust)}
.metode-btn{display:flex;flex-direction:column;align-items:center;gap:4px;padding:12px 10px;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;background:none;font-family:'DM Sans',sans-serif;font-size:12px;color:var(--ink-mid);transition:all 0.18s;min-width:0}
.metode-btn:hover{border-color:var(--forest);background:var(--forest-pale)}
.metode-btn.active{border-color:var(--forest);background:var(--forest-pale);color:var(--forest);font-weight:600}
#modal-overlay{display:none;position:fixed;inset:0;background:rgba(10,20,14,.55);z-index:200;align-items:center;justify-content:center;padding:1rem}
#modal-overlay.open{display:flex}
#modal-box{background:var(--warm);border-radius:var(--radius);padding:2rem;max-width:480px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-lg)}
#file-drop{border:2px dashed var(--border-gold);border-radius:var(--radius-sm);padding:1.5rem;text-align:center;cursor:pointer;transition:border-color 0.18s,background 0.18s}
#file-drop:hover,#file-drop.drag{border-color:var(--forest);background:var(--forest-pale)}
@media(max-width:600px){
  .tagihan-row{flex-direction:column;align-items:flex-start;gap:8px}
  .tagihan-actions{width:100%;display:flex;justify-content:flex-end}
}
</style>
@endsection
@section('content')
@php
  $isAllowed = auth()->user()->isAdmin();
@endphp
<main style="padding:5rem 0 3rem">
<div class="container" style="max-width:720px">

  <div style="margin-bottom:2rem">
    <a href="/dashboard" style="font-size:13px;color:var(--ink-soft)">← Dashboard</a>
    <div style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--forest);margin-top:6px">Iuran Bulanan</div>
    <p style="font-size:14px;color:var(--ink-soft);margin-top:6px">Status iuran bulanan unit rumah Anda.</p>
  </div>

  @if(!$isAllowed)
  <div class="card" style="text-align:center;padding:3rem">
    <div style="font-size:40px;margin-bottom:12px;color:var(--ink-soft)"><i class="fa-solid fa-lock"></i></div>
    <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest);margin-bottom:8px">Fitur Belum Tersedia</div>
    <p style="font-size:14px;color:var(--ink-soft)">Fitur iuran mandiri sedang dalam tahap uji coba. Hubungi pengurus RT jika ingin membayar iuran.</p>
  </div>
  @else

  <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem">
    <div style="font-size:13px;color:var(--ink-soft)">Tahun:</div>
    <select id="tahun-select" style="padding:7px 14px;border:1.5px solid var(--border);border-radius:99px;background:var(--warm);color:var(--ink);font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer">
      <option value="{{ date('Y') }}">{{ date('Y') }}</option>
    </select>
    <div id="loading-hint" style="font-size:12px;color:var(--ink-soft)">Memuat...</div>
  </div>

  <div id="summary-cards" style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
    <div style="background:var(--cream);border-radius:var(--radius-sm);padding:1.25rem;text-align:center">
      <div style="font-size:11px;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Lunas</div>
      <div id="count-lunas" style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--forest)">—</div>
    </div>
    <div style="background:var(--gold-pale);border-radius:var(--radius-sm);padding:1.25rem;text-align:center">
      <div style="font-size:11px;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Menunggu</div>
      <div id="count-pending" style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:#7A5C00">—</div>
    </div>
    <div style="background:#FDECEA;border-radius:var(--radius-sm);padding:1.25rem;text-align:center">
      <div style="font-size:11px;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Belum Bayar</div>
      <div id="count-belum" style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--rust)">—</div>
    </div>
  </div>

  <div id="tagihan-container" class="card">
    <div style="text-align:center;padding:2rem;color:var(--ink-soft)">Memuat data tagihan...</div>
  </div>

  @endif
</div>
</main>

@if($isAllowed)
<!-- Payment modal -->
<div id="modal-overlay">
  <div id="modal-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem">
      <div style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--forest)" id="modal-title">Bayar Iuran</div>
      <button onclick="closeModal()" style="background:none;border:none;font-size:18px;cursor:pointer;color:var(--ink-soft)"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div id="modal-info" style="background:var(--cream);border-radius:var(--radius-sm);padding:1rem;margin-bottom:1.25rem;font-size:13px">
      <div style="color:var(--ink-soft);margin-bottom:4px" id="modal-unit"></div>
      <div style="font-size:1.1rem;font-weight:600;color:var(--forest)" id="modal-nominal"></div>
    </div>

    <div style="margin-bottom:1rem">
      <div style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Metode Pembayaran</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px" id="metode-grid">
        @foreach([['transfer','fa-solid fa-building-columns','Transfer'],['qris','fa-solid fa-qrcode','QRIS'],['tunai','fa-solid fa-money-bill','Tunai'],['gopay','fa-solid fa-wallet','GoPay'],['ovo','fa-solid fa-wallet','OVO'],['dana','fa-solid fa-wallet','DANA']] as [$id,$icon,$lbl])
        <button class="metode-btn" data-metode="{{ $id }}" onclick="selectMetode('{{ $id }}')">
          <span style="font-size:16px"><i class="{{ $icon }}"></i></span>
          <span>{{ $lbl }}</span>
        </button>
        @endforeach
      </div>
    </div>

    <div style="margin-bottom:1rem">
      <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px">Catatan (opsional)</label>
      <input type="text" id="modal-catatan" placeholder="misal: bayar tunai ke pak RT" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px;background:var(--warm);color:var(--ink)">
    </div>

    <div style="margin-bottom:1.5rem">
      <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px">Bukti Pembayaran (opsional)</label>
      <div id="file-drop" onclick="document.getElementById('bukti-input').click()">
        <input type="file" id="bukti-input" accept="image/*,.pdf" style="display:none" onchange="handleFileSelect(this)">
        <div id="file-placeholder">
          <div style="font-size:28px;margin-bottom:6px;color:var(--ink-soft)"><i class="fa-solid fa-paperclip"></i></div>
          <div style="font-size:13px;color:var(--ink-soft)">Klik untuk upload bukti</div>
          <div style="font-size:11px;color:var(--ink-mute);margin-top:4px">JPG, PNG, PDF · Maks 5MB</div>
        </div>
        <div id="file-preview" style="display:none"></div>
      </div>
    </div>

    <div id="modal-error" style="display:none;background:#FDECEA;color:var(--rust);padding:10px 14px;border-radius:var(--radius-sm);font-size:13px;margin-bottom:1rem"></div>

    <button id="modal-submit-btn" onclick="submitBayar()" class="btn-primary" style="width:100%;justify-content:center">
      Kirim Pembayaran
    </button>
  </div>
</div>
@endif

@endsection
@section('scripts')
@if($isAllowed)
<script>
const _csrfToken = '{{ csrf_token() }}';
let _currentTagihanId = null;
let _selectedMetode   = 'transfer';
let _selectedFile     = null;
let _allData          = [];

async function loadTagihan(tahun) {
  document.getElementById('loading-hint').textContent = 'Memuat...';
  try {
    const res  = await fetch(`/api/iuran/warga/tagihan?tahun=${tahun}`);
    const data = await res.json();
    document.getElementById('loading-hint').textContent = '';
    if (!data.success) { renderError(data.message); return; }
    _allData = data.data || [];
    populateYearSelect(data.available_years || [tahun]);
    renderTagihan(_allData);
  } catch (e) {
    document.getElementById('loading-hint').textContent = '';
    renderError('Gagal memuat data.');
  }
}

function populateYearSelect(years) {
  const sel = document.getElementById('tahun-select');
  const cur = sel.value;
  sel.innerHTML = years.map(y => `<option value="${y}" ${y == cur ? 'selected' : ''}>${y}</option>`).join('');
}

function renderTagihan(rows) {
  const lunas     = rows.filter(r => r.status === 'lunas').length;
  const pending   = rows.filter(r => r.status === 'pending').length;
  const belum     = rows.filter(r => r.status === 'belum').length;
  document.getElementById('count-lunas').textContent   = lunas;
  document.getElementById('count-pending').textContent = pending;
  document.getElementById('count-belum').textContent   = belum;

  const BULAN = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  const statusLabel = { belum:'Belum Bayar', pending:'Menunggu Verifikasi', lunas:'Lunas', dispensasi:'Dispensasi', rejected:'Ditolak' };
  const statusClass = { belum:'status-belum', pending:'status-pending', lunas:'status-lunas', dispensasi:'status-dispensasi', rejected:'status-rejected' };

  if (!rows.length) {
    document.getElementById('tagihan-container').innerHTML = `
      <div style="text-align:center;padding:2rem;color:var(--ink-soft)">Tidak ada tagihan untuk tahun ini.</div>`;
    return;
  }

  const html = rows.map(r => {
    const unitLabel = `Blok ${r.blok}${r.nomor}`;
    const bulanLabel = BULAN[r.bulan] || `Bulan ${r.bulan}`;
    const nominal = parseInt(r.nominal).toLocaleString('id-ID');
    const badgeClass = statusClass[r.status] || 'status-belum';
    const badgeLabel = statusLabel[r.status] || r.status;

    let actions = '';
    if (r.status === 'belum') {
      actions = `<button class="tagihan-actions" onclick="openModal(${r.id},'${bulanLabel}','${unitLabel}',${r.nominal})" style="padding:7px 18px;background:var(--forest);color:#fff;border-radius:99px;font-size:12px;font-weight:500;border:none;cursor:pointer">Bayar</button>`;
    } else if (r.status === 'pending') {
      const buktiHtml = r.bukti_url ? `<a href="${r.bukti_url}" target="_blank" style="font-size:11px;color:var(--forest-light)">Lihat bukti</a>` : `<button onclick="openUploadBukti(${r.bayar_id})" style="background:none;border:none;font-size:11px;color:var(--forest);cursor:pointer;text-decoration:underline">Upload bukti</button>`;
      actions = `<div class="tagihan-actions" style="display:flex;flex-direction:column;align-items:flex-end;gap:3px">${buktiHtml}</div>`;
    } else if (r.status === 'lunas') {
      const metodeLabel = r.metode ? ` · ${r.metode}` : '';
      actions = `<div class="tagihan-actions" style="font-size:11px;color:var(--ink-mute)"><i class="fa-solid fa-check" style="color:var(--forest);margin-right:3px;font-size:10px"></i>Lunas${metodeLabel}</div>`;
    }

    return `<div class="tagihan-row">
      <div style="display:flex;align-items:center;gap:12px;min-width:0">
        <div style="flex-shrink:0">
          <div style="font-weight:500;color:var(--ink)">${bulanLabel}</div>
          <div style="font-size:12px;color:var(--ink-soft)">${unitLabel}</div>
        </div>
        <span class="status-badge ${badgeClass}">${badgeLabel}</span>
      </div>
      <div style="display:flex;align-items:center;gap:12px;flex-shrink:0">
        <div style="text-align:right">
          <div style="font-size:13px;font-weight:600;color:var(--forest)">Rp ${nominal}</div>
        </div>
        ${actions}
      </div>
    </div>`;
  }).join('');

  document.getElementById('tagihan-container').innerHTML = `<div>${html}</div>`;
}

function renderError(msg) {
  document.getElementById('tagihan-container').innerHTML = `
    <div style="text-align:center;padding:2rem;color:var(--rust)">${msg}</div>`;
}

// ── Modal ──────────────────────────────────────────────────────

function openModal(tagihanId, bulan, unit, nominal) {
  _currentTagihanId = tagihanId;
  _selectedMetode   = 'transfer';
  _selectedFile     = null;
  document.getElementById('modal-title').textContent     = `Bayar Iuran ${bulan}`;
  document.getElementById('modal-unit').textContent      = unit;
  document.getElementById('modal-nominal').textContent   = 'Rp ' + parseInt(nominal).toLocaleString('id-ID');
  document.getElementById('modal-catatan').value         = '';
  document.getElementById('modal-error').style.display  = 'none';
  document.getElementById('bukti-input').value           = '';
  document.getElementById('file-placeholder').style.display = 'block';
  document.getElementById('file-preview').style.display     = 'none';
  document.querySelectorAll('.metode-btn').forEach(b => {
    b.classList.toggle('active', b.dataset.metode === 'transfer');
  });
  document.getElementById('modal-overlay').classList.add('open');
}

function closeModal() {
  document.getElementById('modal-overlay').classList.remove('open');
  _currentTagihanId = null;
}

function selectMetode(id) {
  _selectedMetode = id;
  document.querySelectorAll('.metode-btn').forEach(b => b.classList.toggle('active', b.dataset.metode === id));
}

function handleFileSelect(input) {
  const file = input.files[0];
  if (!file) return;
  _selectedFile = file;
  const isImg = file.type.startsWith('image/');
  const preview = document.getElementById('file-preview');
  preview.style.display = 'block';
  document.getElementById('file-placeholder').style.display = 'none';
  if (isImg) {
    const reader = new FileReader();
    reader.onload = e => { preview.innerHTML = `<img src="${e.target.result}" style="max-height:160px;max-width:100%;border-radius:6px"><div style="font-size:11px;color:var(--ink-soft);margin-top:6px">${file.name}</div>`; };
    reader.readAsDataURL(file);
  } else {
    preview.innerHTML = `<div style="font-size:28px;color:var(--ink-soft)"><i class="fa-solid fa-file"></i></div><div style="font-size:12px;color:var(--ink-mid);margin-top:4px">${file.name}</div>`;
  }
}

async function submitBayar() {
  const btn = document.getElementById('modal-submit-btn');
  const errEl = document.getElementById('modal-error');
  errEl.style.display = 'none';
  btn.disabled = true;
  btn.textContent = 'Mengirim...';

  try {
    const fd = new FormData();
    fd.append('iuran_tagihan_id', _currentTagihanId);
    fd.append('metode', _selectedMetode);
    fd.append('catatan', document.getElementById('modal-catatan').value);
    fd.append('_token', _csrfToken);
    if (_selectedFile) fd.append('bukti', _selectedFile);

    const res  = await fetch('/api/iuran/warga/bayar', { method: 'POST', body: fd });
    const data = await res.json();

    if (!data.success) {
      errEl.textContent    = data.message || 'Gagal mengirim pembayaran.';
      errEl.style.display  = 'block';
      btn.disabled = false;
      btn.textContent = 'Kirim Pembayaran';
      return;
    }

    closeModal();
    loadTagihan(document.getElementById('tahun-select').value);
  } catch (e) {
    errEl.textContent   = 'Terjadi kesalahan. Coba lagi.';
    errEl.style.display = 'block';
    btn.disabled = false;
    btn.textContent = 'Kirim Pembayaran';
  }
}

// ── Upload bukti for pending payment ─────────────────────────

let _uploadBuktiId = null;

function openUploadBukti(bayarId) {
  _uploadBuktiId = bayarId;
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/*,.pdf';
  input.onchange = async () => {
    const file = input.files[0];
    if (!file) return;
    const fd = new FormData();
    fd.append('bukti', file);
    fd.append('_token', _csrfToken);
    try {
      const res  = await fetch(`/api/iuran/warga/bayar/${bayarId}/bukti`, { method: 'POST', body: fd });
      const data = await res.json();
      if (data.success) {
        loadTagihan(document.getElementById('tahun-select').value);
      } else {
        alert(data.message || 'Gagal upload bukti.');
      }
    } catch (e) {
      alert('Terjadi kesalahan.');
    }
  };
  input.click();
}

// ── Init ──────────────────────────────────────────────────────

document.getElementById('tahun-select').addEventListener('change', function () {
  loadTagihan(this.value);
});

document.getElementById('modal-overlay').addEventListener('click', function (e) {
  if (e.target === this) closeModal();
});

const fileDrop = document.getElementById('file-drop');
fileDrop.addEventListener('dragover', e => { e.preventDefault(); fileDrop.classList.add('drag'); });
fileDrop.addEventListener('dragleave', () => fileDrop.classList.remove('drag'));
fileDrop.addEventListener('drop', e => {
  e.preventDefault();
  fileDrop.classList.remove('drag');
  const file = e.dataTransfer.files[0];
  if (file) {
    const dt = new DataTransfer();
    dt.items.add(file);
    const input = document.getElementById('bukti-input');
    input.files = dt.files;
    handleFileSelect(input);
  }
});

loadTagihan({{ date('Y') }});
</script>
@endif
@endsection
