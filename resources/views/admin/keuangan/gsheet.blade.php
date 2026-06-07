@extends('layouts.admin')
@section('title', 'Google Sheets Sync')
@section('content')
<div class="container">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <a href="/admin/keuangan" style="font-size:13px;color:var(--ink-soft)">← Keuangan</a>
      <div class="section-title" style="font-size:1.8rem;margin-top:6px">Google Sheets Mirror</div>
      <div style="font-size:13px;color:var(--ink-soft);margin-top:4px">Sinkronisasi data keuangan ke Google Spreadsheet secara otomatis</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">

    {{-- Config Panel --}}
    <div>
      <div class="card" style="margin-bottom:1.5rem">
        <div style="font-size:15px;font-weight:600;margin-bottom:1.25rem">Status Koneksi</div>
        <div id="status-badge" style="padding:10px 16px;border-radius:var(--radius-sm);font-size:13px;background:#F0F7F3;color:var(--forest);margin-bottom:1rem">Memuat...</div>
        <div style="font-size:12px;color:var(--ink-soft)" id="last-sync-info">—</div>
        <div style="display:flex;gap:8px;margin-top:1rem">
          <button onclick="syncAll()" class="btn-primary" style="padding:9px 18px;font-size:13px"><i class="fa-solid fa-arrows-rotate" style="margin-right:6px"></i>Sync Semua Tab</button>
          <button onclick="loadStatus()" class="btn-secondary" style="padding:9px 14px;font-size:13px">Refresh</button>
        </div>
      </div>

      <div class="card" style="margin-bottom:1.5rem">
        <div style="font-size:15px;font-weight:600;margin-bottom:1.25rem">Sync Per Tab</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px" id="sync-buttons"></div>
      </div>
    </div>

    {{-- Config Form --}}
    <div>
      <div class="card" style="margin-bottom:1.5rem">
        <div style="font-size:15px;font-weight:600;margin-bottom:1.25rem">Konfigurasi Spreadsheet</div>

        <div style="margin-bottom:1rem">
          <label class="flabel">Spreadsheet ID</label>
          <input type="text" id="conf-sheet-id" placeholder="Paste Spreadsheet ID dari URL Google Sheets" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
          <div style="font-size:11px;color:var(--ink-soft);margin-top:4px">Dari URL: docs.google.com/spreadsheets/d/<b>SPREADSHEET_ID</b>/edit</div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
          @foreach(['kas'=>'Tab Kas','iuran'=>'Tab Iuran','pengeluaran'=>'Tab Pengeluaran','donasi'=>'Tab Donasi','summary'=>'Tab Summary'] as $key=>$label)
          <div>
            <label class="flabel">{{ $label }}</label>
            <input type="text" id="conf-tab-{{ $key }}" placeholder="{{ ucfirst($key) }}" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;outline:none">
          </div>
          @endforeach
          <div style="display:flex;align-items:center;gap:8px;padding-top:1.5rem">
            <input type="checkbox" id="conf-auto-sync" style="width:16px;height:16px">
            <label for="conf-auto-sync" style="font-size:13px;cursor:pointer">Auto sync saat ada transaksi baru</label>
          </div>
        </div>

        <button onclick="saveConfig()" class="btn-primary" style="width:100%;justify-content:center">Simpan Konfigurasi</button>
      </div>

      <div class="card">
        <div style="font-size:15px;font-weight:600;margin-bottom:0.5rem">Service Account Credentials</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-bottom:1rem">Upload file JSON dari Google Cloud Console (Service Account). Pastikan sudah diberi akses Editor ke spreadsheet.</div>
        <div id="creds-status" style="font-size:12px;color:var(--ink-soft);margin-bottom:1rem">—</div>
        <div style="border:2px dashed var(--border);border-radius:var(--radius-sm);padding:1.5rem;text-align:center;cursor:pointer" onclick="document.getElementById('creds-file').click()">
          <div style="font-size:13px;color:var(--ink-soft)"><i class="fa fa-file-code" style="color:var(--forest);margin-right:6px"></i>Klik untuk upload credentials.json</div>
          <input type="file" id="creds-file" style="display:none" accept=".json" onchange="uploadCreds()">
        </div>
      </div>
    </div>
  </div>

  {{-- Sync Log --}}
  <div class="card" style="margin-top:1.5rem">
    <div style="font-size:15px;font-weight:600;margin-bottom:1rem">Log Sinkronisasi</div>
    <div id="sync-log"><div style="text-align:center;padding:1.5rem;color:var(--ink-soft)">Memuat log...</div></div>
  </div>
</div>
<style>.flabel{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-soft);margin-bottom:5px}</style>
@endsection
@section('scripts')
<script>
const tabs = ['kas','iuran','pengeluaran','donasi','summary'];
const tabLabel = { kas:'Kas', iuran:'Iuran', pengeluaran:'Pengeluaran', donasi:'Donasi', summary:'Summary' };

async function loadStatus() {
  const r = await fetch('/api/gsheet/status');
  const j = await r.json();
  if (!j.success) return;

  const cfg = j.config;
  const configured = cfg.configured;

  document.getElementById('status-badge').style.background = configured ? '#E8F4ED' : '#FDECEA';
  document.getElementById('status-badge').style.color      = configured ? 'var(--forest)' : 'var(--rust)';
  document.getElementById('status-badge').innerHTML = configured
    ? '<i class="fa fa-circle-check" style="margin-right:6px"></i> Terkoneksi — Spreadsheet ID: ' + cfg.spreadsheet_id.slice(0,20) + '...'
    : '<i class="fa fa-circle-xmark" style="margin-right:6px"></i> Belum dikonfigurasi — Upload credentials dan masukkan Spreadsheet ID';

  document.getElementById('last-sync-info').textContent = cfg.last_synced_at
    ? 'Terakhir sync: ' + new Date(cfg.last_synced_at).toLocaleString('id-ID')
    : 'Belum pernah sync';

  // Fill config form
  document.getElementById('conf-sheet-id').value = cfg.spreadsheet_id || '';
  document.getElementById('conf-auto-sync').checked = cfg.auto_sync;
  tabs.forEach(t => {
    const el = document.getElementById('conf-tab-' + t);
    if (el) el.value = cfg['tab_' + t] || '';
  });

  // Sync buttons
  document.getElementById('sync-buttons').innerHTML = tabs.map(t=>
    `<button onclick="syncTab('${t}')" style="padding:9px 14px;border:1.5px solid var(--border);border-radius:8px;background:#fff;font-size:13px;cursor:pointer;font-family:'DM Sans',sans-serif" id="sync-btn-${t}">
      <i class="fa fa-table" style="color:var(--forest);margin-right:6px"></i> ${tabLabel[t]}
    </button>`
  ).join('');

  // Logs
  if (j.logs.length) {
    document.getElementById('sync-log').innerHTML = `<div style="overflow-x:auto"><table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:var(--cream)">${['Tab','Baris','Status','Durasi','Waktu','Error'].map(h=>`<th style="padding:8px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--ink-soft);text-transform:uppercase">${h}</th>`).join('')}</tr></thead>
      <tbody>${j.logs.map(l=>`<tr style="border-top:1px solid var(--border)">
        <td style="padding:8px 14px;font-size:13px;font-weight:500">${l.tab}</td>
        <td style="padding:8px 14px;font-size:13px">${l.rows_written}</td>
        <td style="padding:8px 14px"><span style="font-size:11px;padding:2px 9px;border-radius:99px;background:${l.status==='ok'?'#E8F4ED':'#FDECEA'};color:${l.status==='ok'?'var(--forest)':'var(--rust)'}">${l.status}</span></td>
        <td style="padding:8px 14px;font-size:12px;color:var(--ink-soft)">${l.duration_ms}ms</td>
        <td style="padding:8px 14px;font-size:12px;color:var(--ink-soft)">${new Date(l.created_at).toLocaleString('id-ID')}</td>
        <td style="padding:8px 14px;font-size:11px;color:var(--rust);max-width:200px;overflow:hidden;text-overflow:ellipsis">${l.error_msg||''}</td>
      </tr>`).join('')}</tbody>
    </table></div>`;
  } else {
    document.getElementById('sync-log').innerHTML = '<div style="text-align:center;padding:1.5rem;color:var(--ink-soft)">Belum ada log sinkronisasi.</div>';
  }
}

async function saveConfig() {
  const body = {
    spreadsheet_id: document.getElementById('conf-sheet-id').value.trim(),
    auto_sync:      document.getElementById('conf-auto-sync').checked,
  };
  tabs.forEach(t => {
    const el = document.getElementById('conf-tab-' + t);
    if (el) body['tab_' + t] = el.value.trim() || t.charAt(0).toUpperCase() + t.slice(1);
  });

  const r = await fetch('/api/gsheet/config', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrfToken}, body:JSON.stringify(body) });
  const j = await r.json();
  if (j.success) { showToast(j.message); loadStatus(); }
  else alert(j.message);
}

async function uploadCreds() {
  const file = document.getElementById('creds-file').files[0];
  if (!file) return;
  const fd = new FormData();
  fd.append('credentials', file);
  fd.append('_token', _csrfToken);
  const r = await fetch('/api/gsheet/credentials', { method:'POST', body:fd });
  const j = await r.json();
  if (j.success) {
    document.getElementById('creds-status').innerHTML = `<span style="color:var(--forest)"><i class="fa fa-check-circle"></i> Credentials aktif — ${j.email}</span>`;
    showToast(j.message);
  } else {
    alert(j.message);
  }
  document.getElementById('creds-file').value = '';
}

async function syncTab(tab) {
  const btn = document.getElementById('sync-btn-' + tab);
  if (btn) { btn.disabled = true; btn.textContent = '⏳ Syncing...'; }

  const r = await fetch(`/api/gsheet/sync/${tab}`, { method:'POST', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();

  if (j.success) {
    showToast(`Tab ${tab} berhasil disync (${j.rows} baris, ${j.ms}ms)`);
  } else {
    alert('Gagal sync tab ' + tab + ': ' + j.message);
  }

  await loadStatus();
}

async function syncAll() {
  showToast('Memulai sinkronisasi semua tab...');
  const r = await fetch('/api/gsheet/sync-all', { method:'POST', headers:{'X-CSRF-TOKEN':_csrfToken} });
  const j = await r.json();
  const ok = Object.values(j.results).filter(r=>r.success).length;
  const fail = Object.values(j.results).filter(r=>!r.success).length;
  showToast(`Sync selesai: ${ok} tab berhasil, ${fail} gagal`);
  await loadStatus();
}

loadStatus();
</script>
@endsection
