<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin') — GP2 RT10</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
:root{
  --cream:#F3EDD8;--warm:#FDFAF2;--parchment:#EDE5CC;
  --forest:#1A3D2B;--forest-mid:#2A5C40;--forest-light:#3D7A56;--forest-pale:#EBF3EE;
  --gold:#C8A030;--gold-dark:#9A7818;--gold-light:#DFBA56;--gold-pale:#FBF3DC;--gold-wash:#F5ECC5;
  --rust:#B5401A;--sand:#D4BF98;--ink:#1A1810;--ink-mid:#3D3828;--ink-soft:#6B6050;--ink-mute:#9D9080;
  --border:rgba(26,61,43,0.12);--border-gold:rgba(200,160,48,0.28);
  --radius:16px;--radius-sm:10px;--radius-xs:6px;
  --shadow-sm:0 2px 8px rgba(26,61,43,0.07);
  --shadow-md:0 8px 24px rgba(26,61,43,0.10);
  --shadow-lg:0 20px 48px rgba(26,61,43,0.13);
  --sidebar-w:228px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{font-family:'DM Sans',sans-serif;background:var(--warm);color:var(--ink);overflow-x:hidden}
a{text-decoration:none;color:inherit}

/* ── Shell ── */
.admin-shell{display:flex;min-height:100vh}

/* ── Sidebar ── */
.admin-sidebar{
  width:var(--sidebar-w);flex-shrink:0;
  position:fixed;top:0;left:0;bottom:0;
  background:linear-gradient(180deg,#0F2218 0%,#1A3D2B 100%);
  display:flex;flex-direction:column;
  z-index:200;overflow-y:auto;overflow-x:hidden;
  transition:transform 0.26s cubic-bezier(0.4,0,0.2,1);
}
.sidebar-logo{
  display:flex;align-items:center;gap:10px;
  padding:1.25rem 1.125rem 1rem;
  border-bottom:1px solid rgba(200,160,48,0.12);
  flex-shrink:0;
}
.sidebar-logo-mark{
  width:32px;height:32px;flex-shrink:0;border-radius:8px;
  background:rgba(200,160,48,0.18);
  display:flex;align-items:center;justify-content:center;
}
.sidebar-logo-mark svg{width:17px;height:17px;fill:var(--gold)}
.sidebar-brand{font-family:'DM Serif Display',serif;font-size:13.5px;color:#fff;line-height:1.25}
.sidebar-brand small{display:block;font-family:'DM Sans',sans-serif;font-size:9.5px;color:rgba(200,160,48,0.65);font-weight:600;letter-spacing:0.08em;text-transform:uppercase;margin-top:2px}

.sidebar-nav{flex:1;padding:0.625rem 0;overflow-y:auto}
.sidebar-group-label{
  font-size:9px;font-weight:600;letter-spacing:0.12em;text-transform:uppercase;
  color:rgba(255,255,255,0.28);
  padding:1rem 1.125rem 0.3rem;
}
.sidebar-nav a{
  display:flex;align-items:center;gap:10px;
  padding:9px 1.125rem;
  font-size:13px;font-weight:500;
  color:rgba(255,255,255,0.52);
  transition:color 0.15s,background 0.15s;
  position:relative;white-space:nowrap;
}
.sidebar-nav a i{width:15px;text-align:center;font-size:12.5px;flex-shrink:0;opacity:0.75;transition:opacity 0.15s}
.sidebar-nav a:hover{color:rgba(255,255,255,0.88);background:rgba(255,255,255,0.05)}
.sidebar-nav a.active{color:#fff;background:rgba(200,160,48,0.13)}
.sidebar-nav a.active::before{content:'';position:absolute;left:0;top:3px;bottom:3px;width:3px;background:var(--gold);border-radius:0 3px 3px 0}
.sidebar-nav a.active i{color:var(--gold);opacity:1}
.sidebar-divider{height:1px;background:rgba(255,255,255,0.06);margin:0.375rem 1.125rem}

.sidebar-footer{
  flex-shrink:0;
  padding:0.875rem 1rem 1rem;
  border-top:1px solid rgba(255,255,255,0.07);
}
.sidebar-user{display:flex;align-items:center;gap:9px;padding:6px 4px 10px}
.sidebar-avatar{width:28px;height:28px;border-radius:50%;object-fit:cover;border:1.5px solid rgba(200,160,48,0.4)}
.sidebar-avatar-placeholder{width:28px;height:28px;border-radius:50%;background:rgba(200,160,48,0.18);color:var(--gold);font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.sidebar-user-info{min-width:0}
.sidebar-user-name{font-size:12.5px;font-weight:500;color:rgba(255,255,255,0.72);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sidebar-user-role{font-size:10px;color:rgba(200,160,48,0.6);font-weight:500;letter-spacing:0.02em}
.sidebar-action{
  display:flex;align-items:center;gap:8px;width:100%;
  padding:8px 10px;border-radius:8px;
  font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;
  cursor:pointer;border:none;text-align:left;transition:all 0.15s;
  margin-bottom:4px;
}
.sidebar-action-public{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.55)}
.sidebar-action-public:hover{background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.85)}
.sidebar-action-logout{background:rgba(181,64,26,0.14);color:rgba(255,110,80,0.8)}
.sidebar-action-logout:hover{background:rgba(181,64,26,0.26);color:#ff7a5a}

/* ── Mobile topbar ── */
.admin-topbar{
  display:none;position:fixed;top:0;left:0;right:0;height:54px;
  background:#122A1E;border-bottom:1px solid rgba(200,160,48,0.18);
  align-items:center;justify-content:space-between;
  padding:0 1rem;z-index:150;
}
.topbar-toggle{background:none;border:none;cursor:pointer;padding:6px;display:flex;flex-direction:column;gap:4px}
.topbar-toggle span{display:block;width:20px;height:2px;background:rgba(255,255,255,0.65);border-radius:2px;transition:all 0.22s}
.topbar-brand{font-family:'DM Serif Display',serif;font-size:15px;color:#fff}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:190}

/* ── Main ── */
.admin-body{margin-left:var(--sidebar-w);flex:1;min-height:100vh;display:flex;flex-direction:column}
.admin-content{flex:1;padding:2rem 2.5rem 3rem}
.admin-content .container{max-width:none;padding:0;margin:0}

/* ── Shared components ── */
.section-label{display:inline-flex;align-items:center;gap:8px;font-size:10.5px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:var(--forest-light);margin-bottom:0.875rem}
.section-label::before{content:'';width:20px;height:2.5px;background:var(--gold);border-radius:2px}
.section-title{font-family:'DM Serif Display',serif;font-size:clamp(1.5rem,2.5vw,1.9rem);color:var(--forest);line-height:1.2;letter-spacing:-0.02em}
.btn-primary{background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:#fff;padding:10px 22px;border-radius:100px;font-size:13.5px;font-weight:500;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s;font-family:'DM Sans',sans-serif;box-shadow:0 3px 12px rgba(26,61,43,0.22)}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(26,61,43,0.3)}
.btn-secondary{background:#fff;color:var(--forest);padding:10px 22px;border-radius:100px;font-size:13.5px;font-weight:500;border:1.5px solid rgba(26,61,43,0.22);cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s;font-family:'DM Sans',sans-serif}
.btn-secondary:hover{background:var(--forest-pale);border-color:var(--forest)}
.btn-gold{background:linear-gradient(135deg,var(--gold-dark),var(--gold));color:#fff;padding:10px 22px;border-radius:100px;font-size:13.5px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s;font-family:'DM Sans',sans-serif;box-shadow:0 3px 12px rgba(200,160,48,0.28)}
.btn-gold:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(200,160,48,0.38)}
.badge{display:inline-flex;align-items:center;font-size:10.5px;font-weight:600;padding:3px 10px;border-radius:100px;letter-spacing:0.02em}
.badge-urgent{background:#FDECEA;color:var(--rust)}
.badge-open{background:var(--forest-pale);color:var(--forest-mid)}
.badge-nearly{background:var(--gold-pale);color:var(--gold-dark)}
.badge-done{background:#EFEFED;color:var(--ink-soft)}
.gp2-chip{display:inline-flex;align-items:center;gap:7px;background:#fff;border:1px solid var(--border-gold);padding:5px 14px;border-radius:100px;font-size:10.5px;font-weight:600;color:var(--forest);letter-spacing:0.06em;text-transform:uppercase}
.gp2-chip::before{content:'';width:7px;height:7px;background:var(--gold);border-radius:50%;flex-shrink:0}
.card{background:#fff;border-radius:var(--radius);border:1px solid var(--border);padding:1.5rem;box-shadow:var(--shadow-sm)}
.progress-track{height:6px;background:var(--parchment);border-radius:99px;overflow:hidden;margin:10px 0}
.progress-fill{height:100%;border-radius:99px;transition:width 0.8s cubic-bezier(0.25,0.8,0.25,1)}
.pf-green{background:linear-gradient(90deg,var(--forest-mid),var(--forest-light))}
.pf-gold{background:linear-gradient(90deg,var(--gold-dark),var(--gold-light))}
.pf-rust{background:linear-gradient(90deg,var(--rust),#E8896A)}
.gold-divider{width:40px;height:3px;background:linear-gradient(90deg,var(--gold-dark),var(--gold-light));border-radius:2px;margin-bottom:1rem}
.toast{position:fixed;bottom:32px;left:calc(var(--sidebar-w) + 1rem);right:1rem;display:flex;justify-content:center;pointer-events:none;z-index:999}
.toast-inner{background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:#fff;padding:13px 26px;border-radius:100px;font-size:14px;font-weight:500;box-shadow:var(--shadow-lg);opacity:0;transform:translateY(16px);transition:all 0.28s;white-space:nowrap}
.toast-inner.show{opacity:1;transform:translateY(0)}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

/* ── Responsive ── */
@media(max-width:900px){
  .admin-topbar{display:flex}
  .admin-sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}
  .admin-sidebar.open{transform:translateX(0)}
  .sidebar-overlay.open{display:block}
  .admin-body{margin-left:0;padding-top:54px}
  .admin-content{padding:1.5rem 1.125rem 3rem}
  .toast{left:1rem}
}
@media(max-width:480px){
  .admin-content{padding:1.25rem 0.875rem 3rem}
}
</style>
@yield('styles')
</head>
<body>

<div class="admin-topbar">
  <button class="topbar-toggle" id="sidebar-toggle" onclick="toggleSidebar()">
    <span></span><span></span><span></span>
  </button>
  <div class="topbar-brand">Admin Panel</div>
  <div style="width:32px"></div>
</div>
<div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="admin-shell">

  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-logo">
      <div class="sidebar-logo-mark">
        <svg viewBox="0 0 24 24"><path d="M12 2L2 8v14h7v-7h6v7h7V8L12 2z"/></svg>
      </div>
      <div class="sidebar-brand">
        GP2 RT10
        <small>Admin Panel</small>
      </div>
    </div>

    @php $authRole = auth()->user()->role; @endphp
    <div class="sidebar-nav">

      {{-- Dashboard: all admin roles --}}
      <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">
        <i class="fa fa-gauge"></i> Dashboard
      </a>

      {{-- Financial section: bendahara, admin, super_admin --}}
      @if(in_array($authRole, ['bendahara', 'admin', 'super_admin']))
        <a href="/admin/verifikasi" class="{{ request()->is('admin/verifikasi') ? 'active' : '' }}">
          <i class="fa fa-circle-check"></i> Verifikasi Donasi
        </a>

        <div class="sidebar-group-label">Kampanye</div>
        <a href="/admin/kampanye" class="{{ request()->is('admin/kampanye') ? 'active' : '' }}">
          <i class="fa fa-flag"></i> Kelola Kampanye
        </a>

        <div class="sidebar-group-label">Keuangan</div>
        <a href="/admin/keuangan" class="{{ request()->is('admin/keuangan') && !request()->is('admin/keuangan/*') ? 'active' : '' }}">
          <i class="fa fa-chart-line"></i> Dashboard Keuangan
        </a>
        <a href="/admin/keuangan/kas" class="{{ request()->is('admin/keuangan/kas') ? 'active' : '' }}">
          <i class="fa fa-coins"></i> Kas
        </a>
        <a href="/admin/keuangan/transaksi-instan" class="{{ request()->is('admin/keuangan/transaksi-instan') ? 'active' : '' }}">
          <i class="fa fa-right-left"></i> Transaksi Instan
        </a>
        <a href="/admin/keuangan/iuran" class="{{ request()->is('admin/keuangan/iuran*') ? 'active' : '' }}">
          <i class="fa fa-calendar-check"></i> Iuran Bulanan
        </a>
        <a href="/admin/keuangan/pengeluaran" class="{{ request()->is('admin/keuangan/pengeluaran') ? 'active' : '' }}">
          <i class="fa fa-receipt"></i> Pengeluaran
        </a>
        <a href="/admin/keuangan/anggaran" class="{{ request()->is('admin/keuangan/anggaran') ? 'active' : '' }}">
          <i class="fa fa-chart-pie"></i> Anggaran
        </a>
        <a href="/admin/keuangan/kategori" class="{{ request()->is('admin/keuangan/kategori') ? 'active' : '' }}">
          <i class="fa fa-tags"></i> Kategori
        </a>
        <a href="/admin/keuangan/laporan" class="{{ request()->is('admin/keuangan/laporan') ? 'active' : '' }}">
          <i class="fa fa-file-lines"></i> Laporan & Export
        </a>
        <a href="/admin/keuangan/gsheet" class="{{ request()->is('admin/keuangan/gsheet') ? 'active' : '' }}">
          <i class="fa fa-table"></i> Google Sheets
        </a>
      @endif

      {{-- Community section: sekretaris, admin, super_admin --}}
      @if(in_array($authRole, ['sekretaris', 'admin', 'super_admin']))
        <div class="sidebar-divider"></div>
        <div class="sidebar-group-label">Komunitas</div>
        <a href="/admin/warga" class="{{ request()->is('admin/warga') ? 'active' : '' }}">
          <i class="fa fa-users"></i> Data Warga
        </a>
        <a href="/admin/pengumuman" class="{{ request()->is('admin/pengumuman') ? 'active' : '' }}">
          <i class="fa fa-bullhorn"></i> Pengumuman
        </a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-group-label">Kependudukan</div>
        <a href="/admin/kependudukan" class="{{ request()->is('admin/kependudukan') && !request()->is('admin/kependudukan/*') ? 'active' : '' }}">
          <i class="fa fa-house-chimney"></i> Dashboard
        </a>
        <a href="/admin/kependudukan/warga" class="{{ request()->is('admin/kependudukan/warga*') ? 'active' : '' }}">
          <i class="fa fa-id-card"></i> Data Warga & KK
        </a>
        <a href="/admin/kependudukan/kendaraan" class="{{ request()->is('admin/kependudukan/kendaraan*') ? 'active' : '' }}">
          <i class="fa fa-car"></i> Data Kendaraan
        </a>
        <a href="/admin/kependudukan/peta" class="{{ request()->is('admin/kependudukan/peta*') ? 'active' : '' }}">
          <i class="fa fa-map-location-dot"></i> Peta Warga
        </a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-group-label">Surat &amp; Dokumen</div>
        <a href="/admin/surat" class="{{ request()->is('admin/surat*') ? 'active' : '' }}">
          <i class="fa fa-file-lines"></i> Permohonan Surat
        </a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-group-label">Kelembagaan</div>
        <a href="/admin/kelembagaan/agenda" class="{{ request()->is('admin/kelembagaan/agenda*') ? 'active' : '' }}">
          <i class="fa fa-calendar-days"></i> Agenda Kegiatan
        </a>
        <a href="/admin/kelembagaan/galeri" class="{{ request()->is('admin/kelembagaan/galeri*') ? 'active' : '' }}">
          <i class="fa fa-images"></i> Galeri Foto
        </a>
        <a href="/admin/kelembagaan/berita" class="{{ request()->is('admin/kelembagaan/berita*') ? 'active' : '' }}">
          <i class="fa fa-newspaper"></i> Berita & Info
        </a>
        <a href="/admin/kelembagaan/tata-tertib" class="{{ request()->is('admin/kelembagaan/tata-tertib*') ? 'active' : '' }}">
          <i class="fa fa-scale-balanced"></i> Tata Tertib
        </a>
        <a href="/admin/kelembagaan/program-kerja" class="{{ request()->is('admin/kelembagaan/program-kerja*') ? 'active' : '' }}">
          <i class="fa fa-list-check"></i> Program Kerja
        </a>
        <a href="/admin/kelembagaan/saran" class="{{ request()->is('admin/kelembagaan/saran*') ? 'active' : '' }}">
          <i class="fa fa-comments"></i> Saran & Keluhan
        </a>
        <a href="/admin/kelembagaan/usulan" class="{{ request()->is('admin/kelembagaan/usulan*') ? 'active' : '' }}">
          <i class="fa fa-lightbulb"></i> Usulan Pembangunan
        </a>
        <a href="/admin/kelembagaan/bantuan-sosial" class="{{ request()->is('admin/kelembagaan/bantuan-sosial*') ? 'active' : '' }}">
          <i class="fa fa-hand-holding-heart"></i> Bantuan Sosial
        </a>
        <a href="/admin/kelembagaan/inventaris" class="{{ request()->is('admin/kelembagaan/inventaris*') ? 'active' : '' }}">
          <i class="fa fa-boxes-stacked"></i> Inventaris RT
        </a>
      @endif

      {{-- System section: admin, super_admin only --}}
      @if(in_array($authRole, ['admin', 'super_admin']))
        <div class="sidebar-divider"></div>
        <div class="sidebar-group-label">Sistem</div>
        <a href="/admin/aktivitas" class="{{ request()->is('admin/aktivitas') ? 'active' : '' }}">
          <i class="fa fa-clock-rotate-left"></i> Log Aktivitas
        </a>
        <a href="/admin/pengaturan" class="{{ request()->is('admin/pengaturan') ? 'active' : '' }}">
          <i class="fa fa-gear"></i> Pengaturan
        </a>
        @if($authRole === 'super_admin')
        <a href="/admin/seeder" class="{{ request()->is('admin/seeder') ? 'active' : '' }}">
          <i class="fa fa-database"></i> Manajemen Seeder
        </a>
        @endif
      @endif

    </div>

    <div class="sidebar-footer">
      @php $initial = strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)); @endphp
      <div class="sidebar-user">
        @if(auth()->user()->avatar_url)
          <img src="{{ auth()->user()->avatar_url }}" class="sidebar-avatar" alt="{{ $initial }}"
            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
          <div class="sidebar-avatar-placeholder" style="display:none">{{ $initial }}</div>
        @else
          <div class="sidebar-avatar-placeholder">{{ $initial }}</div>
        @endif
        <div class="sidebar-user-info">
          <div class="sidebar-user-name">{{ auth()->user()->nama ?? 'Admin' }}</div>
          <div class="sidebar-user-role">{{ auth()->user()->roleLabel() }}</div>
        </div>
      </div>
      <a href="/" class="sidebar-action sidebar-action-public">
        <i class="fa fa-arrow-left" style="font-size:10px"></i> Ke Halaman Publik
      </a>
      <button onclick="doLogout()" class="sidebar-action sidebar-action-logout">
        <i class="fa fa-right-from-bracket" style="font-size:10px"></i> Keluar
      </button>
    </div>
  </aside>

  <div class="admin-body">
    <div class="admin-content">
      @yield('content')
    </div>
  </div>

</div>

<div class="toast" id="toast"><div class="toast-inner" id="toast-inner"></div></div>

<script>
window._csrfToken = '{{ csrf_token() }}';
const _nativeFetch = window.fetch;
window.fetch = (url, opts = {}) => {
  opts.headers = Object.assign({ 'Accept': 'application/json' }, opts.headers || {});
  return _nativeFetch(url, opts);
};
function doLogout() {
  fetch('/auth/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': window._csrfToken } })
    .then(() => location.href = '/');
}
function toggleSidebar() {
  const sidebar = document.getElementById('admin-sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  const toggle  = document.getElementById('sidebar-toggle');
  const open = sidebar.classList.toggle('open');
  overlay.classList.toggle('open', open);
  const spans = toggle.querySelectorAll('span');
  spans[0].style.transform = open ? 'rotate(45deg) translate(5px,5px)' : '';
  spans[1].style.opacity   = open ? '0' : '1';
  spans[2].style.transform = open ? 'rotate(-45deg) translate(5px,-5px)' : '';
}
function showToast(msg, dur = 3000) {
  const inner = document.getElementById('toast-inner');
  inner.textContent = msg; inner.classList.add('show');
  setTimeout(() => inner.classList.remove('show'), dur);
}
</script>
@yield('scripts')
</body>
</html>
