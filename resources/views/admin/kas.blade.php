@extends('layouts.admin')
@section('title', 'Manajemen Kas')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Manajemen Kas</div>
    </div>
    <button onclick="document.getElementById('form-overlay').style.display='flex'" class="btn-primary">+ Entri Manual</button>
  </div>

  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem">
    <div style="background:#E8F4ED;border-radius:var(--radius-sm);padding:1.25rem">
      <div style="font-size:12px;color:var(--ink-soft);margin-bottom:4px">Total Masuk</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--forest)" id="s-masuk">—</div>
    </div>
    <div style="background:#FDECEA;border-radius:var(--radius-sm);padding:1.25rem">
      <div style="font-size:12px;color:var(--ink-soft);margin-bottom:4px">Total Keluar</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--rust)" id="s-keluar">—</div>
    </div>
    <div style="background:var(--gold-pale);border-radius:var(--radius-sm);padding:1.25rem">
      <div style="font-size:12px;color:var(--ink-soft);margin-bottom:4px">Saldo Kas</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:#7A5C00" id="s-saldo">—</div>
    </div>
  </div>

  <div id="kas-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
</div>

<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:440px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem">Entri Kas Manual</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      @foreach(['masuk'=>'Pemasukan','keluar'=>'Pengeluaran'] as $v=>$l)
      <div onclick="document.querySelectorAll('[data-jenis]').forEach(b=>b.style.cssText='border:1.5px solid var(--border);background:#fff;color:var(--ink-soft);');this.style.cssText='border:1.5px solid var(--forest);background:#F0F7F3;color:var(--forest);';document.getElementById('jenis').value='{{ $v }}'"
        data-jenis="{{ $v }}" style="padding:12px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:#fff;color:var(--ink-soft);text-align:center;cursor:pointer;font-size:14px;font-weight:500">
        {{ $v === 'masuk' ? '💰' : '💸' }} {{ $l }}
      </div>
      @endforeach
    </div>
    <input type="hidden" id="jenis" value="masuk">
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Keterangan</label>
      <input type="text" id="keterangan" placeholder="Keterangan entri kas"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
    </div>
    <div style="margin-bottom:1rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Nominal (Rp)</label>
      <input type="number" id="nominal" placeholder="1000000"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
    </div>
    <div style="margin-bottom:1.5rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Kampanye (opsional)</label>
      <select id="kas-kampanye" style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;background:#fff">
        <option value="">— Tidak terkait kampanye —</option>
      </select>
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="saveKas()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="document.getElementById('form-overlay').style.display='none'" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>

async function loadKas() {
  const [kasRes, sumRes, kRes] = await Promise.all([
    fetch('/api/kas'), fetch('/api/kas/summary'), fetch('/api/kampanye')
  ]);
  const kasData = await kasRes.json();
  const sumData = await sumRes.json();
  const kData = await kRes.json();

  if (sumData.success) {
    const {masuk,keluar,saldo} = sumData.data;
    document.getElementById('s-masuk').textContent = 'Rp '+(parseInt(masuk||0)).toLocaleString('id-ID');
    document.getElementById('s-keluar').textContent = 'Rp '+(parseInt(keluar||0)).toLocaleString('id-ID');
    document.getElementById('s-saldo').textContent = 'Rp '+(parseInt(saldo||0)).toLocaleString('id-ID');
  }
  if (kData.success) {
    const sel = document.getElementById('kas-kampanye');
    sel.innerHTML = '<option value="">— Tidak terkait kampanye —</option>' + kData.data.map(k=>`<option value="${k.id}">${k.judul}</option>`).join('');
  }
  if (!kasData.success || !kasData.data.length) {
    document.getElementById('kas-list').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Belum ada entri kas.</div>';
    return;
  }
  document.getElementById('kas-list').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">
        ${['Tanggal','Keterangan','Kampanye','Jenis','Nominal'].map(h=>`<th style="padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--ink-soft)">${h}</th>`).join('')}
      </tr></thead>
      <tbody>${kasData.data.map(k=>{
        const tgl = new Date(k.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
        const masuk = k.jenis==='masuk';
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:12px 16px;font-size:12px;color:var(--ink-soft)">${tgl}</td>
          <td style="padding:12px 16px;font-size:13px">${k.keterangan}</td>
          <td style="padding:12px 16px;font-size:12px;color:var(--ink-soft)">${k.judul||'—'}</td>
          <td style="padding:12px 16px"><span style="font-size:11px;padding:3px 10px;border-radius:99px;background:${masuk?'#E8F4ED':'#FDECEA'};color:${masuk?'var(--forest)':'var(--rust)'}">${masuk?'Masuk':'Keluar'}</span></td>
          <td style="padding:12px 16px;font-size:13px;font-weight:600;color:${masuk?'var(--forest)':'var(--rust)'}">${masuk?'+':'−'} Rp ${parseInt(k.nominal).toLocaleString('id-ID')}</td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;
}

async function saveKas() {
  const body = {
    jenis: document.getElementById('jenis').value,
    keterangan: document.getElementById('keterangan').value.trim(),
    nominal: parseInt(document.getElementById('nominal').value),
    kampanye_id: document.getElementById('kas-kampanye').value || null
  };
  if (!body.keterangan || !body.nominal) { alert('Keterangan dan nominal wajib diisi.'); return; }
  const res = await fetch('/api/kas', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken}, body: JSON.stringify(body) });
  const data = await res.json();
  if (data.success) { document.getElementById('form-overlay').style.display='none'; showToast(data.message); loadKas(); }
  else alert(data.message);
}
loadKas();
</script>
@endsection
