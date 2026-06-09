@extends('layouts.admin')
@section('title', 'Transaksi Instan')
@section('content')
<div class="container">
  <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Transaksi Instan</div>
      <p style="font-size:13px;color:var(--ink-soft);margin-top:6px">Uang masuk & langsung keluar — net saldo nol. Contoh: titipan bayar domain, iuran RT via perantara.</p>
    </div>
    <button onclick="openForm()" class="btn-primary">+ Tambah</button>
  </div>

  <div style="display:flex;gap:1rem;margin-bottom:1.5rem">
    <input type="text" id="search-input" placeholder="Cari nama / tujuan..." oninput="debounceSearch()"
      style="flex:1;padding:9px 16px;border:1.5px solid var(--border);border-radius:99px;font-family:'DM Sans',sans-serif;font-size:13px;outline:none">
  </div>

  <div id="list-container"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
  <div id="pagination" style="display:flex;justify-content:center;gap:8px;margin-top:1.5rem"></div>
</div>

{{-- Form modal --}}
<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:500px;margin:1rem;max-height:90vh;overflow-y:auto">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem">Transaksi Instan</div>

    <div style="background:var(--gold-pale);border:1px solid var(--border-gold);border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:1.5rem;font-size:13px;color:var(--ink-mid)">
      <i class="fa-solid fa-lightbulb" style="color:var(--gold-dark);margin-right:5px"></i>Uang diterima dari pembayar, langsung dikeluarkan untuk tujuan yang sama. <strong>Saldo kas tidak berubah.</strong>
    </div>

    <div style="display:grid;gap:1rem">
      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Tanggal</label>
        <input type="date" id="f-tanggal" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px">
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Warga (opsional)</label>
        <select id="f-warga" onchange="onWargaChange()" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px;background:#fff">
          <option value="">— Manual / bukan warga terdaftar —</option>
        </select>
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Nama Pembayar</label>
        <input type="text" id="f-nama" placeholder="misal: Pak Andi" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px">
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Tujuan / Keperluan</label>
        <input type="text" id="f-tujuan" placeholder="misal: Perpanjang domain vensalor-kingdom.com" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px">
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Nominal (Rp)</label>
        <input type="number" id="f-nominal" placeholder="150000" min="1" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px">
      </div>

      <div>
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:6px">Keterangan (opsional)</label>
        <textarea id="f-keterangan" rows="2" placeholder="Info tambahan..."
          style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:13px;resize:none"></textarea>
      </div>
    </div>

    <div id="f-error" style="display:none;background:#FDECEA;color:var(--rust);padding:10px 14px;border-radius:var(--radius-sm);font-size:13px;margin-top:1rem"></div>

    <div style="display:flex;gap:8px;margin-top:1.5rem">
      <button onclick="submitForm()" class="btn-primary" id="f-submit" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeForm()" style="padding:12px 20px;border-radius:100px;border:1.5px solid var(--border);background:#fff;font-size:14px;cursor:pointer;font-family:'DM Sans',sans-serif">Batal</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
let _page    = 1;
let _search  = '';
let _debounce = null;
let _wargaList = [];

function rp(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }

async function loadWarga() {
  try {
    const res  = await fetch('/api/users');
    const data = await res.json();
    if (!data.success) return;
    _wargaList = data.data || [];
    const sel = document.getElementById('f-warga');
    sel.innerHTML = `<option value="">— Manual / bukan warga terdaftar —</option>` +
      _wargaList.map(u => {
        const unit = u.units?.length ? ` · ${u.units.map(x => `${x.blok}${x.nomor}`).join(', ')}` : '';
        return `<option value="${u.id}" data-nama="${u.nama}">${u.nama}${unit}</option>`;
      }).join('');
  } catch (e) {}
}

function onWargaChange() {
  const sel    = document.getElementById('f-warga');
  const option = sel.options[sel.selectedIndex];
  const nama   = option.dataset.nama || '';
  const namaEl = document.getElementById('f-nama');
  if (nama) {
    namaEl.value    = nama;
    namaEl.readOnly = true;
    namaEl.style.background = 'var(--cream)';
  } else {
    namaEl.readOnly = false;
    namaEl.style.background = '';
  }
}

async function loadList() {
  const params = new URLSearchParams({ page: _page, search: _search });
  const res  = await fetch('/api/transaksi-instan?' + params);
  const data = await res.json();
  if (!data.success) return;
  const rows = data.data;
  const meta = data.meta;

  if (!rows.length) {
    document.getElementById('list-container').innerHTML =
      `<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Belum ada transaksi instan.</div>`;
    document.getElementById('pagination').innerHTML = '';
    return;
  }

  document.getElementById('list-container').innerHTML =
    `<div class="card" style="padding:0;overflow:hidden">
      <table style="width:100%;border-collapse:collapse">
        <thead><tr style="background:var(--cream)">
          ${['Tanggal','Pembayar','Tujuan','Nominal','Dicatat oleh',''].map(h=>`<th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft)">${h}</th>`).join('')}
        </tr></thead>
        <tbody>${rows.map(r => {
          const tgl = new Date(r.tanggal + 'T00:00:00').toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});
          return `<tr style="border-top:1px solid var(--border)">
            <td style="padding:12px 16px;font-size:13px;color:var(--ink-soft);white-space:nowrap">${tgl}</td>
            <td style="padding:12px 16px;font-size:13px;font-weight:500">${r.pembayar_nama}</td>
            <td style="padding:12px 16px;font-size:13px">${r.tujuan}${r.keterangan?`<div style="font-size:11px;color:var(--ink-mute)">${r.keterangan}</div>`:''}</td>
            <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--forest);white-space:nowrap">${rp(r.nominal)}</td>
            <td style="padding:12px 16px;font-size:12px;color:var(--ink-soft)">${r.dibuat_oleh||'—'}</td>
            <td style="padding:12px 16px">
              <button onclick="hapus(${r.id},'${r.tujuan.replace(/'/g,"\\'")}')"
                style="padding:4px 12px;border-radius:99px;border:1px solid var(--rust);background:#fff;color:var(--rust);font-size:11px;cursor:pointer;font-family:'DM Sans',sans-serif">Hapus</button>
            </td>
          </tr>`;
        }).join('')}</tbody>
      </table>
    </div>`;

  // Pagination
  const totalPages = Math.ceil(meta.total / meta.per_page);
  document.getElementById('pagination').innerHTML = totalPages <= 1 ? '' :
    Array.from({length: totalPages}, (_, i) => i + 1).map(p =>
      `<button onclick="goPage(${p})" style="padding:6px 14px;border-radius:99px;border:1.5px solid ${p===_page?'var(--forest)':'var(--border)'};background:${p===_page?'var(--forest)':'#fff'};color:${p===_page?'#fff':'var(--ink-soft)'};font-size:13px;cursor:pointer;font-family:'DM Sans',sans-serif">${p}</button>`
    ).join('');
}

function goPage(p) { _page = p; loadList(); }

function debounceSearch() {
  clearTimeout(_debounce);
  _debounce = setTimeout(() => { _page = 1; _search = document.getElementById('search-input').value; loadList(); }, 350);
}

// ── Form ──────────────────────────────────────────────────────

function openForm() {
  const today = new Date().toISOString().slice(0, 10);
  document.getElementById('f-tanggal').value    = today;
  document.getElementById('f-warga').value      = '';
  document.getElementById('f-nama').value       = '';
  document.getElementById('f-nama').readOnly    = false;
  document.getElementById('f-nama').style.background = '';
  document.getElementById('f-tujuan').value     = '';
  document.getElementById('f-nominal').value    = '';
  document.getElementById('f-keterangan').value = '';
  document.getElementById('f-error').style.display = 'none';
  document.getElementById('f-submit').disabled  = false;
  document.getElementById('f-submit').textContent = 'Simpan';
  document.getElementById('form-overlay').style.display = 'flex';
}

function closeForm() { document.getElementById('form-overlay').style.display = 'none'; }

async function submitForm() {
  const btn   = document.getElementById('f-submit');
  const errEl = document.getElementById('f-error');
  errEl.style.display = 'none';
  btn.disabled = true;
  btn.textContent = 'Menyimpan...';

  try {
    const res  = await fetch('/api/transaksi-instan', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},
      body: JSON.stringify({
        tanggal:           document.getElementById('f-tanggal').value,
        pembayar_user_id:  document.getElementById('f-warga').value || null,
        pembayar_nama:     document.getElementById('f-nama').value,
        tujuan:            document.getElementById('f-tujuan').value,
        nominal:           parseInt(document.getElementById('f-nominal').value) || 0,
        keterangan:        document.getElementById('f-keterangan').value,
      })
    });
    const data = await res.json();
    if (data.success) {
      closeForm();
      showToast(data.message);
      _page = 1;
      loadList();
    } else {
      const firstErr = data.errors ? Object.values(data.errors)[0] : data.message;
      errEl.textContent = firstErr;
      errEl.style.display = 'block';
      btn.disabled = false;
      btn.textContent = 'Simpan';
    }
  } catch (e) {
    errEl.textContent = 'Terjadi kesalahan.';
    errEl.style.display = 'block';
    btn.disabled = false;
    btn.textContent = 'Simpan';
  }
}

async function hapus(id, tujuan) {
  if (!confirm(`Hapus transaksi instan "${tujuan}"?\n\nEntri kas masuk dan keluar terkait juga akan dihapus.`)) return;
  const res  = await fetch('/api/transaksi-instan/' + id, {
    method: 'DELETE', headers: {'X-CSRF-TOKEN': _csrfToken}
  });
  const data = await res.json();
  if (data.success) { showToast(data.message); loadList(); }
  else alert(data.message);
}

document.getElementById('form-overlay').addEventListener('click', e => { if(e.target===e.currentTarget) closeForm(); });

loadWarga();
loadList();
</script>
@endsection
