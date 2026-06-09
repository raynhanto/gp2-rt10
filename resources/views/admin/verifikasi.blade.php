@extends('layouts.admin')
@section('title', 'Verifikasi Donasi')
@section('content')
<div class="container">
  <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Verifikasi Donasi</div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <button onclick="openInputModal()" style="padding:8px 18px;border-radius:100px;font-size:13px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif;border:1.5px solid var(--forest);background:var(--forest);color:#fff">
        + Input Manual
      </button>
      <div style="width:1px;background:var(--border);margin:0 4px"></div>
      @foreach(['pending'=>'Menunggu','verified'=>'Terverifikasi','rejected'=>'Ditolak'] as $v=>$l)
      <button onclick="filterStatus('{{ $v }}')" id="f-{{ $v }}"
        style="padding:7px 16px;border-radius:100px;font-size:13px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif;border:1.5px solid {{ $v==='pending' ? 'var(--forest)' : 'var(--border)' }};background:{{ $v==='pending' ? 'var(--forest)' : '#fff' }};color:{{ $v==='pending' ? '#fff' : 'var(--ink-soft)' }}">
        {{ $l }}
      </button>
      @endforeach
    </div>
  </div>

  <div id="donasi-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
</div>

{{-- Verify modal --}}
<div id="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:min(480px,calc(100vw - 2rem));margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.25rem" id="modal-title">Verifikasi Donasi</div>
    <div id="modal-info" style="background:var(--cream);border-radius:var(--radius-sm);padding:1rem;margin-bottom:1rem;font-size:13px;line-height:2"></div>
    <div id="modal-bukti" style="margin-bottom:1rem"></div>
    <textarea id="modal-catatan" placeholder="Catatan (opsional)" rows="2"
      style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;resize:none;margin-bottom:1rem"></textarea>
    <div style="display:flex;gap:8px">
      <button onclick="submitVerifikasi('verify')" class="btn-primary" style="flex:1;justify-content:center"><i class="fa-solid fa-check" style="margin-right:6px"></i>Approve</button>
      <button onclick="submitVerifikasi('reject')" style="flex:1;padding:12px;border-radius:100px;border:1.5px solid var(--rust);background:#fff;color:var(--rust);font-size:14px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif"><i class="fa-solid fa-xmark" style="margin-right:6px"></i>Tolak</button>
      <button onclick="closeModal()" style="padding:12px 16px;border-radius:100px;border:1.5px solid var(--border);background:#fff;font-size:14px;cursor:pointer;font-family:'DM Sans',sans-serif">Batal</button>
    </div>
  </div>
</div>

{{-- Input Manual modal --}}
<div id="input-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:500px;margin:1rem;max-height:90vh;overflow-y:auto">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem">Input Donasi Manual</div>

    <div style="display:grid;gap:1rem">
      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Kampanye</label>
        <select id="im-kampanye" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px;background:#fff">
          <option value="">Donasi Umum (tanpa kampanye)</option>
        </select>
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Warga (opsional)</label>
        <select id="im-warga" onchange="onImWargaChange()" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px;background:#fff">
          <option value="">— Manual / bukan warga terdaftar —</option>
        </select>
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Nama Donatur</label>
        <input type="text" id="im-nama" placeholder="Nama lengkap donatur" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px">
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Nominal (Rp)</label>
        <input type="number" id="im-nominal" placeholder="50000" min="1000" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px">
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Metode</label>
        <select id="im-metode" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px;background:#fff">
          <option value="tunai">Tunai</option>
          <option value="transfer">Transfer Bank</option>
          <option value="qris">QRIS</option>
          <option value="gopay">GoPay</option>
          <option value="ovo">OVO</option>
          <option value="dana">DANA</option>
        </select>
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Catatan</label>
        <input type="text" id="im-catatan" placeholder="Keterangan tambahan (opsional)" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px">
      </div>

      <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px">
        <input type="checkbox" id="im-anonym">
        Tampilkan sebagai Donatur Anonim
      </label>
    </div>

    <div id="im-error" style="display:none;background:#FDECEA;color:var(--rust);padding:10px 14px;border-radius:var(--radius-sm);font-size:13px;margin-top:1rem"></div>

    <div style="display:flex;gap:8px;margin-top:1.5rem">
      <button onclick="submitInputManual()" class="btn-primary" id="im-submit-btn" style="flex:1;justify-content:center">Simpan & Verifikasi</button>
      <button onclick="closeInputModal()" style="padding:12px 20px;border-radius:100px;border:1.5px solid var(--border);background:#fff;font-size:14px;cursor:pointer;font-family:'DM Sans',sans-serif">Batal</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
let currentStatus = 'pending';
let currentId = null;
let donasiCache = [];
let kampanyeList = [];

async function loadDonasi() {
  const res = await fetch('/api/donasi?status=' + currentStatus);
  const data = await res.json();
  if (!data.success) return;
  const list = data.data;
  donasiCache = list;
  const statusColor = {pending:'#7A5C00',verified:'var(--forest)',rejected:'var(--rust)'};
  const statusBg = {pending:'var(--gold-pale)',verified:'#E8F4ED',rejected:'#FDECEA'};
  const statusLabel = {pending:'Menunggu',verified:'Terverifikasi',rejected:'Ditolak'};

  document.getElementById('donasi-list').innerHTML = !list.length
    ? `<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Tidak ada donasi ${statusLabel[currentStatus].toLowerCase()}.</div>`
    : `<div class="card" style="padding:0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
          <thead><tr style="background:var(--cream)">
            ${['Warga / Donatur','Kampanye','Nominal','Metode','Waktu','Status',''].map(h=>`<th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--ink-soft)">${h}</th>`).join('')}
          </tr></thead>
          <tbody>${list.map(d => {
            const tgl = new Date(d.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
            const donaturLabel = d.donatur_nama || d.nama || '—';
            const emailLabel = d.email ? `<div style="font-size:11px;color:var(--ink-soft)">${d.email}</div>` : '';
            const isManual = !d.user_id && d.donatur_nama;
            const manualTag = isManual ? `<span style="font-size:10px;padding:1px 6px;border-radius:99px;background:var(--gold-pale);color:var(--gold-dark);margin-left:4px">manual</span>` : '';
            return `<tr style="border-top:1px solid var(--border)">
              <td style="padding:12px 16px"><div style="font-size:13px;font-weight:500">${donaturLabel}${manualTag}</div>${emailLabel}</td>
              <td style="padding:12px 16px;font-size:13px">${d.judul||'—'}</td>
              <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--forest)">Rp ${parseInt(d.nominal).toLocaleString('id-ID')}</td>
              <td style="padding:12px 16px;font-size:12px;text-transform:uppercase">${d.metode}</td>
              <td style="padding:12px 16px;font-size:12px;color:var(--ink-soft)">${tgl}</td>
              <td style="padding:12px 16px"><span style="font-size:11px;padding:3px 10px;border-radius:99px;background:${statusBg[d.status]};color:${statusColor[d.status]}">${statusLabel[d.status]}</span></td>
              <td style="padding:12px 16px">${d.status==='pending'?`<button onclick="openModal(${d.id})" style="padding:6px 14px;border-radius:99px;border:1.5px solid var(--forest);background:#fff;color:var(--forest);font-size:12px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif">Cek</button>`:'—'}</td>
            </tr>`;
          }).join('')}</tbody>
        </table>
      </div>`;
}

function filterStatus(s) {
  currentStatus = s;
  ['pending','verified','rejected'].forEach(v => {
    const b = document.getElementById('f-'+v);
    const active = v===s;
    b.style.background = active?'var(--forest)':'#fff';
    b.style.color = active?'#fff':'var(--ink-soft)';
    b.style.borderColor = active?'var(--forest)':'var(--border)';
  });
  loadDonasi();
}

function openModal(id) {
  currentId = id;
  const d = donasiCache.find(x => x.id == id);
  if (!d) return;
  const donaturLabel = d.donatur_nama || d.nama || '—';
  document.getElementById('modal-info').innerHTML = `
    <b>Donatur:</b> ${donaturLabel}<br>
    <b>Kampanye:</b> ${d.judul||'—'}<br>
    <b>Nominal:</b> Rp ${parseInt(d.nominal).toLocaleString('id-ID')}<br>
    <b>Metode:</b> ${d.metode.toUpperCase()}<br>
    <b>Waktu:</b> ${new Date(d.created_at).toLocaleString('id-ID')}`;
  document.getElementById('modal-bukti').innerHTML = d.bukti_url
    ? `<img src="${d.bukti_url}" style="width:100%;max-height:200px;object-fit:contain;border-radius:var(--radius-sm);border:1px solid var(--border)">`
    : `<div style="text-align:center;padding:1rem;background:var(--cream);border-radius:var(--radius-sm);font-size:13px;color:var(--ink-soft)">Belum ada bukti upload</div>`;
  document.getElementById('modal-catatan').value = '';
  document.getElementById('modal-overlay').style.display = 'flex';
}

function closeModal() { document.getElementById('modal-overlay').style.display = 'none'; }

async function submitVerifikasi(action) {
  const catatan = document.getElementById('modal-catatan').value;
  const res = await fetch('/api/donasi/'+currentId+'/verify', {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},
    body: JSON.stringify({action, catatan})
  });
  const data = await res.json();
  if (data.success) { closeModal(); showToast(data.message); loadDonasi(); }
  else alert(data.message);
}

// ── Input Manual ──────────────────────────────────────────────

let _wargaList = [];

async function loadKampanye() {
  try {
    const res  = await fetch('/api/kampanye');
    const data = await res.json();
    if (!data.success) return;
    kampanyeList = data.data || [];
    const sel = document.getElementById('im-kampanye');
    sel.innerHTML = `<option value="">Donasi Umum (tanpa kampanye)</option>` +
      kampanyeList.filter(k => k.status !== 'arsip').map(k =>
        `<option value="${k.id}">${k.judul}</option>`
      ).join('');
  } catch (e) {}
}

async function loadWargaForDonasi() {
  try {
    const res  = await fetch('/api/users');
    const data = await res.json();
    if (!data.success) return;
    _wargaList = data.data || [];
    const sel = document.getElementById('im-warga');
    sel.innerHTML = `<option value="">— Manual / bukan warga terdaftar —</option>` +
      _wargaList.map(u => {
        const unit = u.units?.length ? ` · ${u.units.map(x => `${x.blok}${x.nomor}`).join(', ')}` : '';
        return `<option value="${u.id}" data-nama="${u.nama}">${u.nama}${unit}</option>`;
      }).join('');
  } catch (e) {}
}

function onImWargaChange() {
  const sel    = document.getElementById('im-warga');
  const option = sel.options[sel.selectedIndex];
  const nama   = option.dataset.nama || '';
  const namaEl = document.getElementById('im-nama');
  if (nama) {
    namaEl.value    = nama;
    namaEl.readOnly = true;
    namaEl.style.background = 'var(--cream)';
  } else {
    namaEl.readOnly = false;
    namaEl.style.background = '';
  }
}

function openInputModal() {
  document.getElementById('im-warga').value   = '';
  document.getElementById('im-nama').value    = '';
  document.getElementById('im-nama').readOnly = false;
  document.getElementById('im-nama').style.background = '';
  document.getElementById('im-nominal').value = '';
  document.getElementById('im-catatan').value = '';
  document.getElementById('im-anonym').checked = false;
  document.getElementById('im-metode').value  = 'tunai';
  document.getElementById('im-error').style.display = 'none';
  document.getElementById('im-submit-btn').disabled = false;
  document.getElementById('im-submit-btn').textContent = 'Simpan & Verifikasi';
  document.getElementById('input-overlay').style.display = 'flex';
}

function closeInputModal() { document.getElementById('input-overlay').style.display = 'none'; }

async function submitInputManual() {
  const btn   = document.getElementById('im-submit-btn');
  const errEl = document.getElementById('im-error');
  errEl.style.display = 'none';

  const nominal = parseInt(document.getElementById('im-nominal').value) || 0;
  const nama    = document.getElementById('im-nama').value.trim();

  if (nominal < 1000) {
    errEl.textContent = 'Nominal minimal Rp 1.000.';
    errEl.style.display = 'block';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Menyimpan...';

  try {
    const wargaId = document.getElementById('im-warga').value || null;
    const res = await fetch('/api/donasi/admin', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},
      body: JSON.stringify({
        user_id:      wargaId ? parseInt(wargaId) : null,
        kampanye_id:  document.getElementById('im-kampanye').value || null,
        donatur_nama: wargaId ? null : (nama || 'Donatur Tunai'),
        nominal:      nominal,
        metode:       document.getElementById('im-metode').value,
        catatan:      document.getElementById('im-catatan').value,
        is_anonym:    document.getElementById('im-anonym').checked,
      })
    });
    const data = await res.json();
    if (data.success) {
      closeInputModal();
      showToast(data.message);
      loadDonasi();
    } else {
      errEl.textContent = data.message || 'Gagal menyimpan.';
      errEl.style.display = 'block';
      btn.disabled = false;
      btn.textContent = 'Simpan & Verifikasi';
    }
  } catch (e) {
    errEl.textContent = 'Terjadi kesalahan.';
    errEl.style.display = 'block';
    btn.disabled = false;
    btn.textContent = 'Simpan & Verifikasi';
  }
}

document.getElementById('modal-overlay').addEventListener('click', e => { if(e.target===e.currentTarget) closeModal(); });
document.getElementById('input-overlay').addEventListener('click', e => { if(e.target===e.currentTarget) closeInputModal(); });

loadKampanye();
loadWargaForDonasi();
loadDonasi();
</script>
@endsection
