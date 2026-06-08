@extends('layouts.admin')
@section('title', 'Data Warga')
@section('content')
<div class="container">

  {{-- Header --}}
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Data Warga</div>
      <div style="font-size:13px;color:var(--ink-soft);margin-top:4px">Daftar seluruh akun yang terdaftar di sistem RT 10</div>
    </div>
    <a href="/admin/aktivitas" class="btn-secondary" style="font-size:13px;padding:8px 14px"><i class="fa-solid fa-magnifying-glass" style="margin-right:6px"></i>Log Aktivitas</a>
  </div>

  {{-- Stats --}}
  <div id="stats-bar" style="display:flex;gap:12px;margin-bottom:1rem;flex-wrap:wrap"></div>

  {{-- Search + Filter --}}
  <div style="display:flex;gap:8px;margin-bottom:1rem;flex-wrap:wrap">
    <input id="search-input" type="text" placeholder="Cari nama, email, blok..." autocomplete="off"
      style="flex:1;min-width:180px;padding:9px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
    <select id="filter-role"
      style="padding:9px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none;background:#fff">
      <option value="">Semua Role</option>
      <option value="warga">Warga</option>
      <option value="sekretaris">Sekretaris</option>
      <option value="bendahara">Bendahara</option>
      <option value="admin">Admin</option>
      <option value="super_admin">Super Admin</option>
    </select>
    <select id="filter-profil"
      style="padding:9px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none;background:#fff">
      <option value="">Semua Status</option>
      <option value="1">Profil Lengkap</option>
      <option value="0">Belum Lengkap</option>
    </select>
  </div>

  {{-- Table --}}
  <div id="warga-list"><div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat...</div></div>

</div>

{{-- Edit User Modal --}}
<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:520px;max-width:95vw;max-height:90vh;overflow-y:auto;box-shadow:0 8px 32px rgba(0,0,0,.18)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
      <div>
        <div style="font-size:16px;font-weight:600">Edit Data Warga</div>
        <div id="edit-modal-email" style="font-size:12px;color:var(--ink-soft);margin-top:2px"></div>
      </div>
      <button onclick="closeEditModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--ink-soft);line-height:1">×</button>
    </div>

    <div style="margin-bottom:1rem">
      <label class="flabel">Nama Lengkap</label>
      <input id="edit-nama" type="text" placeholder="Nama warga"
        style="width:100%;box-sizing:border-box;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
    </div>

    <div style="margin-bottom:1.25rem">
      <label class="flabel">Nomor WhatsApp</label>
      <input id="edit-wa" type="text" placeholder="08xxxxxxxxxx"
        style="width:100%;box-sizing:border-box;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
    </div>

    <div style="margin-bottom:1.25rem">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
        <label class="flabel" style="margin-bottom:0">Unit Rumah</label>
        <button onclick="addUnitRow()" type="button"
          style="font-size:11px;padding:4px 10px;border:1px solid var(--forest);border-radius:6px;background:#E8F4ED;color:var(--forest);cursor:pointer;font-weight:600">+ Tambah Unit</button>
      </div>
      <div id="edit-units" style="display:flex;flex-direction:column;gap:6px"></div>
      <div style="font-size:11px;color:var(--ink-soft);margin-top:6px;padding:8px 10px;background:#F4F4F4;border-radius:6px">
        <i class="fa-solid fa-lightbulb" style="color:var(--gold-dark);margin-right:5px"></i>Anggota keluarga berbeda akun boleh menggunakan unit rumah yang sama.
      </div>
    </div>

    <div style="display:flex;gap:8px;justify-content:flex-end">
      <button onclick="closeEditModal()" class="btn-secondary" style="padding:9px 18px;font-size:13px">Batal</button>
      <button onclick="saveEdit()" class="btn-primary" style="padding:9px 18px;font-size:13px">Simpan Perubahan</button>
    </div>
  </div>
</div>

{{-- Role Change Modal --}}
<div id="role-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:var(--radius);padding:2rem;width:420px;max-width:92vw;box-shadow:0 8px 32px rgba(0,0,0,.18)">
    <div style="font-size:16px;font-weight:600;margin-bottom:0.25rem">Ubah Role Pengguna</div>
    <div id="role-modal-name" style="font-size:13px;color:var(--ink-soft);margin-bottom:1.25rem"></div>

    <div style="margin-bottom:1.25rem">
      <label class="flabel">Role Baru</label>
      <select id="role-select" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
        {{-- Options populated dynamically by openRoleModal() based on actor's role --}}
      </select>
    </div>

    <div style="background:#FFF8E0;border:1px solid #F0D060;border-radius:var(--radius-sm);padding:10px 14px;font-size:12px;color:#7A5C00;margin-bottom:1.25rem">
      <i class="fa-solid fa-triangle-exclamation" style="margin-right:5px"></i>Hanya Super Admin yang dapat mengubah role. Hati-hati saat memberi role Super Admin.
    </div>

    <div style="display:flex;gap:8px;justify-content:flex-end">
      <button onclick="closeRoleModal()" class="btn-secondary" style="padding:9px 18px;font-size:13px">Batal</button>
      <button onclick="saveRole()" class="btn-primary" style="padding:9px 18px;font-size:13px">Simpan Perubahan</button>
    </div>
  </div>
</div>

<style>
.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}
.unit-row{display:flex;gap:6px;align-items:center}
.unit-row select,.unit-row input[type=number]{padding:8px 10px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none}
.shared-badge{display:inline-flex;align-items:center;gap:3px;font-size:10px;padding:2px 7px;border-radius:99px;background:#E8F0FD;color:#2D5AA8;font-weight:600;margin-left:4px}
</style>

@endsection
@section('scripts')
<script>
const _csrfToken = '{{ csrf_token() }}';
let allUsers      = [];
let unitUserMap   = {};
let currentUserRole = null; // exact role of logged-in admin
let currentEditUser = null;
let currentRoleUser = null;

const ROLE_ORDER = ['warga', 'sekretaris', 'bendahara', 'admin', 'super_admin'];

const ROLE_LABELS = {
  super_admin: 'Super Admin — akses penuh termasuk manajemen role',
  admin:       'Admin — akses penuh, dapat kelola role di bawahnya',
  bendahara:   'Bendahara — akses penuh modul keuangan',
  sekretaris:  'Sekretaris — kelola data warga & pengumuman',
  warga:       'Warga — akses normal, tidak bisa ke panel admin',
};

const roleStyle = {
  super_admin: ['#EEF4FF','#3B5BDB','Super Admin'],
  admin:       ['#FDF6E4','#7A5C00','Admin'],
  bendahara:   ['#E8F4ED','var(--forest)','Bendahara'],
  sekretaris:  ['#F3EEFF','#5C3B8A','Sekretaris'],
  warga:       ['#F4F4F4','#666','Warga'],
};

// Can the current actor manage (edit/change role of) the target role?
function canManage(targetRole) {
  if (!currentUserRole) return false;
  if (currentUserRole === 'super_admin') return true;
  const actorIdx  = ROLE_ORDER.indexOf(currentUserRole);
  const targetIdx = ROLE_ORDER.indexOf(targetRole);
  return targetIdx < actorIdx;
}

// Roles the current actor can assign
function assignableRoles() {
  if (!currentUserRole) return [];
  if (currentUserRole === 'super_admin') return ROLE_ORDER;
  const actorIdx = ROLE_ORDER.indexOf(currentUserRole);
  return ROLE_ORDER.slice(0, actorIdx); // strictly below
}

// Can the current actor change roles at all?
function canChangeRole() {
  return ['admin', 'super_admin'].includes(currentUserRole);
}

// Can the current actor edit user data?
function canEditData() {
  return ['sekretaris', 'admin', 'super_admin'].includes(currentUserRole);
}

function roleBadge(role) {
  const [bg, col, lbl] = roleStyle[role] || roleStyle.warga;
  return `<span style="font-size:11px;padding:3px 10px;border-radius:99px;background:${bg};color:${col};font-weight:600">${lbl}</span>`;
}

// ── Load ─────────────────────────────────────────────────────────
async function loadWarga() {
  let wData, meData;
  try {
    const headers = {'Accept':'application/json'};
    const [wRes, meRes] = await Promise.all([
      fetch('/api/users',    {headers}),
      fetch('/api/auth/me',  {headers}),
    ]);
    wData  = await wRes.json();
    meData = await meRes.json();
  } catch (err) {
    document.getElementById('warga-list').innerHTML =
      `<div style="text-align:center;padding:3rem;color:#B5451B">Gagal memuat data: ${err.message}</div>`;
    return;
  }

  if (meData.success) {
    currentUserRole = meData.data?.role ?? null;
  }

  if (!wData.success) {
    document.getElementById('warga-list').innerHTML =
      '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Gagal memuat data.</div>';
    return;
  }

  allUsers = wData.data || [];

  // Build unit→users map for shared detection
  unitUserMap = {};
  allUsers.forEach(u => {
    (u.units || []).forEach(uu => {
      const key = `${uu.blok}|${uu.nomor}`;
      if (!unitUserMap[key]) unitUserMap[key] = [];
      unitUserMap[key].push(u.id);
    });
  });

  renderStats();
  renderTable();
  bindFilters();
}

// ── Stats ─────────────────────────────────────────────────────────
function renderStats() {
  const total      = allUsers.length;
  const pengurus   = allUsers.filter(u => ['admin','super_admin','bendahara','sekretaris'].includes(u.role)).length;
  const wargaCount = allUsers.filter(u => u.role === 'warga').length;
  const incomplete = allUsers.filter(u => !u.profil_lengkap).length;
  const linkedKK   = allUsers.filter(u => u.kepala_keluarga).length;

  const sharedUnits = Object.values(unitUserMap).filter(ids => ids.length > 1).length;

  const stats = [
    {label:'Total Akun',  value:total,      bg:'#E8F4ED', col:'var(--forest)'},
    {label:'Pengurus',    value:pengurus,    bg:'#FDF6E4', col:'#7A5C00'},
    {label:'Warga',       value:wargaCount,  bg:'#F4F4F4', col:'#555'},
    {label:'Belum Lengkap', value:incomplete, bg:'#FDECEA', col:'#B5451B'},
    {label:'Terhubung KK',  value:linkedKK,   bg:'#F3EEFF', col:'#5C3B8A'},
  ];
  if (sharedUnits > 0) {
    stats.push({label:'Unit Bersama', value:sharedUnits, bg:'#E8F0FD', col:'#2D5AA8'});
  }

  document.getElementById('stats-bar').innerHTML = stats.map(s =>
    `<div style="background:${s.bg};border-radius:var(--radius-sm);padding:12px 20px">
      <div style="font-size:11px;color:var(--ink-soft)">${s.label}</div>
      <div style="font-size:1.4rem;font-weight:600;color:${s.col}">${s.value}</div>
    </div>`
  ).join('');
}

// ── Filters ───────────────────────────────────────────────────────
function bindFilters() {
  document.getElementById('search-input').addEventListener('input', renderTable);
  document.getElementById('filter-role').addEventListener('change', renderTable);
  document.getElementById('filter-profil').addEventListener('change', renderTable);
}

function filtered() {
  const q      = document.getElementById('search-input').value.toLowerCase().trim();
  const role   = document.getElementById('filter-role').value;
  const profil = document.getElementById('filter-profil').value;

  return allUsers.filter(u => {
    if (role   && u.role !== role) return false;
    if (profil !== '' && String(u.profil_lengkap ? 1 : 0) !== profil) return false;
    if (q) {
      const hay = [
        u.nama || '', u.email || '', u.no_wa || '',
        ...(u.units || []).map(uu => `blok ${uu.blok} ${uu.nomor}`)
      ].join(' ').toLowerCase();
      if (!hay.includes(q)) return false;
    }
    return true;
  });
}

// ── Render Table ──────────────────────────────────────────────────
function renderTable() {
  const users = filtered();

  if (!users.length) {
    const hasFilter = document.getElementById('search-input').value ||
                      document.getElementById('filter-role').value ||
                      document.getElementById('filter-profil').value;
    document.getElementById('warga-list').innerHTML =
      `<div style="text-align:center;padding:3rem;color:var(--ink-soft)">${hasFilter ? 'Tidak ada data yang cocok.' : 'Belum ada warga terdaftar.'}</div>`;
    return;
  }

  const avatarColors = [
    {bg:'#E8F4ED',color:'#1C3D2E'},{bg:'#FDF6E4',color:'#7A5C00'},
    {bg:'#E8F0FD',color:'#2D5AA8'},{bg:'#FDECEA',color:'#B5451B'},
  ];

  const rows = users.map((u, i) => {
    const av       = avatarColors[i % avatarColors.length];
    const initials = (u.nama || u.email || '?').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    const tgl      = new Date(u.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});

    // Units with shared indicator
    const kk = u.kepala_keluarga;
    const kkHtml = kk
      ? `<a href="/admin/kependudukan/warga/${kk.id}" style="font-size:12px;font-weight:500;color:var(--forest);white-space:nowrap">${kk.nama}</a>
         ${kk.unit_rumah ? `<div style="font-size:11px;color:var(--ink-mute)">Blok ${kk.unit_rumah.blok}-${kk.unit_rumah.nomor}</div>` : ''}`
      : `<span style="font-size:12px;color:var(--ink-mute)">—</span>`;

    const unitHtml = (u.units && u.units.length)
      ? u.units.map(uu => {
          const key    = `${uu.blok}|${uu.nomor}`;
          const shared = unitUserMap[key] && unitUserMap[key].length > 1;
          const shareCount = shared ? unitUserMap[key].length - 1 : 0;
          const sharedTip = shared ? `title="${shareCount} akun lain di unit ini"` : '';
          return `<span style="white-space:nowrap">Blok ${uu.blok}/${uu.nomor}${uu.is_primary ? '' : ' <span style="font-size:10px;color:var(--ink-soft)">(tambahan)</span>'}${shared ? `<span class="shared-badge" ${sharedTip}><i class="fa-solid fa-users" style="font-size:9px"></i> +${shareCount}</span>` : ''}</span>`;
        }).join('<br>')
      : '<span style="color:var(--ink-soft);font-size:12px">—</span>';

    const profilBadge = u.profil_lengkap
      ? `<span style="font-size:10px;padding:2px 7px;border-radius:99px;background:#E8F4ED;color:var(--forest);font-weight:600"><i class="fa-solid fa-check" style="font-size:9px;margin-right:3px"></i>Lengkap</span>`
      : `<span style="font-size:10px;padding:2px 7px;border-radius:99px;background:#FDECEA;color:#B5451B;font-weight:600">Belum</span>`;

    const editBtn = (canEditData() && canManage(u.role))
      ? `<button onclick='openEditModal(${JSON.stringify({id:u.id, nama:u.nama||'', email:u.email, no_wa:u.no_wa||'', units:u.units||[]})})' title="Edit data warga"
           style="padding:4px 10px;font-size:11px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;margin-right:4px"><i class="fa-solid fa-pen" style="font-size:10px;margin-right:3px"></i>Edit</button>`
      : '';

    const roleBtn = (canChangeRole() && canManage(u.role))
      ? `<button onclick='openRoleModal(${JSON.stringify({id:u.id, nama:u.nama||u.email, email:u.email, role:u.role})})' title="Ubah role"
           style="padding:4px 10px;font-size:11px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer"><i class="fa-solid fa-user-shield" style="font-size:10px;margin-right:3px"></i>Role</button>`
      : '';

    return `<tr style="border-top:1px solid var(--border)">
      <td style="padding:12px 16px">
        <div style="display:flex;align-items:center;gap:10px">
          <div style="width:34px;height:34px;border-radius:50%;background:${av.bg};color:${av.color};font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">${initials}</div>
          <div>
            <div style="font-size:13px;font-weight:500">${u.nama || '<span style="color:var(--ink-soft);font-style:italic">Belum isi nama</span>'}</div>
            <div style="font-size:11px;color:var(--ink-soft)">${u.email || ''}</div>
          </div>
        </div>
      </td>
      <td style="padding:12px 16px;font-size:12px;line-height:1.8">${unitHtml}</td>
      <td style="padding:12px 16px">${kkHtml}</td>
      <td style="padding:12px 16px;font-size:12px;color:var(--ink-soft)">${u.no_wa || '—'}</td>
      <td style="padding:12px 16px">${profilBadge}</td>
      <td style="padding:12px 16px">${roleBadge(u.role)}</td>
      <td style="padding:12px 16px;font-size:11px;color:var(--ink-soft);white-space:nowrap">${tgl}</td>
      <td style="padding:12px 16px;white-space:nowrap">${editBtn}${roleBtn}</td>
    </tr>`;
  }).join('');

  const headers = ['Nama / Email','Unit Rumah','KK Terhubung','No. WA','Profil','Role','Bergabung','Aksi'];

  document.getElementById('warga-list').innerHTML = `
    <div class="card" style="padding:0;overflow:hidden">
      <div style="padding:10px 16px;background:var(--cream);border-bottom:1px solid var(--border);font-size:12px;color:var(--ink-soft)">
        Menampilkan <strong>${users.length}</strong> dari <strong>${allUsers.length}</strong> akun
      </div>
      <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;min-width:700px">
          <thead>
            <tr style="background:var(--cream)">
              ${headers.map(h => `<th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase;white-space:nowrap">${h}</th>`).join('')}
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </div>
    </div>`;
}

// ── Edit Modal ────────────────────────────────────────────────────
function openEditModal(user) {
  currentEditUser = user;
  document.getElementById('edit-modal-email').textContent = user.email;
  document.getElementById('edit-nama').value = user.nama || '';
  document.getElementById('edit-wa').value   = user.no_wa || '';

  const container = document.getElementById('edit-units');
  container.innerHTML = '';

  const units = user.units && user.units.length ? user.units : [{blok:'A', nomor:''}];
  units.forEach(u => addUnitRow(u.blok, u.nomor));

  document.getElementById('edit-modal').style.display = 'flex';
  document.getElementById('edit-nama').focus();
}

function closeEditModal() {
  document.getElementById('edit-modal').style.display = 'none';
  currentEditUser = null;
}

const BLOKS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');

function addUnitRow(blok = '', nomor = '') {
  const container = document.getElementById('edit-units');
  const row       = document.createElement('div');
  row.className   = 'unit-row';

  const blokSel = document.createElement('select');
  blokSel.style.cssText = 'padding:8px 10px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none;flex:0 0 90px';
  BLOKS.forEach(b => {
    const opt = document.createElement('option');
    opt.value = b; opt.textContent = `Blok ${b}`;
    if (b === (blok || '').toUpperCase()) opt.selected = true;
    blokSel.appendChild(opt);
  });

  const nomorInput = document.createElement('input');
  nomorInput.type        = 'number';
  nomorInput.min         = '1';
  nomorInput.max         = '99';
  nomorInput.placeholder = 'No.';
  nomorInput.value       = nomor || '';
  nomorInput.style.cssText = 'flex:1;padding:8px 10px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none;width:70px';

  const removeBtn = document.createElement('button');
  removeBtn.type      = 'button';
  removeBtn.textContent = '×';
  removeBtn.style.cssText = 'padding:6px 10px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;font-size:14px;color:var(--ink-soft)';
  removeBtn.onclick = () => {
    if (container.children.length > 1) container.removeChild(row);
  };

  row.appendChild(blokSel);
  row.appendChild(nomorInput);
  row.appendChild(removeBtn);
  container.appendChild(row);
}

async function saveEdit() {
  if (!currentEditUser) return;

  const nama  = document.getElementById('edit-nama').value.trim();
  const noWa  = document.getElementById('edit-wa').value.trim();
  const rows  = document.getElementById('edit-units').querySelectorAll('.unit-row');

  const units = [];
  let valid = true;
  rows.forEach(row => {
    const blok  = row.querySelector('select').value;
    const nomor = parseInt(row.querySelector('input').value, 10);
    if (!blok || !nomor || nomor < 1 || nomor > 99) { valid = false; return; }
    units.push({blok, nomor});
  });

  if (nama.length < 2)  { alert('Nama minimal 2 karakter.'); return; }
  if (!valid || !units.length) { alert('Isi unit rumah dengan benar (Blok A-Z, No. 1-99).'); return; }

  const r = await fetch(`/api/admin/users/${currentEditUser.id}`, {
    method: 'PUT',
    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},
    body: JSON.stringify({nama, no_wa: noWa || null, units}),
  });
  const j = await r.json();

  if (j.success) {
    closeEditModal();
    showToast(j.message || 'Data berhasil diperbarui.');
    loadWarga();
  } else {
    const errMsg = j.errors ? Object.values(j.errors).join('\n') : (j.message || 'Terjadi kesalahan.');
    alert(errMsg);
  }
}

// ── Role Modal ────────────────────────────────────────────────────
function openRoleModal(user) {
  currentRoleUser = user;
  document.getElementById('role-modal-name').textContent = `${user.nama} — ${user.email}`;

  // Populate only roles the current actor is allowed to assign
  const select  = document.getElementById('role-select');
  const allowed = assignableRoles();
  select.innerHTML = '';
  allowed.forEach(r => {
    const opt = document.createElement('option');
    opt.value = r;
    opt.textContent = ROLE_LABELS[r] || r;
    if (r === user.role) opt.selected = true;
    select.appendChild(opt);
  });

  document.getElementById('role-modal').style.display = 'flex';
}

function closeRoleModal() {
  document.getElementById('role-modal').style.display = 'none';
  currentRoleUser = null;
}

async function saveRole() {
  if (!currentRoleUser) return;
  const newRole = document.getElementById('role-select').value;

  const r = await fetch(`/api/admin/users/${currentRoleUser.id}/role`, {
    method: 'PUT',
    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken},
    body: JSON.stringify({role: newRole}),
  });
  const j = await r.json();

  if (j.success) {
    closeRoleModal();
    showToast(j.message);
    loadWarga();
  } else {
    alert(j.message);
  }
}

// Close modals on backdrop click
document.getElementById('edit-modal').addEventListener('click', e => { if (e.target === e.currentTarget) closeEditModal(); });
document.getElementById('role-modal').addEventListener('click', e => { if (e.target === e.currentTarget) closeRoleModal(); });

loadWarga();
</script>
@endsection
