@extends('layouts.admin')
@section('title', 'Iuran Bulanan')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <a href="/admin/keuangan" style="font-size:13px;color:var(--ink-soft)">← Keuangan</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Iuran Bulanan</div>
    </div>
    <div style="display:flex;gap:8px">
      <a href="/admin/keuangan/iuran/matrix" class="btn-secondary" style="padding:9px 18px;font-size:13px"><i class="fa-solid fa-table-cells" style="margin-right:6px"></i>Matrix Compliance</a>
      <button onclick="openPeriodeForm()" class="btn-primary">+ Tambah Periode</button>
    </div>
  </div>

  {{-- Tabs --}}
  <div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:1.5rem">
    <button onclick="showTab('periode')" id="tab-periode" class="tab-btn tab-active">Periode & Generate</button>
    <button onclick="showTab('tagihan')" id="tab-tagihan" class="tab-btn">Tagihan</button>
    <button onclick="showTab('bayar')"   id="tab-bayar"   class="tab-btn">Pembayaran Masuk</button>
  </div>

  <div id="panel-periode">
    <div id="periode-list"><div style="text-align:center;padding:2rem;color:var(--ink-soft)">Memuat...</div></div>
  </div>
  <div id="panel-tagihan" style="display:none">
    <div style="display:flex;gap:8px;margin-bottom:1rem;align-items:center;flex-wrap:wrap">
      <select id="tf-periode" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none" onchange="loadTagihan()">
        <option value="">Semua Periode</option>
      </select>
      <select id="tf-status" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none" onchange="loadTagihan()">
        <option value="">Semua Status</option>
        <option value="belum">Belum</option>
        <option value="pending">Pending</option>
        <option value="lunas">Lunas</option>
        <option value="dispensasi">Dispensasi</option>
      </select>
    </div>
    <div id="tagihan-list"></div>
    <div id="tagihan-pagination" style="display:flex;gap:8px;justify-content:center;padding:1rem 0"></div>
  </div>
  <div id="panel-bayar" style="display:none">
    <div style="display:flex;gap:8px;margin-bottom:1rem;align-items:center">
      <select id="bf-status" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none" onchange="loadBayar()">
        <option value="pending">Menunggu Verifikasi</option>
        <option value="">Semua</option>
        <option value="verified">Terverifikasi</option>
        <option value="rejected">Ditolak</option>
      </select>
    </div>
    <div id="bayar-list"></div>
  </div>
</div>

{{-- Periode Form --}}
<div id="periode-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:440px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem">Tambah Periode Iuran</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Bulan</label>
        <select id="pf-bulan" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i=>$bln)
          <option value="{{ $i+1 }}" @if($i+1 == date('n')) selected @endif>{{ $bln }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="flabel">Tahun</label>
        <input type="number" id="pf-tahun" value="{{ date('Y') }}" min="2020" max="2030" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Nominal (Rp)</label>
        <input type="number" id="pf-nominal" placeholder="150000" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Jatuh Tempo</label>
        <input type="date" id="pf-jatuh" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
    </div>
    <div style="margin-bottom:1.5rem">
      <label class="flabel">Keterangan (opsional)</label>
      <input type="text" id="pf-ket" placeholder="Cth: Termasuk biaya keamanan" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="savePeriode()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="document.getElementById('periode-overlay').style.display='none'" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

{{-- Bayar Form (Manual entry by admin) --}}
<div id="bayar-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:420px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1rem">Catat Pembayaran</div>
    <div style="font-size:13px;color:var(--ink-soft);margin-bottom:1.5rem" id="bayar-info">—</div>
    <input type="hidden" id="bf-tagihan-id">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Nominal (Rp)</label>
        <input type="number" id="bf-nominal" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Metode</label>
        <select id="bf-metode" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="tunai">Tunai</option>
          <option value="transfer">Transfer</option>
          <option value="qris">QRIS</option>
          <option value="gopay">GoPay</option>
          <option value="ovo">OVO</option>
          <option value="dana">DANA</option>
        </select>
      </div>
    </div>
    <div style="margin-bottom:1.5rem">
      <label class="flabel">Catatan</label>
      <input type="text" id="bf-catatan" placeholder="Opsional" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
    </div>
    <div style="display:flex;gap:8px">
      <button onclick="saveBayar(true)" class="btn-primary" style="flex:1;justify-content:center">Simpan + Langsung Verifikasi</button>
      <button onclick="document.getElementById('bayar-overlay').style.display='none'" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>

<style>
.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}
.tab-btn{padding:10px 20px;border:none;background:none;font-size:13px;font-weight:500;color:var(--ink-soft);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px}
.tab-active{color:var(--forest);border-bottom-color:var(--forest)!important}
</style>
@endsection
@section('scripts')
<script>
const bulanNama = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const statusColor = { belum:'#FDECEA:#B5401A', pending:'#FFF3CD:#C8A030', lunas:'#E8F4ED:var(--forest)', dispensasi:'#EFEFED:var(--ink-soft)' };
function rp(n){ return 'Rp '+(parseInt(n)||0).toLocaleString('id-ID'); }

function showTab(t) {
  ['periode','tagihan','bayar'].forEach(id => {
    document.getElementById('panel-'+id).style.display = id===t?'block':'none';
    document.getElementById('tab-'+id).classList.toggle('tab-active', id===t);
  });
  if (t==='tagihan') loadTagihan();
  if (t==='bayar')   loadBayar();
}

async function loadPeriode() {
  const r = await fetch('/api/iuran/periode');
  const j = await r.json();
  if (!j.success) return;

  if (!j.data.length) {
    document.getElementById('periode-list').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Belum ada periode iuran. Tambahkan untuk memulai.</div>';
    return;
  }

  // Populate tagihan filter
  document.getElementById('tf-periode').innerHTML = '<option value="">Semua Periode</option>' + j.data.map(p=>`<option value="${p.id}">${p.nama_bulan}</option>`).join('');

  document.getElementById('periode-list').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">
        ${['Periode','Nominal','Jatuh Tempo','Unit','Lunas','Pending','Belum','Tindakan'].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}
      </tr></thead>
      <tbody>${j.data.map(p=>{
        const pct = p.total_unit > 0 ? Math.round(p.lunas_count/p.total_unit*100) : 0;
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:10px 16px;font-size:13px;font-weight:500">${p.nama_bulan}</td>
          <td style="padding:10px 16px;font-size:13px">${rp(p.nominal)}</td>
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${p.jatuh_tempo}</td>
          <td style="padding:10px 16px;font-size:13px">${p.total_unit}</td>
          <td style="padding:10px 16px">
            <span style="font-size:12px;font-weight:600;color:var(--forest)">${p.lunas_count}</span>
            <span style="font-size:10px;color:var(--ink-mute)"> / ${p.total_unit}</span>
            <div style="height:4px;background:var(--parchment);border-radius:99px;margin-top:4px;width:60px">
              <div style="height:100%;border-radius:99px;background:var(--forest-mid);width:${pct}%"></div>
            </div>
          </td>
          <td style="padding:10px 16px;font-size:12px;color:#C8A030;font-weight:500">${p.pending_count}</td>
          <td style="padding:10px 16px;font-size:12px;color:var(--rust);font-weight:500">${p.belum_count}</td>
          <td style="padding:10px 16px;white-space:nowrap">
            ${p.total_unit === 0 ? `<button onclick="generateTagihan(${p.id},'${p.nama_bulan}')" class="btn-primary" style="padding:6px 14px;font-size:12px">Generate Tagihan</button>` :
              `<span style="font-size:11px;color:var(--ink-soft)">${p.total_unit} tagihan aktif</span>`}
            ${p.total_unit === 0 ? `<button onclick="delPeriode(${p.id})" style="margin-left:6px;font-size:11px;padding:4px 8px;border:1px solid #FDECEA;border-radius:6px;background:#FDECEA;color:var(--rust);cursor:pointer">Hapus</button>` : ''}
          </td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;
}

async function generateTagihan(periodeId, namaBulan) {
  if (!confirm(`Generate tagihan iuran untuk semua unit aktif - ${namaBulan}?`)) return;
  const r = await fetch(`/api/iuran/tagihan/${periodeId}/generate`, { method:'POST', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  showToast(j.message);
  if (j.success) loadPeriode();
}

async function loadTagihan(page=1) {
  const params = new URLSearchParams({ page, per_page:30 });
  const pId = document.getElementById('tf-periode').value;
  const st  = document.getElementById('tf-status').value;
  if (pId) params.set('periode_id', pId);
  if (st)  params.set('status', st);

  const r = await fetch('/api/iuran/tagihan?' + params.toString());
  const j = await r.json();
  if (!j.success || !j.data.length) {
    document.getElementById('tagihan-list').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-soft)">Tidak ada tagihan.</div>';
    return;
  }
  const sc = (s) => s==='lunas'?'#E8F4ED:var(--forest)':s==='pending'?'#FFF3CD:#C8A030':s==='dispensasi'?'#EFEFED:var(--ink-soft)':'#FDECEA:var(--rust)';

  document.getElementById('tagihan-list').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">
        ${['Unit','Periode','Nominal','Status','Aksi'].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}
      </tr></thead>
      <tbody>${j.data.map(t=>{
        const [bg,col] = sc(t.status).split(':');
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:10px 16px;font-size:13px;font-weight:500">Blok ${t.blok}${t.nomor}</td>
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${bulanNama[t.bulan]} ${t.tahun}</td>
          <td style="padding:10px 16px;font-size:13px">${rp(t.nominal)}</td>
          <td style="padding:10px 16px"><span style="font-size:11px;padding:3px 10px;border-radius:99px;background:${bg};color:${col}">${t.status}</span></td>
          <td style="padding:10px 16px;white-space:nowrap">
            ${t.status==='belum'||t.status==='pending' ? `<button onclick="openBayar(${t.id},'${t.blok}${t.nomor}','${bulanNama[t.bulan]} ${t.tahun}',${t.nominal})" style="font-size:11px;padding:4px 10px;border:1px solid var(--forest);border-radius:6px;background:var(--forest-pale);color:var(--forest);cursor:pointer">Catat Bayar</button>` : ''}
            ${t.status==='belum' ? `<button onclick="dispensasi(${t.id})" style="margin-left:4px;font-size:11px;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer">Dispensasi</button>` : ''}
          </td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;

  const meta = j.meta;
  let pag = '';
  if (meta.pages > 1) for (let p=1;p<=meta.pages;p++) pag += `<button onclick="loadTagihan(${p})" style="padding:6px 12px;border:1.5px solid ${p===meta.page?'var(--forest)':'var(--border)'};background:${p===meta.page?'var(--forest)':'#fff'};color:${p===meta.page?'#fff':'var(--ink-soft)'};border-radius:6px;cursor:pointer;font-size:13px">${p}</button>`;
  document.getElementById('tagihan-pagination').innerHTML = pag;
}

async function loadBayar() {
  const status = document.getElementById('bf-status').value;
  const params = new URLSearchParams();
  if (status) params.set('status', status);
  const r = await fetch('/api/iuran/bayar?' + params.toString());
  const j = await r.json();
  if (!j.success || !j.data.length) {
    document.getElementById('bayar-list').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-soft)">Tidak ada data pembayaran.</div>';
    return;
  }
  document.getElementById('bayar-list').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">
        ${['Unit','Periode','Nominal','Metode','Status','Aksi'].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}
      </tr></thead>
      <tbody>${j.data.map(b=>{
        const isPending = b.status==='pending';
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:10px 16px;font-size:13px;font-weight:500">Blok ${b.blok}${b.nomor}</td>
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${bulanNama[b.bulan]} ${b.tahun}</td>
          <td style="padding:10px 16px;font-size:13px;font-weight:500;color:var(--forest)">${rp(b.nominal)}</td>
          <td style="padding:10px 16px;font-size:12px">${b.metode}</td>
          <td style="padding:10px 16px"><span style="font-size:11px;padding:3px 10px;border-radius:99px;background:${b.status==='verified'?'#E8F4ED':b.status==='pending'?'#FFF3CD':'#FDECEA'};color:${b.status==='verified'?'var(--forest)':b.status==='pending'?'#C8A030':'var(--rust)'}">${b.status}</span></td>
          <td style="padding:10px 16px;white-space:nowrap">
            ${b.bukti_url?`<a href="${b.bukti_url}" target="_blank" style="font-size:11px;color:var(--forest-light);margin-right:6px"><i class="fa-solid fa-paperclip" style="margin-right:3px"></i>Bukti</a>`:''}
            ${isPending?`<button onclick="verifyBayar(${b.id},'verify')" style="font-size:11px;padding:4px 10px;border:1px solid var(--forest);border-radius:6px;background:var(--forest-pale);color:var(--forest);cursor:pointer">Verifikasi</button>
            <button onclick="verifyBayar(${b.id},'reject')" style="margin-left:4px;font-size:11px;padding:4px 8px;border:1px solid #FDECEA;border-radius:6px;background:#FDECEA;color:var(--rust);cursor:pointer">Tolak</button>`:''}
          </td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;
}

function openPeriodeForm() {
  const d = new Date();
  d.setDate(25); // default jatuh tempo tgl 25
  document.getElementById('pf-jatuh').value = d.toISOString().slice(0,10);
  document.getElementById('periode-overlay').style.display='flex';
}

async function savePeriode() {
  const body = {
    bulan:       parseInt(document.getElementById('pf-bulan').value),
    tahun:       parseInt(document.getElementById('pf-tahun').value),
    nominal:     parseInt(document.getElementById('pf-nominal').value),
    jatuh_tempo: document.getElementById('pf-jatuh').value,
    keterangan:  document.getElementById('pf-ket').value||null,
  };
  if (!body.nominal || !body.jatuh_tempo) { alert('Nominal dan jatuh tempo wajib diisi.'); return; }
  const r = await fetch('/api/iuran/periode',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},body:JSON.stringify(body)});
  const j = await r.json();
  if (j.success) { document.getElementById('periode-overlay').style.display='none'; showToast(j.message); loadPeriode(); }
  else alert(j.message);
}

async function delPeriode(id) {
  if (!confirm('Hapus periode ini?')) return;
  const r = await fetch(`/api/iuran/periode/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':_csrfToken}});
  const j = await r.json();
  if (j.success) { showToast(j.message); loadPeriode(); }
  else alert(j.message);
}

function openBayar(tagihanId, unit, periode, nominal) {
  document.getElementById('bf-tagihan-id').value = tagihanId;
  document.getElementById('bayar-info').textContent = `Unit ${unit} — ${periode} — ${rp(nominal)}`;
  document.getElementById('bf-nominal').value = nominal;
  document.getElementById('bf-metode').value = 'tunai';
  document.getElementById('bf-catatan').value = '';
  document.getElementById('bayar-overlay').style.display='flex';
}

async function saveBayar(autoVerify) {
  const body = {
    iuran_tagihan_id: parseInt(document.getElementById('bf-tagihan-id').value),
    nominal:          parseInt(document.getElementById('bf-nominal').value),
    metode:           document.getElementById('bf-metode').value,
    catatan:          document.getElementById('bf-catatan').value||null,
  };
  const r = await fetch('/api/iuran/bayar',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},body:JSON.stringify(body)});
  const j = await r.json();
  if (!j.success) { alert(j.message); return; }

  if (autoVerify) {
    const r2 = await fetch(`/api/iuran/bayar/${j.data.id}/verify`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},body:JSON.stringify({action:'verify'})});
    const j2 = await r2.json();
    showToast(j2.message);
  } else {
    showToast(j.message);
  }
  document.getElementById('bayar-overlay').style.display='none';
  loadPeriode(); loadTagihan();
}

async function verifyBayar(id, action) {
  const msg = action==='verify' ? 'Verifikasi pembayaran ini?' : 'Tolak pembayaran ini?';
  if (!confirm(msg)) return;
  const r = await fetch(`/api/iuran/bayar/${id}/verify`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},body:JSON.stringify({action})});
  const j = await r.json();
  showToast(j.message);
  loadBayar();
}

async function dispensasi(id) {
  if (!confirm('Tandai sebagai dispensasi (bebas iuran)?')) return;
  const r = await fetch(`/api/iuran/tagihan/${id}/dispensasi`,{method:'PUT',headers:{'X-CSRF-TOKEN':_csrfToken}});
  const j = await r.json();
  showToast(j.message);
  loadTagihan();
}

loadPeriode();
</script>
@endsection
