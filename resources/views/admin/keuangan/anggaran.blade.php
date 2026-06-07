@extends('layouts.admin')
@section('title', 'Anggaran')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <a href="/admin/keuangan" style="font-size:13px;color:var(--ink-soft)">← Keuangan</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Anggaran</div>
    </div>
    <button onclick="openForm()" class="btn-primary">+ Tambah Pos Anggaran</button>
  </div>

  {{-- Filter --}}
  <div style="display:flex;gap:8px;margin-bottom:1rem;align-items:center">
    <label style="font-size:13px;font-weight:500">Tahun:</label>
    <select id="f-tahun" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none" onchange="loadData()">
      <option value="">Semua</option>
      @for($y = date('Y'); $y >= 2024; $y--)<option value="{{ $y }}" @if($y == date('Y')) selected @endif>{{ $y }}</option>@endfor
    </select>
    <label style="font-size:13px;font-weight:500">Kampanye:</label>
    <select id="f-kampanye" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;background:#fff;outline:none" onchange="loadData()">
      <option value="">Semua</option>
    </select>
  </div>

  <div id="data-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
</div>

{{-- Form Modal --}}
<div id="form-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:480px;margin:1rem">
    <div style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--forest);margin-bottom:1.5rem" id="form-title">Tambah Pos Anggaran</div>
    <input type="hidden" id="edit-id">

    <div style="margin-bottom:1rem">
      <label class="flabel">Nama Pos Anggaran</label>
      <input type="text" id="f-pos" placeholder="Cth: Biaya Kebersihan, Pengecatan..." style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Estimasi (Rp)</label>
        <input type="number" id="f-estimasi" placeholder="0" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
      <div>
        <label class="flabel">Tahun</label>
        <input type="number" id="f-tahun" value="{{ date('Y') }}" min="2020" max="2030" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
      <div>
        <label class="flabel">Sumber Dana</label>
        <select id="f-sumber" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="campuran">Campuran</option>
          <option value="iuran">Iuran</option>
          <option value="donasi">Donasi</option>
          <option value="saldo">Saldo Kas</option>
        </select>
      </div>
      <div>
        <label class="flabel">Kampanye (opsional)</label>
        <select id="f-kampanye-sel" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none;background:#fff">
          <option value="">— Tidak terkait —</option>
        </select>
      </div>
    </div>

    <div style="margin-bottom:1.5rem">
      <label class="flabel">Catatan</label>
      <input type="text" id="f-catatan" placeholder="Opsional" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;outline:none">
    </div>

    <div style="display:flex;gap:8px">
      <button onclick="saveData()" class="btn-primary" style="flex:1;justify-content:center">Simpan</button>
      <button onclick="closeForm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>
<style>.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}</style>
@endsection
@section('scripts')
<script>
const sumberLabel = { campuran:'Campuran', iuran:'Iuran', donasi:'Donasi', saldo:'Saldo Kas' };
function rp(n) { return 'Rp '+(parseInt(n)||0).toLocaleString('id-ID'); }

async function loadDeps() {
  const r = await fetch('/api/kampanye');
  const j = await r.json();
  if (j.success) {
    const opts = '<option value="">— Tidak terkait —</option>' + j.data.map(k=>`<option value="${k.id}">${k.judul}</option>`).join('');
    document.getElementById('f-kampanye').innerHTML = '<option value="">Semua</option>' + j.data.map(k=>`<option value="${k.id}">${k.judul}</option>`).join('');
    document.getElementById('f-kampanye-sel').innerHTML = opts;
  }
}

async function loadData() {
  const tahun    = document.getElementById('f-tahun').value;
  const kampanye = document.getElementById('f-kampanye').value;
  const params   = new URLSearchParams();
  if (tahun)    params.set('tahun', tahun);
  if (kampanye) params.set('kampanye_id', kampanye);

  document.getElementById('data-list').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-soft)">Memuat...</div>';
  const r = await fetch('/api/anggaran?' + params.toString());
  const j = await r.json();

  if (!j.success || !j.data.length) {
    document.getElementById('data-list').innerHTML = '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Tidak ada pos anggaran.</div>';
    return;
  }

  document.getElementById('data-list').innerHTML = `<div class="card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">
        ${['Pos Anggaran','Tahun','Sumber Dana','Kampanye','Estimasi','Realisasi','Sisa',''].map(h=>`<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}
      </tr></thead>
      <tbody>${j.data.map(a=>{
        const sisa   = a.estimasi - (a.realisasi||0);
        const pct    = a.estimasi > 0 ? Math.min(100, Math.round((a.realisasi||0)/a.estimasi*100)) : 0;
        const over   = sisa < 0;
        return `<tr style="border-top:1px solid var(--border)">
          <td style="padding:10px 16px;font-size:13px;font-weight:500">${a.pos}</td>
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${a.tahun||'—'}</td>
          <td style="padding:10px 16px"><span style="font-size:11px;padding:2px 8px;border-radius:99px;background:var(--forest-pale);color:var(--forest)">${sumberLabel[a.sumber_dana]||a.sumber_dana}</span></td>
          <td style="padding:10px 16px;font-size:12px;color:var(--ink-soft)">${a.kampanye?.judul||'—'}</td>
          <td style="padding:10px 16px;font-size:13px">${rp(a.estimasi)}</td>
          <td style="padding:10px 16px">
            <div style="font-size:13px;font-weight:500;color:var(--rust)">${rp(a.realisasi||0)}</div>
            <div style="height:4px;background:var(--parchment);border-radius:99px;margin-top:4px;width:80px">
              <div style="height:100%;border-radius:99px;background:${over?'var(--rust)':'var(--forest-mid)'};width:${pct}%"></div>
            </div>
            <div style="font-size:10px;color:var(--ink-mute);margin-top:2px">${pct}%</div>
          </td>
          <td style="padding:10px 16px;font-size:13px;font-weight:600;color:${over?'var(--rust)':'var(--forest)'}">${rp(sisa)}</td>
          <td style="padding:10px 16px;white-space:nowrap">
            <button onclick='editData(${JSON.stringify(a)})' style="font-size:11px;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;margin-right:4px">Edit</button>
            ${a.realisasi===0?`<button onclick='delData(${a.id})' style="font-size:11px;padding:4px 8px;border:1px solid #FDECEA;border-radius:6px;background:#FDECEA;color:var(--rust);cursor:pointer">Hapus</button>`:''}
          </td>
        </tr>`;
      }).join('')}</tbody>
    </table>
  </div>`;
}

function openForm() {
  document.getElementById('edit-id').value = '';
  document.getElementById('form-title').textContent = 'Tambah Pos Anggaran';
  document.getElementById('f-pos').value = '';
  document.getElementById('f-estimasi').value = '';
  document.getElementById('f-tahun').value = new Date().getFullYear();
  document.getElementById('f-sumber').value = 'campuran';
  document.getElementById('f-kampanye-sel').value = '';
  document.getElementById('f-catatan').value = '';
  document.getElementById('form-overlay').style.display = 'flex';
}
function closeForm() { document.getElementById('form-overlay').style.display='none'; }

function editData(a) {
  document.getElementById('edit-id').value = a.id;
  document.getElementById('form-title').textContent = 'Edit Pos Anggaran';
  document.getElementById('f-pos').value = a.pos;
  document.getElementById('f-estimasi').value = a.estimasi;
  document.getElementById('f-tahun').value = a.tahun||'';
  document.getElementById('f-sumber').value = a.sumber_dana||'campuran';
  document.getElementById('f-kampanye-sel').value = a.kampanye_id||'';
  document.getElementById('f-catatan').value = a.catatan||'';
  document.getElementById('form-overlay').style.display = 'flex';
}

async function saveData() {
  const id = document.getElementById('edit-id').value;
  const body = {
    pos:         document.getElementById('f-pos').value.trim(),
    estimasi:    parseInt(document.getElementById('f-estimasi').value),
    tahun:       parseInt(document.getElementById('f-tahun').value)||null,
    sumber_dana: document.getElementById('f-sumber').value,
    kampanye_id: document.getElementById('f-kampanye-sel').value||null,
    catatan:     document.getElementById('f-catatan').value||null,
  };
  if (!body.pos) { alert('Nama pos wajib diisi.'); return; }
  const url = id ? `/api/anggaran/${id}` : '/api/anggaran';
  const method = id ? 'PUT' : 'POST';
  const r = await fetch(url, { method, headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken}, body:JSON.stringify(body) });
  const j = await r.json();
  if (j.success) { closeForm(); showToast(j.message); loadData(); }
  else alert(j.message);
}

async function delData(id) {
  if (!confirm('Hapus pos anggaran ini?')) return;
  const r = await fetch(`/api/anggaran/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  if (j.success) { showToast(j.message); loadData(); }
  else alert(j.message);
}

loadDeps().then(() => loadData());
</script>
@endsection
