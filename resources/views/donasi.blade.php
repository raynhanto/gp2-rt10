@extends('layouts.app')
@section('title', 'Donasi')
@section('styles')
<style>
@media(max-width:768px){
  #donasi-grid{grid-template-columns:1fr!important;gap:1.5rem!important}
}
</style>
@endsection
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container">
  <div id="donasi-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:start">

    <!-- LEFT: Info -->
    <div class="fade-in">
      <div class="section-label">Donasi</div>
      <div class="section-title" style="margin-bottom:1rem">Setiap rupiah<br>berarti untuk kita</div>
      <p style="font-size:14px;color:var(--ink-soft);line-height:1.75;margin-bottom:1.75rem">Donasi kamu langsung digunakan untuk kampanye yang dipilih. Semua penggunaan dana terdokumentasi dan bisa diakses oleh seluruh warga RT 10.</p>

      <!-- Langkah Donasi -->
      <div class="card" style="margin-bottom:1.25rem;padding:1.25rem">
        <div style="font-size:11px;font-weight:700;color:var(--forest);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:1rem">Langkah Donasi</div>
        @php $steps = [
          ['Pilih kampanye & isi nominal', 'Pilih kampanye aktif dan masukkan jumlah donasi'],
          ['Pilih metode pembayaran', 'QRIS, transfer bank, atau GoPay/OVO'],
          ['Lakukan pembayaran', 'Scan QRIS atau transfer ke rekening di bawah'],
          ['Upload bukti pembayaran', 'Screenshot transfer atau struk QRIS kamu'],
          ['Verifikasi oleh pengurus', 'Dana masuk ke kas RT & donasi tercatat'],
        ]; @endphp
        @foreach($steps as $i => [$title, $sub])
        <div style="display:flex;gap:12px;{{ !$loop->last ? 'margin-bottom:0' : '' }}">
          <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0">
            <div style="width:26px;height:26px;border-radius:50%;background:{{ $loop->last ? 'var(--forest)' : 'var(--forest-pale)' }};border:1.5px solid {{ $loop->last ? 'var(--forest)' : 'rgba(26,61,43,0.2)' }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:{{ $loop->last ? '#fff' : 'var(--forest)' }}">{{ $i+1 }}</div>
            @if(!$loop->last)
            <div style="width:1.5px;height:24px;background:rgba(26,61,43,0.12);margin:3px 0"></div>
            @endif
          </div>
          <div style="padding-top:3px;{{ !$loop->last ? 'padding-bottom:4px' : '' }}">
            <div style="font-size:13px;font-weight:600;color:var(--ink);line-height:1.3">{{ $title }}</div>
            <div style="font-size:12px;color:var(--ink-soft);margin-top:2px">{{ $sub }}</div>
          </div>
        </div>
        @endforeach
      </div>

      @foreach([
        ['🔒', 'Dana aman & terverifikasi', 'Dikelola langsung oleh Bendahara RT 10'],
        ['📊', 'Laporan bulanan terbuka', 'Rekap penggunaan dana bisa diakses semua warga'],
        ['💬', 'Update progres real-time', 'Status donasi bisa dipantau di dashboard kamu'],
      ] as [$icon, $title, $sub])
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:{{ $loop->last ? '0' : '12px' }}">
        <div style="width:34px;height:34px;background:var(--forest);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0">{{ $icon }}</div>
        <div>
          <div style="font-size:13px;color:var(--ink-mid);font-weight:500">{{ $title }}</div>
          <div style="font-size:12px;color:var(--ink-soft)">{{ $sub }}</div>
        </div>
      </div>
      @endforeach
    </div>

    <!-- RIGHT: Form -->
    <div class="fade-in">
      @guest
      <div style="background:var(--gold-pale);border:1px solid rgba(200,168,75,0.3);border-radius:var(--radius-sm);padding:1rem 1.25rem;margin-bottom:1.25rem;font-size:13px;color:#7A5C00">
        <i class="fa fa-circle-info" style="margin-right:6px"></i>
        <a href="/login" style="font-weight:600;color:var(--forest)">Masuk</a> untuk menyimpan riwayat donasi kamu.
      </div>
      @endguest

      <div class="card">
        <div style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px">Pilih kampanye</div>
        <select id="kampanye-select" onchange="onKampanyeChange(this.value)"
          style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;color:var(--ink);outline:none;margin-bottom:10px;background:#fff">
          <option value="">Memuat kampanye...</option>
        </select>

        <div id="kampanye-info" style="display:none;background:var(--cream);border-radius:var(--radius-sm);padding:12px 14px;margin-bottom:1.25rem;border:1px solid var(--border)"></div>

        <div style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px">Nominal donasi</div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:10px" id="amt-grid">
          @foreach([20000=>'Rp 20rb',50000=>'Rp 50rb',100000=>'Rp 100rb',200000=>'Rp 200rb'] as $val=>$lbl)
          <div onclick="pickAmt(this,{{ $val }})" data-amt="{{ $val }}"
            style="padding:10px 4px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:#fff;font-size:13px;font-weight:600;color:var(--ink-mid);cursor:pointer;text-align:center;transition:all 0.15s">
            {{ $lbl }}
          </div>
          @endforeach
        </div>
        <div style="position:relative;margin-bottom:10px">
          <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:14px;color:var(--ink-soft);pointer-events:none;font-weight:500">Rp</span>
          <input type="text" inputmode="numeric" id="amt-input" placeholder="0" value="20.000"
            oninput="onAmtInput(this)"
            style="width:100%;padding:11px 14px 11px 38px;border:1.5px solid var(--forest);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:15px;font-weight:700;outline:none;color:var(--ink);box-sizing:border-box">
        </div>

        <div onclick="toggleAnonym()" id="anonym-toggle"
          style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:#fff;cursor:pointer;margin-bottom:1.25rem;transition:all 0.15s;user-select:none">
          <div id="anonym-check" style="width:18px;height:18px;border-radius:5px;border:1.5px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all 0.15s">
            <span id="anonym-icon" style="font-size:11px;display:none">✓</span>
          </div>
          <div>
            <div style="font-size:13px;font-weight:500;color:var(--ink)">Sembunyikan nama saya (anonim)</div>
            <div style="font-size:11px;color:var(--ink-soft);margin-top:1px">Tampil sebagai: <span id="donor-name-preview">@auth{{ auth()->user()->nama ?? 'Kamu' }}@else Donatur @endauth</span></div>
          </div>
        </div>

        <button onclick="submitDonasi()" class="btn-primary" style="width:100%;justify-content:center;padding:14px">
          Kirim Donasi →
        </button>
      </div>
      <div id="donasi-result"></div>
    </div>
  </div>
</div>
</main>

<!-- Confirmation Modal -->
<div id="confirm-overlay" onclick="if(event.target===this)closeConfirm()"
  style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:100%;max-width:420px;box-shadow:var(--shadow-lg)">
    <div style="font-family:'DM Serif Display',serif;font-size:1.25rem;color:var(--forest);margin-bottom:1.25rem">Konfirmasi Donasi</div>
    <div id="confirm-summary" style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;margin-bottom:1rem;font-size:13px"></div>
    <p style="font-size:12px;color:var(--ink-soft);line-height:1.7;margin-bottom:1.25rem">
      <i class="fa fa-circle-info" style="margin-right:5px;color:var(--forest-light)"></i>Setelah konfirmasi, kamu akan diarahkan ke halaman pembayaran untuk menyelesaikan transaksi.
    </p>
    <div style="display:flex;gap:8px">
      <button onclick="doConfirmDonasi()" class="btn-primary" id="confirm-btn" style="flex:1;justify-content:center">Lanjut Bayar →</button>
      <button onclick="closeConfirm()" class="btn-secondary">Batal</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const _isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
const _userName   = '{{ auth()->check() ? addslashes(auth()->user()->nama ?? 'Kamu') : 'Donatur' }}';
let _pendingDonasi = null;

function fmtRp(n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function setChip(el) {
  document.querySelectorAll('[data-amt]').forEach(b => { b.style.borderColor='var(--border)';b.style.background='#fff';b.style.color='var(--ink-mid)'; });
  if (el) { el.style.borderColor='var(--forest)';el.style.background='var(--forest)';el.style.color='#fff'; }
}

function pickAmt(el, amt) {
  setChip(el);
  const input = document.getElementById('amt-input');
  input.value = fmtRp(amt);
  input.style.borderColor = 'var(--forest)';
}

function onAmtInput(input) {
  const raw = input.value.replace(/\D/g, '');
  input.value = raw ? fmtRp(parseInt(raw)) : '';
  input.style.borderColor = raw ? 'var(--forest)' : 'var(--border)';
  setChip(null);
}

document.querySelector('[data-amt="20000"]').style.cssText += ';border-color:var(--forest);background:var(--forest);color:#fff';

let anonymChecked = false;
function toggleAnonym() {
  anonymChecked = !anonymChecked;
  const toggle  = document.getElementById('anonym-toggle');
  const check   = document.getElementById('anonym-check');
  const icon    = document.getElementById('anonym-icon');
  const preview = document.getElementById('donor-name-preview');
  toggle.dataset.checked  = anonymChecked ? '1' : '0';
  check.style.background  = anonymChecked ? 'var(--forest)' : '#fff';
  check.style.borderColor = anonymChecked ? 'var(--forest)' : 'var(--border)';
  icon.style.color        = '#fff';
  icon.style.display      = anonymChecked ? 'inline' : 'none';
  toggle.style.borderColor = anonymChecked ? 'var(--forest)' : 'var(--border)';
  toggle.style.background  = anonymChecked ? '#F0F7F3' : '#fff';
  if (preview) preview.textContent = anonymChecked ? 'Donatur Anonim' : _userName;
}

let kampanyeMap = {};

function onKampanyeChange(id) {
  renderKampanyeInfo(id && id !== '0' ? kampanyeMap[id] : null);
}

function renderKampanyeInfo(k) {
  const box = document.getElementById('kampanye-info');
  if (!k) { box.style.display = 'none'; return; }

  const pct = k.target > 0 ? Math.min(100, Math.round(k.terkumpul / k.target * 100)) : 0;
  const statusMeta = {
    aktif:  { label: 'Aktif',   bg: 'var(--forest-pale)', color: 'var(--forest)',    bar: 'pf-green' },
    urgent: { label: 'Urgent',  bg: '#FDECEA',            color: 'var(--rust)',       bar: 'pf-rust'  },
    selesai:{ label: 'Selesai', bg: '#EFEFED',            color: 'var(--ink-soft)',   bar: 'pf-green' },
  };
  const sm = statusMeta[k.status] || { label: k.status, bg: '#EFEFED', color: 'var(--ink-soft)', bar: 'pf-green' };

  const deadline = k.deadline
    ? new Date(k.deadline).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
    : null;

  const desc = k.deskripsi
    ? `<div style="font-size:12px;color:var(--ink-soft);line-height:1.65;margin-bottom:10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">${k.deskripsi}</div>`
    : '';

  box.innerHTML = `
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px">
      <div style="font-size:13px;font-weight:600;color:var(--ink);line-height:1.4">${k.judul}</div>
      <span style="font-size:10px;padding:2px 9px;border-radius:99px;background:${sm.bg};color:${sm.color};white-space:nowrap;flex-shrink:0;font-weight:600">${sm.label}</span>
    </div>
    ${desc}
    <div class="progress-track" style="margin:0 0 6px">
      <div class="${sm.bar} progress-fill" style="width:${pct}%"></div>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
      <div>
        <span style="font-size:13px;font-weight:700;color:var(--forest)">Rp ${parseInt(k.terkumpul).toLocaleString('id-ID')}</span>
        <span style="font-size:11px;color:var(--ink-soft)"> / Rp ${parseInt(k.target).toLocaleString('id-ID')}</span>
      </div>
      <span style="font-size:12px;font-weight:700;color:${sm.color}">${pct}%</span>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:4px">
      ${deadline ? `<span style="font-size:11px;color:var(--ink-mute)"><i class="fa fa-calendar-days" style="margin-right:3px"></i>Batas: ${deadline}</span>` : '<span></span>'}
      <a href="/kampanye/${k.id}" style="font-size:11px;color:var(--forest-light);font-weight:500;display:inline-flex;align-items:center;gap:3px">Lihat detail <i class="fa fa-arrow-right" style="font-size:9px"></i></a>
    </div>`;
  box.style.display = 'block';
}

async function loadKampanye() {
  const res = await fetch('/api/kampanye');
  const data = await res.json();
  if (!data.success) return;
  const sel = document.getElementById('kampanye-select');
  const list = data.data.filter(k => k.status !== 'selesai' && k.status !== 'arsip');
  kampanyeMap = Object.fromEntries(list.map(k => [k.id, k]));
  sel.innerHTML =
    '<option value="">-- Pilih Kampanye --</option>' +
    '<option value="0">🏠 Donasi Umum (Kas RT)</option>' +
    list.map(k => `<option value="${k.id}">${k.judul}</option>`).join('');
  const preselect = new URLSearchParams(window.location.search).get('kampanye');
  if (preselect && kampanyeMap[preselect]) {
    sel.value = preselect;
    renderKampanyeInfo(kampanyeMap[preselect]);
  }
}

function submitDonasi() {
  const sel = document.getElementById('kampanye-select');
  const kampanyeId = sel.value;
  const kampanyeName = sel.options[sel.selectedIndex]?.text || '';
  const isAnonym = document.getElementById('anonym-toggle').dataset.checked === '1';
  const nominal = parseInt(document.getElementById('amt-input').value.replace(/\D/g, '') || '0');

  if (kampanyeId === '') { alert('Pilih kampanye atau pilih Donasi Umum.'); return; }
  if (!nominal || nominal < 1000) { alert('Masukkan nominal minimal Rp 1.000.'); return; }

  if (!_isLoggedIn) {
    document.getElementById('donasi-result').innerHTML = `
      <div style="background:var(--gold-pale);border:1px solid rgba(200,168,75,0.3);border-radius:var(--radius-sm);padding:1rem 1.25rem;margin-top:1rem;font-size:13px;color:#7A5C00">
        <i class="fa fa-circle-info" style="margin-right:6px"></i>
        <a href="/login" style="font-weight:600;color:var(--forest)">Masuk</a> terlebih dahulu untuk menyimpan dan melacak donasi kamu.
      </div>`;
    return;
  }

  const row = (label, value, last = false) =>
    `<div style="display:flex;justify-content:space-between;align-items:flex-start;padding:9px 14px;${last ? '' : 'border-bottom:1px solid var(--border)'}">
      <span style="color:var(--ink-soft);flex-shrink:0;margin-right:12px">${label}</span>
      <span style="font-weight:600;text-align:right">${value}</span>
    </div>`;
  document.getElementById('confirm-summary').innerHTML =
    row('Kampanye', kampanyeName) +
    row('Nominal', `<span style="color:var(--forest);font-size:1.05rem">Rp ${nominal.toLocaleString('id-ID')}</span>`) +
    row('Tampil sebagai', isAnonym ? 'Donatur Anonim' : _userName, true);

  _pendingDonasi = { kampanyeId, nominal, isAnonym };
  document.getElementById('confirm-overlay').style.display = 'flex';
}

function closeConfirm() {
  document.getElementById('confirm-overlay').style.display = 'none';
}

async function doConfirmDonasi() {
  if (!_pendingDonasi) return;
  const btn = document.getElementById('confirm-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin" style="margin-right:6px"></i>Memproses...';
  try {
    const res = await fetch('/api/donasi', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken },
      body: JSON.stringify({
        kampanye_id: _pendingDonasi.kampanyeId === '0' ? null : parseInt(_pendingDonasi.kampanyeId),
        nominal: _pendingDonasi.nominal,
        is_anonym: _pendingDonasi.isAnonym ? 1 : 0
      })
    });
    const data = await res.json();
    if (data.success) {
      location.href = '/dashboard/bayar?donasi=' + data.data.id;
    } else {
      alert(data.message || 'Gagal menyimpan donasi.');
      btn.disabled = false;
      btn.innerHTML = 'Lanjut Bayar →';
    }
  } catch(e) {
    alert('Koneksi gagal: ' + e.message);
    btn.disabled = false;
    btn.innerHTML = 'Lanjut Bayar →';
  }
}

loadKampanye();
</script>
@endsection
