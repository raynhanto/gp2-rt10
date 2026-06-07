@php
  if (auth()->user()?->profil_lengkap) { header('Location: /dashboard'); exit; }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lengkapi Profil — GP2 RT10</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#F5F0E8;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
.card{background:#fff;border-radius:20px;padding:2.5rem 2rem;width:100%;max-width:440px;border:1px solid rgba(28,61,46,0.1)}
.step-badge{display:inline-flex;align-items:center;gap:6px;background:#E8F4ED;color:#1C3D2E;font-size:12px;font-weight:600;padding:4px 12px;border-radius:99px;margin-bottom:1.25rem}
h1{font-family:'DM Serif Display',serif;font-size:1.5rem;color:#1C3D2E;margin-bottom:8px}
.sub{font-size:14px;color:#6B6358;margin-bottom:2rem;line-height:1.6}
.field{margin-bottom:1.25rem}
label{display:block;font-size:12px;font-weight:600;color:#6B6358;letter-spacing:0.05em;text-transform:uppercase;margin-bottom:8px}
input[type=text],input[type=tel]{width:100%;padding:11px 14px;border:1.5px solid #D4C5A0;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:14px;color:#1A1714;outline:none;transition:border-color 0.15s;background:#fff}
input:focus{border-color:#4A8A63}
input::placeholder{color:#C4B99A}
.unit-group{display:flex;gap:8px;align-items:center;margin-bottom:8px}
.remove-unit{width:32px;height:32px;border:none;background:#FDECEA;color:#B5451B;border-radius:8px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.add-unit{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#2D5C42;cursor:pointer;background:none;border:1.5px dashed #A8C5B0;border-radius:10px;padding:8px 14px;width:100%;justify-content:center;margin-top:4px;transition:all 0.15s}
.add-unit:hover{background:#E8F4ED}
.hint{font-size:12px;color:#888780;margin-top:6px}
.error-msg{font-size:12px;color:#B5451B;margin-top:4px;display:none}
.submit-btn{width:100%;padding:14px;background:#1C3D2E;color:#fff;border:none;border-radius:100px;font-family:'DM Sans',sans-serif;font-size:15px;font-weight:500;cursor:pointer;margin-top:0.5rem;transition:all 0.2s}
.submit-btn:hover{background:#2D5C42}
.submit-btn:disabled{opacity:0.6;cursor:not-allowed}
.api-error{background:#FDECEA;color:#B5451B;font-size:13px;padding:10px 14px;border-radius:10px;margin-bottom:1rem;display:none}
</style>
</head>
<body>
<div class="card">
  <div class="step-badge"><svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;display:inline;vertical-align:middle;margin-right:4px"><path d="M12 2L2 8v14h7v-7h6v7h7V8L12 2z"/></svg>Satu langkah lagi</div>
  <h1>Lengkapi profil kamu</h1>
  <p class="sub">Data ini digunakan untuk menampilkan nama di papan donatur dan mengirim notifikasi.</p>

  <div class="api-error" id="api-error"></div>

  <div class="field">
    <label>Nama lengkap</label>
    <input type="text" id="nama" placeholder="Nama sesuai KTP" maxlength="100" />
    <div class="error-msg" id="err-nama"></div>
  </div>

  <div class="field">
    <label>No. WhatsApp <span style="font-weight:400;color:#aaa">(opsional)</span></label>
    <input type="tel" id="no_wa" placeholder="08xx-xxxx-xxxx atau +62xxx" maxlength="20"
      oninput="previewPhone(this)" onblur="normalizePhoneInput(this)" />
    <div class="hint" id="phone-preview"></div>
  </div>

  <div class="field">
    <label>Unit rumah</label>
    <div id="unit-list"></div>
    <button class="add-unit" onclick="addUnit()" id="add-unit-btn" type="button">+ Tambah unit lain</button>
    <div class="hint">Blok huruf (A–Z) + nomor (1–99). Contoh: Blok O / No. 16</div>
    <div class="error-msg" id="err-units"></div>
  </div>

  <button class="submit-btn" onclick="submitProfile()" id="submit-btn">Simpan & Lanjutkan →</button>
</div>

<script>
const _csrf = document.querySelector('meta[name="csrf-token"]').content;
let unitCount = 0;

function normalizePhone(raw) {
  let p = raw.replace(/[\s\-\.\(\)]/g, '');
  if (p.startsWith('+62')) p = '0' + p.slice(3);
  else if (/^62\d/.test(p)) p = '0' + p.slice(2);
  return p.replace(/[^0-9]/g, '');
}
function previewPhone(input) {
  const norm = normalizePhone(input.value);
  document.getElementById('phone-preview').textContent = norm && input.value ? '→ ' + norm : '';
}
function normalizePhoneInput(input) {
  const norm = normalizePhone(input.value);
  if (norm) { input.value = norm; document.getElementById('phone-preview').textContent = ''; }
}
function addUnit(blok = '', nomor = '') {
  if (unitCount >= 5) return;
  unitCount++;
  const idx = unitCount;
  const div = document.createElement('div');
  div.className = 'unit-group';
  div.id = 'unit-' + idx;
  div.innerHTML = `
    <div style="width:72px"><input type="text" maxlength="1" placeholder="O" id="blok-${idx}"
      oninput="this.value=this.value.toUpperCase().replace(/[^A-Z]/g,'')" value="${blok}"
      style="padding:11px 8px;text-align:center;width:100%;border:1.5px solid #D4C5A0;border-radius:10px;font-size:16px;font-weight:600;letter-spacing:0.1em;outline:none;font-family:inherit" /></div>
    <div style="flex:1"><input type="text" inputmode="numeric" maxlength="2" placeholder="16" id="nomor-${idx}"
      oninput="this.value=this.value.replace(/[^0-9]/g,'')" value="${nomor}"
      style="width:100%;padding:11px 14px;border:1.5px solid #D4C5A0;border-radius:10px;font-size:14px;outline:none;font-family:inherit" /></div>
    ${idx > 1 ? `<button class="remove-unit" onclick="removeUnit(${idx})" type="button">×</button>` : ''}`;
  document.getElementById('unit-list').appendChild(div);
  document.getElementById('add-unit-btn').style.display = unitCount >= 5 ? 'none' : 'flex';
}
function removeUnit(idx) {
  document.getElementById('unit-' + idx).remove();
  unitCount--;
  document.getElementById('add-unit-btn').style.display = 'flex';
}
async function submitProfile() {
  const nama  = document.getElementById('nama').value.trim();
  const no_wa = normalizePhone(document.getElementById('no_wa').value);
  const units = [];
  document.querySelectorAll('.unit-group').forEach(el => {
    const idx = el.id.replace('unit-', '');
    const blok  = (document.getElementById('blok-' + idx)?.value || '').trim().toUpperCase();
    const nomor = parseInt(document.getElementById('nomor-' + idx)?.value || '0');
    if (blok && nomor) units.push({ blok, nomor });
  });
  let hasError = false;
  if (nama.length < 2) { document.getElementById('err-nama').textContent = 'Nama minimal 2 karakter.'; document.getElementById('err-nama').style.display = 'block'; hasError = true; } else { document.getElementById('err-nama').style.display = 'none'; }
  if (!units.length) { document.getElementById('err-units').textContent = 'Tambahkan minimal 1 unit rumah.'; document.getElementById('err-units').style.display = 'block'; hasError = true; } else { document.getElementById('err-units').style.display = 'none'; }
  if (hasError) return;
  const btn = document.getElementById('submit-btn');
  btn.disabled = true; btn.textContent = 'Menyimpan...';
  try {
    const res = await fetch('/api/user/profile', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': _csrf },
      body: JSON.stringify({ nama, no_wa, units })
    });
    const data = await res.json();
    if (!data.success) {
      document.getElementById('api-error').textContent = data.message || 'Terjadi kesalahan.';
      document.getElementById('api-error').style.display = 'block';
      btn.disabled = false; btn.textContent = 'Simpan & Lanjutkan →';
      return;
    }
    window.location.href = '/dashboard';
  } catch (e) {
    document.getElementById('api-error').textContent = 'Koneksi gagal. Coba lagi.';
    document.getElementById('api-error').style.display = 'block';
    btn.disabled = false; btn.textContent = 'Simpan & Lanjutkan →';
  }
}
addUnit();
</script>
</body>
</html>
