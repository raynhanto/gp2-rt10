@extends('layouts.admin')
@section('title', 'Catat Pengeluaran')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Catat Pengeluaran</div>
    </div>
    <button onclick="document.getElementById('form-overlay').style.display='flex'" class="btn-primary">+ Catat Pengeluaran</button>
  </div>
  <div id="pengeluaran-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
</div>

<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:480px;margin:1rem;max-height:90vh;overflow-y:auto">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem">Catat Pengeluaran</div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Kampanye</label>
      <select id="form-kampanye" onchange="loadAnggaran(this.value)" style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;background:#fff"></select>
    </div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Pos Anggaran (opsional)</label>
      <select id="form-anggaran" style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;background:#fff">
        <option value="">— Pilih pos anggaran —</option>
      </select>
    </div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Keterangan</label>
      <input type="text" id="form-keterangan" placeholder="Contoh: Beli lampu LED di toko Jaya"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
    </div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Nominal (Rp)</label>
      <input type="number" id="form-nominal" placeholder="500000"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
    </div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Tanggal</label>
      <input type="date" id="form-tanggal"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
    </div>
    <div style="margin-bottom:1.5rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Foto Nota / Struk (opsional)</label>
      <input type="file" id="form-bukti" accept="image/*,.pdf"
        style="width:100%;padding:10px;border:1.5px dashed var(--border);border-radius:var(--radius-sm);font-size:13px">
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="savePengeluaran()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="document.getElementById('form-overlay').style.display='none'" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>

async function loadPengeluaran() {
  const [pRes, kRes] = await Promise.all([fetch('/api/pengeluaran'), fetch('/api/kampanye')]);
  const pData = await pRes.json();
  const kData = await kRes.json();
  if (kData.success) {
    const sel = document.getElementById('form-kampanye');
    sel.innerHTML = kData.data.map(k=>`<option value="${k.id}">${k.judul}</option>`).join('');
    if (kData.data.length) loadAnggaran(kData.data[0].id);
  }
  // Set today's date as default
  document.getElementById('form-tanggal').value = new Date().toISOString().split('T')[0];

  if (!pData.success || !pData.data.length) {
    document.getElementById('pengeluaran-list').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Belum ada pengeluaran tercatat.</div>'; return;
  }
  document.getElementById('pengeluaran-list').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">
        ${['Tanggal','Keterangan','Kampanye','Nominal','Bukti'].map(h=>`<th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--ink-soft)">${h}</th>`).join('')}
      </tr></thead>
      <tbody>${pData.data.map(p=>{
        const tgl = new Date(p.tanggal).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:12px 16px;font-size:12px;color:var(--ink-soft)">${tgl}</td>
          <td style="padding:12px 16px;font-size:13px">${p.keterangan}</td>
          <td style="padding:12px 16px;font-size:12px;color:var(--ink-soft)">${p.judul||'—'}</td>
          <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--rust)">- Rp ${parseInt(p.nominal).toLocaleString('id-ID')}</td>
          <td style="padding:12px 16px">${p.bukti_url?`<a href="${p.bukti_url}" target="_blank" style="font-size:12px;color:var(--forest-light)">Lihat foto</a>`:'—'}</td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;
}

async function loadAnggaran(kampanyeId) {
  const res = await fetch('/api/anggaran?kampanye_id='+kampanyeId);
  const data = await res.json();
  const sel = document.getElementById('form-anggaran');
  sel.innerHTML = '<option value="">— Pilih pos anggaran —</option>';
  if (data.success) sel.innerHTML += data.data.map(a=>`<option value="${a.id}">${a.pos} (est. Rp ${parseInt(a.estimasi).toLocaleString('id-ID')})</option>`).join('');
}

async function savePengeluaran() {
  const formData = new FormData();
  formData.append('kampanye_id', document.getElementById('form-kampanye').value);
  formData.append('anggaran_id', document.getElementById('form-anggaran').value || '');
  formData.append('keterangan', document.getElementById('form-keterangan').value.trim());
  formData.append('nominal', document.getElementById('form-nominal').value);
  formData.append('tanggal', document.getElementById('form-tanggal').value);
  formData.append('_token', _csrfToken);
  const bukti = document.getElementById('form-bukti').files[0];
  if (bukti) formData.append('bukti', bukti);
  if (!formData.get('keterangan') || !formData.get('nominal') || !formData.get('tanggal')) {
    alert('Keterangan, nominal, dan tanggal wajib diisi.'); return;
  }
  const res = await fetch('/api/pengeluaran', { method:'POST', body: formData });
  const data = await res.json();
  if (data.success) { document.getElementById('form-overlay').style.display='none'; showToast(data.message); loadPengeluaran(); }
  else alert(data.message);
}
loadPengeluaran();
</script>
@endsection
