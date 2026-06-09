@extends('layouts.admin')
@section('title', 'Pengumuman')
@section('content')
<div class="container">
  <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;align-items:flex-end;margin-bottom:2rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Kirim Pengumuman</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem">
    <div class="card">
      <div style="font-size:15px;font-weight:500;margin-bottom:1.5rem">Buat pengumuman baru</div>
      <div style="margin-bottom:1rem">
        <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Judul</label>
        <input type="text" id="judul" placeholder="Judul pengumuman"
          style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none">
      </div>
      <div style="margin-bottom:1rem">
        <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Isi pesan</label>
        <textarea id="isi" rows="6" placeholder="Tulis pesan untuk warga RT 10..."
          style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;resize:vertical"></textarea>
      </div>
      <div style="margin-bottom:1rem">
        <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Target penerima</label>
        <div style="display:flex;gap:8px">
          @foreach(['semua'=>'<i class="fa-solid fa-users" style="margin-right:5px"></i>Semua warga','donatur'=>'<i class="fa-solid fa-heart" style="margin-right:5px"></i>Donatur saja'] as $v=>$l)
          <div onclick="selectTarget('{{ $v }}')" data-target="{{ $v }}"
            style="flex:1;padding:10px;border-radius:var(--radius-sm);border:1.5px solid {{ $v==='semua' ? 'var(--forest)' : 'var(--border)' }};background:{{ $v==='semua' ? '#F0F7F3' : '#fff' }};color:{{ $v==='semua' ? 'var(--forest)' : 'var(--ink-soft)' }};text-align:center;cursor:pointer;font-size:13px;font-weight:500;transition:all 0.15s">
            {!! $l !!}
          </div>
          @endforeach
        </div>
        <input type="hidden" id="target" value="semua">
      </div>

      {{-- Channel toggles --}}
      <div style="margin-bottom:1.5rem">
        <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px">Kirim juga via</label>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <label id="toggle-email" onclick="toggleChannel('email')" style="display:flex;align-items:center;gap:7px;padding:8px 14px;border-radius:8px;border:1.5px solid var(--border);background:#fff;cursor:pointer;font-size:13px;font-weight:500;color:var(--ink-soft);user-select:none;transition:all .15s">
            <i class="fa fa-envelope"></i> Email
          </label>
          <label id="toggle-wa" onclick="toggleChannel('wa')" style="display:flex;align-items:center;gap:7px;padding:8px 14px;border-radius:8px;border:1.5px solid var(--border);background:#fff;cursor:pointer;font-size:13px;font-weight:500;color:var(--ink-soft);user-select:none;transition:all .15s">
            <i class="fa-brands fa-whatsapp"></i> WhatsApp Blast
          </label>
        </div>
        <div id="wa-note" style="display:none;margin-top:8px;font-size:12px;color:var(--ink-soft)">
          <i class="fa fa-circle-info"></i> WA blast dikirim ke semua warga yang memiliki nomor WA terdaftar.
        </div>
      </div>

      <button onclick="kirimPengumuman()" class="btn-primary" style="width:100%;justify-content:center">
        <i class="fa-solid fa-bullhorn" style="margin-right:6px"></i>Kirim Pengumuman
      </button>
    </div>

    <div>
      <div style="font-size:15px;font-weight:500;margin-bottom:1rem">Pengumuman sebelumnya</div>
      <div id="pengumuman-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const _channels = { email: false, wa: false };

function selectTarget(val) {
  document.getElementById('target').value = val;
  document.querySelectorAll('[data-target]').forEach(el => {
    const active = el.dataset.target===val;
    el.style.borderColor = active?'var(--forest)':'var(--border)';
    el.style.background = active?'#F0F7F3':'#fff';
    el.style.color = active?'var(--forest)':'var(--ink-soft)';
  });
}

function toggleChannel(ch) {
  _channels[ch] = !_channels[ch];
  const el = document.getElementById('toggle-' + ch);
  const on = _channels[ch];
  const colors = ch === 'wa'
    ? { bg:'#E8F7EE', border:'#25D366', color:'#128C4E' }
    : { bg:'#EBF0F9', border:'#2d5aa8', color:'#2d5aa8' };
  el.style.background   = on ? colors.bg    : '#fff';
  el.style.borderColor  = on ? colors.border : 'var(--border)';
  el.style.color        = on ? colors.color  : 'var(--ink-soft)';
  if (ch === 'wa') document.getElementById('wa-note').style.display = on ? '' : 'none';
}

async function loadPengumuman() {
  const res = await fetch('/api/pengumuman');
  const data = await res.json();
  if (!data.success || !data.data.length) {
    document.getElementById('pengumuman-list').innerHTML = '<div style="font-size:13px;color:var(--ink-soft)">Belum ada pengumuman.</div>'; return;
  }
  document.getElementById('pengumuman-list').innerHTML = data.data.map(p => {
    const tgl = new Date(p.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});
    return `<div class="card" style="margin-bottom:1rem">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
        <div style="font-size:14px;font-weight:500;color:var(--forest)">${p.judul}</div>
        <span style="font-size:11px;padding:2px 8px;border-radius:99px;background:${p.target==='semua'?'#E8F4ED':'var(--gold-pale)'};color:${p.target==='semua'?'var(--forest)':'#7A5C00'};white-space:nowrap;margin-left:8px">${p.target==='semua'?'Semua':'Donatur'}</span>
      </div>
      <div style="font-size:12px;color:var(--ink-soft);margin-bottom:6px">${tgl}</div>
      <div style="font-size:13px;color:var(--ink-mid);line-height:1.6;white-space:pre-line">${p.isi.substring(0,150)}${p.isi.length>150?'...':''}</div>
    </div>`;
  }).join('');
}

async function kirimPengumuman() {
  const body = {
    judul:       document.getElementById('judul').value.trim(),
    isi:         document.getElementById('isi').value.trim(),
    target:      document.getElementById('target').value,
    kirim_email: _channels.email,
    kirim_wa:    _channels.wa,
  };
  if (!body.judul || !body.isi) { alert('Judul dan isi wajib diisi.'); return; }
  const res = await fetch('/api/pengumuman', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken}, body: JSON.stringify(body) });
  const data = await res.json();
  if (data.success) {
    document.getElementById('judul').value = '';
    document.getElementById('isi').value = '';
    showToast(data.message);
    loadPengumuman();
  } else alert(data.message);
}
loadPengumuman();
</script>
@endsection
