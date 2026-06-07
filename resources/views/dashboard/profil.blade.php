@extends('layouts.app')
@section('title', 'Edit Profil')
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container" style="max-width:600px">

  <div style="margin-bottom:2rem">
    <a href="/dashboard" style="font-size:13px;color:var(--ink-soft)">← Dashboard</a>
    <div style="font-family:'DM Serif Display',serif;font-size:1.8rem;color:var(--forest);margin-top:6px">Edit Profil</div>
  </div>

  <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;padding:1.25rem;background:var(--cream);border-radius:var(--radius)">
    <div id="profile-avatar" style="width:56px;height:56px;border-radius:50%;background:var(--forest);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:600;color:var(--gold);flex-shrink:0">?</div>
    <div>
      <div style="font-size:15px;font-weight:500;color:var(--ink)" id="profile-nama">Memuat...</div>
      <div style="font-size:13px;color:var(--ink-soft)" id="profile-email"></div>
      <div style="font-size:12px;color:var(--forest-light);margin-top:2px" id="profile-units"></div>
    </div>
  </div>

  <div class="card">
    <div style="font-size:15px;font-weight:500;margin-bottom:1.5rem">Informasi pribadi</div>

    <div style="margin-bottom:1.25rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Nama lengkap</label>
      <input type="text" id="nama" placeholder="Nama sesuai KTP" maxlength="100"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;transition:border-color 0.15s"
        onfocus="this.style.borderColor='var(--forest-light)'" onblur="this.style.borderColor='var(--border)'">
      <div style="font-size:11px;color:var(--rust);margin-top:4px;display:none" id="err-nama"></div>
    </div>

    <div style="margin-bottom:1.5rem">
      <label style="display:block;font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">
        No. WhatsApp <span style="font-weight:400;color:var(--sand)">(opsional)</span>
      </label>
      <input type="tel" id="no_wa" placeholder="08xx-xxxx-xxxx" maxlength="20"
        style="width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;outline:none;transition:border-color 0.15s"
        onfocus="this.style.borderColor='var(--forest-light)'" onblur="this.style.borderColor='var(--border)'">
    </div>

    <div style="border-top:1px solid var(--border);padding-top:1.5rem;margin-bottom:1.5rem">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
        <label style="font-size:12px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;letter-spacing:0.05em">Unit Rumah</label>
        <div style="font-size:12px;color:var(--ink-soft)">Format: Blok (A-Z) + Nomor (1-99)</div>
      </div>
      <div id="unit-list" style="display:flex;flex-direction:column;gap:8px;margin-bottom:10px"></div>
      <button type="button" onclick="addUnit()" id="add-unit-btn"
        style="width:100%;padding:9px;border:1.5px dashed var(--sand);border-radius:var(--radius-sm);background:transparent;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:500;color:var(--forest-light);cursor:pointer;transition:all 0.15s"
        onmouseenter="this.style.borderColor='var(--forest-light)';this.style.background='#F7FBF8'"
        onmouseleave="this.style.borderColor='var(--sand)';this.style.background='transparent'">
        + Tambah unit lain
      </button>
      <div style="font-size:11px;color:var(--rust);margin-top:4px;display:none" id="err-units"></div>
    </div>

    <div id="api-error" style="background:#FDECEA;color:var(--rust);font-size:13px;padding:10px 14px;border-radius:var(--radius-sm);margin-bottom:1rem;display:none"></div>

    <button onclick="saveProfile()" class="btn-primary" style="width:100%;justify-content:center" id="save-btn">
      Simpan Perubahan
    </button>
  </div>

</div>
</main>
@endsection
@section('scripts')
<script>
let unitCount = 0;

async function loadProfile() {
  const res = await fetch('/api/auth/me');
  const data = await res.json();
  if (!data.success) return;
  const u = data.data;

  const initials = (u.nama || u.email || '?').split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();
  const av = document.getElementById('profile-avatar');
  if (u.avatar_url) {
    av.innerHTML = `<img src="${u.avatar_url}" style="width:56px;height:56px;border-radius:50%;object-fit:cover">`;
  } else {
    av.textContent = initials;
  }
  document.getElementById('profile-nama').textContent = u.nama || 'Belum diisi';
  document.getElementById('profile-email').textContent = u.email || '';
  document.getElementById('profile-units').textContent = (u.units||[]).map(uu=>`Blok ${uu.blok}/${uu.nomor}`).join(' · ') || 'Belum ada unit';

  document.getElementById('nama').value = u.nama || '';
  document.getElementById('no_wa').value = u.no_wa || '';

  document.getElementById('unit-list').innerHTML = '';
  unitCount = 0;
  if (u.units && u.units.length) {
    u.units.forEach(uu => addUnit(uu.blok, uu.nomor));
  } else {
    addUnit();
  }
}

function addUnit(blok = '', nomor = '') {
  if (unitCount >= 5) return;
  unitCount++;
  const idx = unitCount;
  const div = document.createElement('div');
  div.id = 'unit-row-' + idx;
  div.style.cssText = 'display:flex;gap:8px;align-items:center';
  div.innerHTML = `
    <div style="width:80px">
      <div style="font-size:10px;color:var(--ink-soft);margin-bottom:4px;text-align:center">Blok</div>
      <input type="text" id="blok-${idx}" maxlength="1" value="${blok}"
        placeholder="O" oninput="this.value=this.value.toUpperCase().replace(/[^A-Z]/g,'')"
        style="width:100%;padding:10px 8px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:16px;font-weight:600;letter-spacing:0.1em;text-align:center;font-family:'DM Sans',sans-serif;outline:none"
        onfocus="this.style.borderColor='var(--forest-light)'" onblur="this.style.borderColor='var(--border)'">
    </div>
    <div style="flex:1">
      <div style="font-size:10px;color:var(--ink-soft);margin-bottom:4px">Nomor</div>
      <input type="text" id="nomor-${idx}" maxlength="2" value="${nomor}"
        placeholder="16" oninput="this.value=this.value.replace(/[^0-9]/g,'')"
        style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'DM Sans',sans-serif;outline:none"
        onfocus="this.style.borderColor='var(--forest-light)'" onblur="this.style.borderColor='var(--border)'">
    </div>
    ${idx > 1 ? `<button type="button" onclick="removeUnit(${idx})" style="width:32px;height:36px;background:#FDECEA;border:none;border-radius:var(--radius-sm);color:var(--rust);cursor:pointer;font-size:16px;margin-top:18px;flex-shrink:0">×</button>` : '<div style="width:32px;margin-top:18px"></div>'}
  `;
  document.getElementById('unit-list').appendChild(div);
  document.getElementById('add-unit-btn').style.display = unitCount >= 5 ? 'none' : 'block';
}

function removeUnit(idx) {
  document.getElementById('unit-row-' + idx).remove();
  unitCount--;
  document.getElementById('add-unit-btn').style.display = 'block';
}

async function saveProfile() {
  const nama = document.getElementById('nama').value.trim();
  const no_wa = document.getElementById('no_wa').value.trim();

  const units = [];
  document.querySelectorAll('[id^="unit-row-"]').forEach(row => {
    const idx = row.id.replace('unit-row-', '');
    const blok  = (document.getElementById('blok-' + idx)?.value || '').toUpperCase().trim();
    const nomor = parseInt(document.getElementById('nomor-' + idx)?.value || '0');
    if (blok && nomor) units.push({ blok, nomor });
  });

  let hasError = false;
  if (nama.length < 2) {
    document.getElementById('err-nama').textContent = 'Nama minimal 2 karakter.';
    document.getElementById('err-nama').style.display = 'block';
    hasError = true;
  } else {
    document.getElementById('err-nama').style.display = 'none';
  }
  if (!units.length) {
    document.getElementById('err-units').textContent = 'Minimal 1 unit rumah.';
    document.getElementById('err-units').style.display = 'block';
    hasError = true;
  } else {
    document.getElementById('err-units').style.display = 'none';
  }
  if (hasError) return;

  const btn = document.getElementById('save-btn');
  btn.disabled = true;
  btn.textContent = 'Menyimpan...';

  try {
    const res = await fetch('/api/user/profile', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrfToken },
      body: JSON.stringify({ nama, no_wa: no_wa || null, units })
    });
    const data = await res.json();

    if (data.success) {
      showToast('Profil berhasil disimpan!');
      document.getElementById('profile-nama').textContent = nama;
      document.getElementById('profile-units').textContent = units.map(u=>`Blok ${u.blok}/${u.nomor}`).join(' · ');
    } else {
      document.getElementById('api-error').textContent = data.message || 'Terjadi kesalahan.';
      document.getElementById('api-error').style.display = 'block';
    }
  } catch(e) {
    document.getElementById('api-error').textContent = 'Koneksi gagal. Coba lagi.';
    document.getElementById('api-error').style.display = 'block';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Simpan Perubahan';
  }
}

loadProfile();
</script>
@endsection
