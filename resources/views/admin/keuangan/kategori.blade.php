@extends('layouts.admin')
@section('title', 'Kategori Keuangan')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <a href="/admin/keuangan" style="font-size:13px;color:var(--ink-soft)">← Keuangan</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Kategori Keuangan</div>
    </div>
    <button onclick="openForm()" class="btn-primary">+ Tambah Kategori</button>
  </div>

  <div id="kategori-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
</div>

{{-- Modal --}}
<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:440px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem" id="form-title">Tambah Kategori</div>
    <input type="hidden" id="edit-id" value="">

    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft);margin-bottom:5px">Nama Kategori</label>
      <input type="text" id="f-nama" placeholder="Cth: Keamanan, Kebersihan..." style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
    </div>

    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft);margin-bottom:5px">Jenis</label>
      <select id="f-jenis" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;background:#fff">
        <option value="keduanya">Masuk & Keluar</option>
        <option value="masuk">Pemasukan saja</option>
        <option value="keluar">Pengeluaran saja</option>
      </select>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft);margin-bottom:5px">Warna</label>
        <input type="color" id="f-warna" value="#1A3D2B" style="width:100%;height:42px;padding:4px;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer">
      </div>
      <div>
        <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-soft);margin-bottom:5px">Urutan</label>
        <input type="number" id="f-urutan" value="0" min="0" max="99" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
      </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:1.5rem">
      <button onclick="saveKategori()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeForm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const icons = { masuk:'fa-arrow-down', keluar:'fa-arrow-up', keduanya:'fa-arrows-up-down' };
const jenisLabel = { masuk:'Pemasukan', keluar:'Pengeluaran', keduanya:'Masuk & Keluar' };

async function loadKategori() {
  const r = await fetch('/api/kategori');
  const j = await r.json();
  if (!j.success) { document.getElementById('kategori-list').innerHTML = '<p style="color:var(--rust)">Gagal memuat.</p>'; return; }
  if (!j.data.length) {
    document.getElementById('kategori-list').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Belum ada kategori. Tambahkan untuk mulai mengkategorikan transaksi.</div>';
    return;
  }
  document.getElementById('kategori-list').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">
        ${['Warna','Nama','Jenis','Urutan',''].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}
      </tr></thead>
      <tbody>${j.data.map(k=>`<tr style="border-top:1px solid var(--border)">
        <td style="padding:10px 16px"><span style="display:inline-block;width:20px;height:20px;border-radius:4px;background:${k.warna}"></span></td>
        <td style="padding:10px 16px;font-size:14px;font-weight:500">${k.nama}</td>
        <td style="padding:10px 16px"><span style="font-size:11px;padding:3px 10px;border-radius:99px;background:var(--forest-pale);color:var(--forest)">${jenisLabel[k.jenis]||k.jenis}</span></td>
        <td style="padding:10px 16px;font-size:13px;color:var(--ink-soft)">${k.urutan}</td>
        <td style="padding:10px 16px;text-align:right">
          <button onclick='editKategori(${JSON.stringify(k)})' style="font-size:12px;padding:5px 12px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;margin-right:4px">Edit</button>
          <button onclick='delKategori(${k.id},"${k.nama}")' style="font-size:12px;padding:5px 12px;border:1px solid #FDECEA;border-radius:6px;background:#FDECEA;color:var(--rust);cursor:pointer">Hapus</button>
        </td>
      </tr>`).join('')}</tbody>
    </table>
  </div>`;
}

function openForm() {
  document.getElementById('edit-id').value = '';
  document.getElementById('form-title').textContent = 'Tambah Kategori';
  document.getElementById('f-nama').value = '';
  document.getElementById('f-jenis').value = 'keduanya';
  document.getElementById('f-warna').value = '#1A3D2B';
  document.getElementById('f-urutan').value = 0;
  document.getElementById('form-overlay').style.display = 'flex';
}
function closeForm() { document.getElementById('form-overlay').style.display = 'none'; }

function editKategori(k) {
  document.getElementById('edit-id').value = k.id;
  document.getElementById('form-title').textContent = 'Edit Kategori';
  document.getElementById('f-nama').value = k.nama;
  document.getElementById('f-jenis').value = k.jenis;
  document.getElementById('f-warna').value = k.warna;
  document.getElementById('f-urutan').value = k.urutan;
  document.getElementById('form-overlay').style.display = 'flex';
}

async function saveKategori() {
  const id   = document.getElementById('edit-id').value;
  const body = {
    nama: document.getElementById('f-nama').value.trim(),
    jenis: document.getElementById('f-jenis').value,
    warna: document.getElementById('f-warna').value,
    urutan: parseInt(document.getElementById('f-urutan').value),
  };
  if (!body.nama) { alert('Nama kategori wajib diisi.'); return; }

  const url    = id ? `/api/kategori/${id}` : '/api/kategori';
  const method = id ? 'PUT' : 'POST';
  const r = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken }, body: JSON.stringify(body) });
  const j = await r.json();
  if (j.success) { closeForm(); showToast(j.message); loadKategori(); }
  else alert(j.message);
}

async function delKategori(id, nama) {
  if (!confirm(`Hapus kategori "${nama}"?`)) return;
  const r = await fetch(`/api/kategori/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': _csrfToken } });
  const j = await r.json();
  if (j.success) { showToast(j.message); loadKategori(); }
  else alert(j.message);
}

loadKategori();
</script>
@endsection
