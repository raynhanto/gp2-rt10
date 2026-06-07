@extends('layouts.admin')
@section('title', 'Log Aktivitas Admin')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Log Aktivitas Admin</div>
      <div style="font-size:13px;color:var(--ink-soft);margin-top:4px">Rekam jejak setiap tindakan yang dilakukan pengurus RT</div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card" style="margin-bottom:1.25rem">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
      <div style="flex:2;min-width:180px">
        <label class="flabel">Cari deskripsi / aksi</label>
        <input type="text" id="f-search" placeholder="Cari..." style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
      </div>
      <div style="flex:1;min-width:140px">
        <label class="flabel">Admin</label>
        <select id="f-admin" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
          <option value="">Semua Admin</option>
        </select>
      </div>
      <div style="flex:1;min-width:140px">
        <label class="flabel">Jenis Aksi</label>
        <select id="f-action" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
          <option value="">Semua Aksi</option>
        </select>
      </div>
      <div style="min-width:120px">
        <label class="flabel">Dari</label>
        <input type="date" id="f-dari" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
      </div>
      <div style="min-width:120px">
        <label class="flabel">Sampai</label>
        <input type="date" id="f-sampai" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
      </div>
      <div>
        <button onclick="loadLog(1)" class="btn-primary" style="padding:9px 18px;font-size:13px">Cari</button>
        <button onclick="resetFilter()" class="btn-secondary" style="padding:9px 14px;font-size:13px;margin-left:6px">Reset</button>
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="card" style="padding:0;overflow:hidden">
    <div id="log-container">
      <div style="text-align:center;padding:3rem;color:var(--ink-soft)">Memuat log aktivitas...</div>
    </div>
    <div id="pagination" style="display:flex;justify-content:space-between;align-items:center;padding:12px 20px;border-top:1px solid var(--border);font-size:13px;color:var(--ink-soft)"></div>
  </div>
</div>

<style>
.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}
.action-badge{display:inline-block;font-size:10px;padding:2px 8px;border-radius:99px;font-weight:600}
</style>
@endsection

@section('scripts')
<script>
const actionColors = {
  verify_donasi:  ['#E8F4ED','var(--forest)'],
  reject_donasi:  ['#FDECEA','var(--rust)'],
  verify_iuran:   ['#E8F4ED','var(--forest)'],
  create_kas:     ['#EEF4FF','#3B5BDB'],
  update_kas:     ['#FFF4E5','#964B00'],
  delete_kas:     ['#FDECEA','var(--rust)'],
  create_pengeluaran: ['#EEF4FF','#3B5BDB'],
  update_pengeluaran: ['#FFF4E5','#964B00'],
  delete_pengeluaran: ['#FDECEA','var(--rust)'],
  create_iuran_periode: ['#EEF4FF','#3B5BDB'],
  create_kas_anggaran:  ['#EEF4FF','#3B5BDB'],
  gsheet_sync:    ['#F3EEFF','#5C3B8A'],
  update_role:    ['#FFF4E5','#964B00'],
};

function badgeFor(action) {
  const [bg, col] = actionColors[action] || ['#F0F0F0','#666'];
  return `<span class="action-badge" style="background:${bg};color:${col}">${action}</span>`;
}

function roleColor(role) {
  const m = {super_admin:'#3B5BDB',admin:'#3B5BDB',bendahara:'var(--forest)',sekretaris:'#964B00',warga:'var(--ink-soft)'};
  return m[role] || 'var(--ink-soft)';
}

let currentPage = 1;
let totalPages  = 1;

async function loadLog(page = 1) {
  currentPage = page;
  const params = new URLSearchParams({
    page,
    per_page: 30,
    search:   document.getElementById('f-search').value.trim(),
    user_id:  document.getElementById('f-admin').value,
    action:   document.getElementById('f-action').value,
    dari:     document.getElementById('f-dari').value,
    sampai:   document.getElementById('f-sampai').value,
  });
  // Remove empty params
  for (const [k, v] of [...params.entries()]) if (!v) params.delete(k);

  const r = await fetch('/api/admin/aktivitas?' + params.toString());
  const j = await r.json();
  if (!j.success) return;

  // Populate dropdowns on first load
  if (page === 1 && !document.getElementById('f-admin').dataset.loaded) {
    document.getElementById('f-admin').dataset.loaded = '1';
    j.admins.forEach(a => {
      const opt = document.createElement('option');
      opt.value = a.id;
      opt.textContent = `${a.nama || a.email} (${a.role})`;
      document.getElementById('f-admin').appendChild(opt);
    });
    j.actions.forEach(a => {
      const opt = document.createElement('option');
      opt.value = a;
      opt.textContent = a;
      document.getElementById('f-action').appendChild(opt);
    });
  }

  const items = j.data;
  totalPages = j.meta.last_page;

  if (!items.length) {
    document.getElementById('log-container').innerHTML =
      '<div style="text-align:center;padding:3rem;color:var(--ink-soft)">Tidak ada log aktivitas ditemukan.</div>';
    document.getElementById('pagination').innerHTML = '';
    return;
  }

  const rows = items.map(log => {
    const u    = log.user || {};
    const tgl  = new Date(log.created_at).toLocaleString('id-ID', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
    const ctx  = log.context ? `<div style="margin-top:4px;font-size:10px;color:var(--ink-soft);font-family:monospace;word-break:break-all">${JSON.stringify(log.context)}</div>` : '';
    return `<tr style="border-top:1px solid var(--border)">
      <td style="padding:10px 14px;width:44px">
        <img src="${u.avatar_url||'https://ui-avatars.com/api/?name='+encodeURIComponent(u.nama||'?')+'&size=32&background=4A7C59&color=fff'}" style="width:32px;height:32px;border-radius:50%;object-fit:cover">
      </td>
      <td style="padding:10px 14px">
        <div style="font-size:13px;font-weight:500">${u.nama || u.email || '—'}</div>
        <div style="font-size:11px;color:${roleColor(u.role)};font-weight:600;text-transform:uppercase;letter-spacing:.05em">${u.role || ''}</div>
      </td>
      <td style="padding:10px 14px">${badgeFor(log.action)}</td>
      <td style="padding:10px 14px;max-width:320px">
        <div style="font-size:13px">${log.description || '—'}</div>
        ${ctx}
      </td>
      <td style="padding:10px 14px;font-size:12px;color:var(--ink-soft);white-space:nowrap">${log.ip || '—'}</td>
      <td style="padding:10px 14px;font-size:12px;color:var(--ink-soft);white-space:nowrap">${tgl}</td>
    </tr>`;
  }).join('');

  document.getElementById('log-container').innerHTML = `
    <div style="overflow-x:auto">
      <table style="width:100%;border-collapse:collapse">
        <thead>
          <tr style="background:var(--cream)">
            <th style="padding:10px 14px;width:44px"></th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">Admin</th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">Aksi</th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">Keterangan</th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">IP</th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">Waktu</th>
          </tr>
        </thead>
        <tbody>${rows}</tbody>
      </table>
    </div>`;

  const meta = j.meta;
  const start = (meta.current_page - 1) * meta.per_page + 1;
  const end   = Math.min(meta.current_page * meta.per_page, meta.total);
  document.getElementById('pagination').innerHTML = `
    <span>Menampilkan ${start}–${end} dari ${meta.total} log</span>
    <div style="display:flex;gap:6px">
      <button onclick="loadLog(${meta.current_page - 1})" ${meta.current_page <= 1 ? 'disabled' : ''} style="padding:5px 12px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;font-size:13px">←</button>
      <span style="padding:5px 12px;font-size:13px">${meta.current_page} / ${meta.last_page}</span>
      <button onclick="loadLog(${meta.current_page + 1})" ${meta.current_page >= meta.last_page ? 'disabled' : ''} style="padding:5px 12px;border:1px solid var(--border);border-radius:6px;background:#fff;cursor:pointer;font-size:13px">→</button>
    </div>`;
}

function resetFilter() {
  ['f-search','f-dari','f-sampai'].forEach(id => document.getElementById(id).value = '');
  document.getElementById('f-admin').value  = '';
  document.getElementById('f-action').value = '';
  loadLog(1);
}

loadLog(1);
</script>
@endsection
