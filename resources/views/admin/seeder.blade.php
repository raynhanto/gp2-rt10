@extends('layouts.admin')
@section('title', 'Manajemen Seeder — Admin')
@section('content')
<style>
.sd-group { margin-bottom: 2rem; }
.sd-group-label {
  font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
  color: var(--ink-mute); margin-bottom: .75rem; padding-left: 2px;
}
.sd-card {
  background: var(--warm); border: 1px solid var(--border);
  border-radius: var(--radius-sm); margin-bottom: .75rem;
  overflow: hidden;
}
.sd-card-top {
  display: flex; align-items: flex-start; gap: 1rem;
  padding: 1rem 1.125rem;
}
.sd-icon {
  width: 36px; height: 36px; border-radius: 8px; flex-shrink: 0;
  background: var(--forest-pale); color: var(--forest);
  display: flex; align-items: center; justify-content: center; font-size: 14px;
}
.sd-info { flex: 1; min-width: 0; }
.sd-label { font-size: 14px; font-weight: 600; color: var(--ink); margin-bottom: 2px; }
.sd-desc  { font-size: 12.5px; color: var(--ink-soft); line-height: 1.5; }
.sd-warning {
  display: flex; align-items: flex-start; gap: 6px;
  margin-top: .5rem; padding: .4rem .7rem;
  background: rgba(200,160,48,.1); border-radius: 6px;
  font-size: 11.5px; color: var(--gold-dark); line-height: 1.4;
}
.sd-warning i { flex-shrink: 0; margin-top: 1px; }
.sd-actions { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; padding-top: 2px; }
.sd-badge {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px;
}
.sd-badge-applied  { background: rgba(26,61,43,.1); color: var(--forest); }
.sd-badge-empty    { background: rgba(157,144,128,.1); color: var(--ink-mute); }
.sd-badge-rolled   { background: rgba(181,64,26,.08); color: var(--rust); }
.sd-history {
  border-top: 1px solid var(--border); background: rgba(26,61,43,.02);
  padding: .625rem 1.125rem; display: none;
}
.sd-history.open { display: block; }
.sd-history-row {
  display: flex; align-items: center; gap: .75rem;
  padding: .3rem 0; font-size: 12px; color: var(--ink-soft);
  border-bottom: 1px solid var(--border);
}
.sd-history-row:last-child { border-bottom: none; }
.sd-history-row .sd-run-badge {
  font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 99px;
  background: rgba(26,61,43,.1); color: var(--forest); flex-shrink: 0;
}
.sd-history-row .sd-run-badge.rolled { background: rgba(181,64,26,.08); color: var(--rust); }
.sd-history-meta { flex: 1; min-width: 0; }
.sd-history-meta span { margin-right: .5rem; }
.sd-history-meta .dim { color: var(--ink-mute); }
.btn-run {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: var(--radius-xs); border: none; cursor: pointer;
  font-size: 12.5px; font-weight: 600; font-family: 'DM Sans', sans-serif;
  background: var(--forest); color: #fff; transition: background .15s;
}
.btn-run:hover { background: var(--forest-mid); }
.btn-run:disabled { opacity: .45; cursor: not-allowed; }
.btn-rollback {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 11px; border-radius: var(--radius-xs);
  font-size: 11.5px; font-weight: 600; font-family: 'DM Sans', sans-serif;
  background: rgba(181,64,26,.1); color: var(--rust);
  border: 1px solid rgba(181,64,26,.18); cursor: pointer; transition: all .15s;
}
.btn-rollback:hover { background: rgba(181,64,26,.2); }
.btn-history {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 10px; border-radius: var(--radius-xs);
  font-size: 11.5px; font-weight: 500; font-family: 'DM Sans', sans-serif;
  background: transparent; color: var(--ink-soft);
  border: 1px solid var(--border); cursor: pointer; transition: all .15s;
}
.btn-history:hover { background: var(--parchment); }
.sd-toast {
  position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%);
  background: var(--ink); color: #fff; border-radius: 8px;
  padding: .7rem 1.25rem; font-size: 13px; font-weight: 500;
  display: none; align-items: center; gap: .5rem; z-index: 9999;
  box-shadow: var(--shadow-lg); max-width: 420px; text-align: center;
}
.sd-toast.show { display: flex; }
.sd-toast.err   { background: var(--rust); }
.sd-empty { text-align: center; padding: 3rem; color: var(--ink-mute); font-size: 13px; }
.sd-counts {
  display: flex; flex-wrap: wrap; gap: 6px; margin-top: .5rem;
}
.sd-count-pill {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 11px; font-weight: 600; padding: 2px 8px;
  border-radius: 99px; background: rgba(26,61,43,.07); color: var(--ink-mid);
}
.sd-count-pill.has-data { background: rgba(200,160,48,.14); color: var(--gold-dark); }
.sd-untracked {
  display: flex; align-items: flex-start; gap: 6px;
  margin-top: .5rem; padding: .4rem .7rem;
  background: rgba(200,160,48,.08); border: 1px solid rgba(200,160,48,.2);
  border-radius: 6px; font-size: 11.5px; color: var(--gold-dark); line-height: 1.4;
}
.sd-untracked i { flex-shrink: 0; margin-top: 1px; }
</style>

<div class="admin-page-header">
  <div>
    <div class="admin-page-title">Manajemen Seeder</div>
    <div class="admin-page-sub">Jalankan atau rollback data awal per modul. Hanya Super Admin yang dapat mengakses halaman ini.</div>
  </div>
  <button class="btn-run" onclick="loadSeeders()">
    <i class="fa fa-rotate"></i> Refresh
  </button>
</div>

<div id="seeder-container">
  <div class="sd-empty">Memuat data seeder...</div>
</div>

{{-- Confirm Modal --}}
<div id="confirm-modal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(10,20,14,.55);align-items:center;justify-content:center">
  <div style="background:var(--warm);border-radius:var(--radius);padding:2rem;max-width:420px;width:90%;box-shadow:var(--shadow-lg)">
    <div style="font-size:16px;font-weight:700;color:var(--ink);margin-bottom:.5rem" id="modal-title"></div>
    <div style="font-size:13px;color:var(--ink-soft);line-height:1.6;margin-bottom:1.5rem" id="modal-body"></div>
    <div style="display:flex;justify-content:flex-end;gap:.625rem">
      <button onclick="closeModal()" class="btn-history" style="padding:7px 16px;font-size:13px">Batal</button>
      <button id="modal-confirm-btn" class="btn-run" style="font-size:13px">Konfirmasi</button>
    </div>
  </div>
</div>

<div class="sd-toast" id="sd-toast"></div>

<script>
const _csrf = '{{ csrf_token() }}';
let _seeders = [];

async function loadSeeders() {
  try {
    const res = await fetch('/api/admin/seeder');
    const json = await res.json();
    if (!res.ok) {
      document.getElementById('seeder-container').innerHTML =
        `<div class="sd-empty" style="color:var(--rust)"><i class="fa fa-triangle-exclamation" style="margin-right:6px"></i>${json.error || 'Gagal memuat seeder (HTTP ' + res.status + ')'}</div>`;
      return;
    }
    _seeders = json.data || [];
    renderSeeders(_seeders);
  } catch (e) {
    document.getElementById('seeder-container').innerHTML =
      `<div class="sd-empty" style="color:var(--rust)"><i class="fa fa-triangle-exclamation" style="margin-right:6px"></i>Gagal menghubungi server: ${e.message}</div>`;
  }
}

function renderSeeders(seeders) {
  const groups = {};
  for (const s of seeders) {
    if (!groups[s.group]) groups[s.group] = [];
    groups[s.group].push(s);
  }

  let html = '';
  for (const [group, list] of Object.entries(groups)) {
    html += `<div class="sd-group">
      <div class="sd-group-label"><i class="fa fa-layer-group" style="margin-right:5px"></i>${group}</div>`;

    for (const s of list) {
      const isApplied    = s.last_applied !== null;
      const depsMet      = s.depends_on.every(dep => {
        const depSeeder = seeders.find(x => x.key === dep);
        return depSeeder && depSeeder.last_applied !== null;
      });
      const hasUntracked = !isApplied && s.total_rows > 0;

      // Status badge
      const badgeHtml = isApplied
        ? `<span class="sd-badge sd-badge-applied"><i class="fa fa-check"></i> Aktif</span>`
        : `<span class="sd-badge sd-badge-empty"><i class="fa fa-minus"></i> Belum Dijalankan</span>`;

      // Table row counts
      const countPills = Object.entries(s.table_counts)
        .map(([tbl, cnt]) => `<span class="sd-count-pill ${cnt > 0 ? 'has-data' : ''}"><i class="fa fa-table"></i>${tbl}: ${cnt}</span>`)
        .join('');
      const countsHtml = `<div class="sd-counts">${countPills}</div>`;

      // Warning about data that exists but isn't tracked
      const untrackedHtml = hasUntracked
        ? `<div class="sd-untracked"><i class="fa fa-triangle-exclamation"></i><span>Tabel sudah berisi data (${s.total_rows} baris) namun belum terdaftar di sistem ini. Jalankan seeder hanya jika data tersebut bukan data nyata.</span></div>`
        : '';

      const warnHtml = s.warning
        ? `<div class="sd-warning"><i class="fa fa-triangle-exclamation"></i><span>${s.warning}</span></div>`
        : '';

      const historyBtn = s.runs.length > 0
        ? `<button class="btn-history" onclick="toggleHistory('${s.key}')"><i class="fa fa-clock-rotate-left"></i> ${s.runs.length} Riwayat</button>`
        : '';

      const runBtn = `<button class="btn-run" onclick="confirmRun('${s.key}')" ${!depsMet ? 'disabled title="Dependensi belum terpenuhi"' : ''}>
        <i class="fa fa-play"></i> Jalankan
      </button>`;

      const rollbackBtn = isApplied
        ? `<button class="btn-rollback" onclick="confirmRollback(${s.last_applied.id}, '${s.label}')">
            <i class="fa fa-rotate-left"></i> Rollback
          </button>`
        : '';

      let historyHtml = '';
      if (s.runs.length > 0) {
        historyHtml = `<div class="sd-history" id="hist-${s.key}">`;
        for (const r of s.runs) {
          const isRolled = r.status === 'rolled_back';
          const badgeCls = isRolled ? 'rolled' : '';
          const badgeTxt = isRolled ? 'Rolled Back' : 'Applied';
          const rbInfo   = isRolled && r.rolled_back_at
            ? `<span class="dim">— di-rollback ${formatDate(r.rolled_back_at)} oleh ${r.rolled_back_by}</span>`
            : '';
          historyHtml += `<div class="sd-history-row">
            <span class="sd-run-badge ${badgeCls}">${badgeTxt}</span>
            <div class="sd-history-meta">
              <span>${formatDate(r.run_at)} oleh <strong>${r.run_by}</strong></span>
              ${rbInfo}
            </div>
            ${!isRolled ? `<button class="btn-rollback" style="font-size:11px;padding:3px 9px" onclick="confirmRollback(${r.id}, '${s.label}')"><i class="fa fa-rotate-left"></i></button>` : ''}
          </div>`;
        }
        historyHtml += '</div>';
      }

      html += `<div class="sd-card">
        <div class="sd-card-top">
          <div class="sd-icon"><i class="fa fa-database"></i></div>
          <div class="sd-info">
            <div class="sd-label">${s.label}</div>
            <div class="sd-desc">${s.description}</div>
            ${countsHtml}
            ${untrackedHtml}
            ${warnHtml}
            ${s.depends_on.length > 0 ? `<div style="font-size:11.5px;color:var(--ink-mute);margin-top:.35rem"><i class="fa fa-link" style="margin-right:3px"></i>Bergantung pada: ${s.depends_on.join(', ')}</div>` : ''}
          </div>
          <div class="sd-actions">
            ${badgeHtml}
            ${historyBtn}
            ${rollbackBtn}
            ${runBtn}
          </div>
        </div>
        ${historyHtml}
      </div>`;
    }

    html += '</div>';
  }

  document.getElementById('seeder-container').innerHTML = html || '<div class="sd-empty">Tidak ada seeder terdaftar.</div>';
}

function toggleHistory(key) {
  const el = document.getElementById('hist-' + key);
  if (el) el.classList.toggle('open');
}

function formatDate(str) {
  if (!str) return '—';
  const d = new Date(str);
  return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
    + ' ' + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
}

let _pendingAction = null;

function confirmRun(key) {
  const s = _seeders.find(x => x.key === key);
  openModal(
    `Jalankan Seeder — ${s.label}`,
    `Seeder <strong>${s.label}</strong> akan dijalankan dan data baru akan ditambahkan ke database.<br><br>Data yang dimasukkan dapat di-rollback nanti.`,
    'var(--forest)',
    () => doRun(key)
  );
}

function confirmRollback(runId, label) {
  openModal(
    `Rollback Seeder — ${label}`,
    `Semua baris yang diinsert oleh run ini akan <strong>dihapus permanen</strong> dari database.<br><br>Pastikan tidak ada data transaksi nyata yang bergantung pada data ini sebelum rollback.`,
    'var(--rust)',
    () => doRollback(runId)
  );
}

function openModal(title, body, btnColor, onConfirm) {
  document.getElementById('modal-title').innerHTML = title;
  document.getElementById('modal-body').innerHTML  = body;
  const btn = document.getElementById('modal-confirm-btn');
  btn.style.background = btnColor;
  _pendingAction = onConfirm;
  document.getElementById('confirm-modal').style.display = 'flex';
}

function closeModal() {
  document.getElementById('confirm-modal').style.display = 'none';
  _pendingAction = null;
}

document.getElementById('modal-confirm-btn').addEventListener('click', () => {
  const action = _pendingAction;
  closeModal();
  if (action) action();
});

document.getElementById('confirm-modal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

async function apiPost(url) {
  showToast('Memproses...', false, 0);
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': _csrf, 'Content-Type': 'application/json' },
    });
    let json;
    try {
      json = await res.json();
    } catch (_) {
      showToast(`Server error (HTTP ${res.status}) — cek log Laravel.`, true);
      return null;
    }
    return { ok: res.ok, json };
  } catch (networkErr) {
    showToast('Tidak dapat menghubungi server: ' + networkErr.message, true);
    return null;
  }
}

async function doRun(key) {
  const result = await apiPost(`/api/admin/seeder/${key}/run`);
  if (!result) return;
  showToast(result.json.message || result.json.error, !result.ok);
  if (result.ok) loadSeeders();
}

async function doRollback(runId) {
  const result = await apiPost(`/api/admin/seeder/${runId}/rollback`);
  if (!result) return;
  showToast(result.json.message || result.json.error, !result.ok);
  if (result.ok) loadSeeders();
}

function showToast(msg, isErr = false, duration = 5000) {
  const el = document.getElementById('sd-toast');
  el.textContent = msg;
  el.className = 'sd-toast show' + (isErr ? ' err' : '');
  if (duration > 0) setTimeout(() => el.className = 'sd-toast', duration);
}

loadSeeders();
</script>
@endsection
