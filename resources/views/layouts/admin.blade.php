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
  --sidebar-w:232px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{font-family:'DM Sans',sans-serif;background:var(--warm);color:var(--ink);overflow-x:hidden}
a{text-decoration:none;color:inherit}

/* ── Keyframes ── */
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes popIn{
  from{opacity:0;transform:translateY(6px) scale(0.97)}
  to{opacity:1;transform:translateY(0) scale(1)}
}

/* ── Shell ── */
.admin-shell{display:flex;min-height:100vh}

/* ── Sidebar ── */
.admin-sidebar{
  width:var(--sidebar-w);flex-shrink:0;
  position:fixed;top:0;left:0;bottom:0;
  background:linear-gradient(180deg,#0C1E14 0%,#122419 50%,#1A3D2B 100%);
  display:flex;flex-direction:column;
  z-index:200;overflow:hidden;
  transition:transform 0.26s cubic-bezier(0.4,0,0.2,1);
}

/* ── Brand header ── */
.sidebar-brand{
  display:flex;align-items:center;gap:10px;
  padding:1.125rem 1rem 1rem;
  border-bottom:1px solid rgba(200,160,48,0.1);
  flex-shrink:0;
}
.sidebar-brand-mark{
  width:34px;height:34px;flex-shrink:0;border-radius:10px;
  background:rgba(200,160,48,0.1);
  border:1px solid rgba(200,160,48,0.18);
  display:flex;align-items:center;justify-content:center;
}
.sidebar-brand-mark svg{width:16px;height:16px}
.sidebar-brand-text{line-height:1.2}
.sidebar-brand-name{
  font-family:'DM Serif Display',serif;font-size:14px;color:#fff;
  display:block;
}
.sidebar-brand-sub{
  font-size:8.5px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;
  color:rgba(200,160,48,0.5);margin-top:2px;display:block;
}

/* ── Nav scroll area ── */
.sidebar-nav{flex:1;overflow-y:auto;overflow-x:hidden;padding:0.5rem 0 0.5rem}
.sidebar-nav::-webkit-scrollbar{width:2px}
.sidebar-nav::-webkit-scrollbar-thumb{background:rgba(200,160,48,0.15);border-radius:2px}

/* ── Direct link (Dashboard) ── */
.nav-direct{
  display:flex;align-items:center;gap:10px;
  padding:9px 1rem;
  font-size:13px;font-weight:500;
  color:rgba(255,255,255,0.5);
  transition:color 0.15s,background 0.15s;
  position:relative;
}
.nav-direct svg{width:15px;height:15px;flex-shrink:0;opacity:0.65;transition:opacity 0.15s}
.nav-direct:hover{color:rgba(255,255,255,0.85);background:rgba(255,255,255,0.04)}
.nav-direct:hover svg{opacity:1}
.nav-direct.active{color:#fff;background:rgba(200,160,48,0.11)}
.nav-direct.active::before{
  content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);
  width:2.5px;height:18px;background:var(--gold);border-radius:0 2px 2px 0;
}
.nav-direct.active svg{opacity:1;filter:drop-shadow(0 0 4px rgba(200,160,48,0.4))}

/* ── Accordion section ── */
.nav-section{margin:0}
.nav-section-header{
  display:flex;align-items:center;gap:9px;
  padding:8.5px 1rem;
  font-size:12px;font-weight:600;
  color:rgba(255,255,255,0.38);
  cursor:pointer;user-select:none;
  transition:color 0.15s,background 0.15s;
  position:relative;
}
.nav-section-header:hover{color:rgba(255,255,255,0.65);background:rgba(255,255,255,0.035)}
.nav-section-icon{
  width:28px;height:28px;border-radius:8px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  background:rgba(255,255,255,0.05);
  transition:background 0.2s;
}
.nav-section-icon svg{width:13px;height:13px;opacity:0.6;transition:opacity 0.2s}
.nav-section-label{flex:1;letter-spacing:0.01em}
.nav-section-chevron{
  width:14px;height:14px;flex-shrink:0;opacity:0.35;
  transition:transform 0.22s cubic-bezier(0.4,0,0.2,1),opacity 0.2s;
}

/* open state */
.nav-section.open > .nav-section-header{color:rgba(255,255,255,0.75)}
.nav-section.open > .nav-section-header .nav-section-icon{
  background:rgba(200,160,48,0.14);
}
.nav-section.open > .nav-section-header .nav-section-icon svg{opacity:1;color:var(--gold)}
.nav-section.open > .nav-section-header .nav-section-chevron{
  transform:rotate(90deg);opacity:0.6;
}

/* ── Section links ── */
.nav-section-body{
  max-height:0;overflow:hidden;
  transition:max-height 0.28s cubic-bezier(0.4,0,0.2,1);
}
.nav-section.open > .nav-section-body{max-height:600px}

.nav-link{
  display:flex;align-items:center;gap:9px;
  padding:7px 1rem 7px 1.625rem;
  font-size:12.5px;font-weight:500;
  color:rgba(255,255,255,0.42);
  transition:color 0.14s,background 0.14s,padding-left 0.14s;
  position:relative;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.nav-link i{width:13px;text-align:center;font-size:11px;flex-shrink:0;opacity:0.55;transition:opacity 0.14s,color 0.14s}
.nav-link:hover{color:rgba(255,255,255,0.8);background:rgba(255,255,255,0.04);padding-left:1.75rem}
.nav-link.active{color:#fff;background:rgba(200,160,48,0.10)}
.nav-link.active::before{
  content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);
  width:2.5px;height:16px;background:var(--gold);border-radius:0 2px 2px 0;
}
.nav-link.active i{color:var(--gold-light);opacity:1}

.nav-sub-label{
  font-size:8px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;
  color:rgba(200,160,48,0.35);
  padding:0.75rem 1rem 0.2rem 1.625rem;
  display:flex;align-items:center;gap:6px;
}
.nav-sub-label::after{content:'';flex:1;height:1px;background:rgba(200,160,48,0.08)}
.nav-divider{height:1px;background:rgba(255,255,255,0.05);margin:0.25rem 0.875rem}
.nav-section-gap{height:2px}

/* ── Footer ── */
.sidebar-footer{
  flex-shrink:0;
  border-top:1px solid rgba(255,255,255,0.06);
  padding:0.75rem 0.875rem 0.875rem;
}
.sidebar-user{
  display:flex;align-items:center;gap:9px;
  padding:4px 2px 8px;cursor:pointer;border-radius:9px;
  transition:background 0.15s;
}
.sidebar-user:hover{background:rgba(255,255,255,0.04)}
.sidebar-avatar-wrap{
  width:30px;height:30px;border-radius:50%;flex-shrink:0;
  border:1.5px solid rgba(200,160,48,0.35);overflow:hidden;
  transition:border-color 0.2s;
}
.sidebar-user:hover .sidebar-avatar-wrap{border-color:rgba(200,160,48,0.65)}
.sidebar-avatar{width:100%;height:100%;object-fit:cover;display:block}
.sidebar-avatar-ph{
  width:100%;height:100%;
  background:rgba(200,160,48,0.15);color:var(--gold);
  font-size:11px;font-weight:700;
  display:flex;align-items:center;justify-content:center;
}
.sidebar-user-info{min-width:0}
.sidebar-user-name{font-size:12px;font-weight:500;color:rgba(255,255,255,0.72);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.3}
.sidebar-user-role{
  display:inline-flex;align-items:center;
  font-size:8.5px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;
  color:rgba(200,160,48,0.55);margin-top:2px;
}
.sidebar-actions{display:flex;gap:6px;margin-top:2px}
.sidebar-action{
  flex:1;display:flex;align-items:center;justify-content:center;gap:6px;
  padding:7px 8px;border-radius:8px;
  font-size:11.5px;font-weight:500;font-family:'DM Sans',sans-serif;
  cursor:pointer;border:none;text-align:center;transition:all 0.15s;
}
.sidebar-action svg{width:11px;height:11px;flex-shrink:0}
.action-public{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.5)}
.action-public:hover{background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.85)}
.action-logout{background:rgba(181,64,26,0.13);color:rgba(255,110,80,0.75)}
.action-logout:hover{background:rgba(181,64,26,0.24);color:#ff7a5a}

/* ── Mobile topbar ── */
.admin-topbar{
  display:none;position:fixed;top:0;left:0;right:0;height:54px;
  background:#0C1E14;border-bottom:1px solid rgba(200,160,48,0.15);
  align-items:center;justify-content:space-between;
  padding:0 1rem;z-index:150;
}
.topbar-toggle{background:none;border:none;cursor:pointer;padding:6px;display:flex;flex-direction:column;gap:4px}
.topbar-toggle span{display:block;width:20px;height:2px;background:rgba(255,255,255,0.65);border-radius:2px;transition:all 0.22s}
.topbar-brand{font-family:'DM Serif Display',serif;font-size:15px;color:#fff}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:190;backdrop-filter:blur(2px)}

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

    <div class="sidebar-brand">
      <div class="sidebar-brand-mark">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M3 9.5L12 3L21 9.5V20C21 20.55 20.55 21 20 21H15V15H9V21H4C3.45 21 3 20.55 3 20V9.5Z" fill="var(--gold)"/>
        </svg>
      </div>
      <div class="sidebar-brand-text">
        <span class="sidebar-brand-name">GP2 RT10</span>
        <span class="sidebar-brand-sub">Admin Panel</span>
      </div>
    </div>

    @php $authRole = auth()->user()->role; @endphp
    <div class="sidebar-nav">

      {{-- Dashboard --}}
      <a href="/admin" class="nav-direct {{ request()->is('admin') && !request()->is('admin/*') ? 'active' : '' }}">
        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2 11l8-8 8 8v7a1 1 0 01-1 1h-4v-5H7v5H3a1 1 0 01-1-1v-7z"/></svg>
        Dashboard
      </a>

      {{-- Donasi & Kampanye --}}
      @if(in_array($authRole, ['bendahara','admin','super_admin']))
      <div class="nav-section-gap"></div>
      <div class="nav-section" id="sec-donasi">
        <div class="nav-section-header" onclick="toggleSection('donasi')">
          <div class="nav-section-icon">
            <svg viewBox="0 0 20 20" fill="currentColor" style="color:var(--gold)">
              <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
            </svg>
          </div>
          <span class="nav-section-label">Donasi &amp; Kampanye</span>
          <svg class="nav-section-chevron" viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 01.708 0l6 6a.5.5 0 010 .708l-6 6a.5.5 0 01-.708-.708L10.293 8 4.646 2.354a.5.5 0 010-.708z"/>
          </svg>
        </div>
        <div class="nav-section-body">
          <a href="/admin/verifikasi" class="nav-link {{ request()->is('admin/verifikasi') ? 'active' : '' }}">
            <i class="fa fa-circle-check"></i>Verifikasi Donasi
          </a>
          <a href="/admin/kampanye" class="nav-link {{ request()->is('admin/kampanye') ? 'active' : '' }}">
            <i class="fa fa-flag"></i>Kelola Kampanye
          </a>
        </div>
      </div>

      {{-- Keuangan --}}
      <div class="nav-section" id="sec-keuangan">
        <div class="nav-section-header" onclick="toggleSection('keuangan')">
          <div class="nav-section-icon">
            <svg viewBox="0 0 20 20" fill="currentColor" style="color:var(--gold)">
              <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM2 9v7a2 2 0 002 2h12a2 2 0 002-2V9H2zm5 3a1 1 0 011-1h4a1 1 0 010 2H8a1 1 0 01-1-1z"/>
            </svg>
          </div>
          <span class="nav-section-label">Keuangan</span>
          <svg class="nav-section-chevron" viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 01.708 0l6 6a.5.5 0 010 .708l-6 6a.5.5 0 01-.708-.708L10.293 8 4.646 2.354a.5.5 0 010-.708z"/>
          </svg>
        </div>
        <div class="nav-section-body">
          <a href="/admin/keuangan" class="nav-link {{ request()->is('admin/keuangan') && !request()->is('admin/keuangan/*') ? 'active' : '' }}">
            <i class="fa fa-chart-line"></i>Dashboard Keuangan
          </a>
          <a href="/admin/keuangan/kas" class="nav-link {{ request()->is('admin/keuangan/kas') ? 'active' : '' }}">
            <i class="fa fa-coins"></i>Kas
          </a>
          <a href="/admin/keuangan/transaksi-instan" class="nav-link {{ request()->is('admin/keuangan/transaksi-instan') ? 'active' : '' }}">
            <i class="fa fa-right-left"></i>Transaksi Instan
          </a>
          <a href="/admin/keuangan/iuran" class="nav-link {{ request()->is('admin/keuangan/iuran*') ? 'active' : '' }}">
            <i class="fa fa-calendar-check"></i>Iuran Bulanan
          </a>
          <a href="/admin/keuangan/pengeluaran" class="nav-link {{ request()->is('admin/keuangan/pengeluaran') ? 'active' : '' }}">
            <i class="fa fa-receipt"></i>Pengeluaran
          </a>
          <a href="/admin/keuangan/anggaran" class="nav-link {{ request()->is('admin/keuangan/anggaran') ? 'active' : '' }}">
            <i class="fa fa-chart-pie"></i>Anggaran
          </a>
          <a href="/admin/keuangan/kategori" class="nav-link {{ request()->is('admin/keuangan/kategori') ? 'active' : '' }}">
            <i class="fa fa-tags"></i>Kategori
          </a>
          <a href="/admin/keuangan/laporan" class="nav-link {{ request()->is('admin/keuangan/laporan') ? 'active' : '' }}">
            <i class="fa fa-file-lines"></i>Laporan &amp; Export
          </a>
          <a href="/admin/keuangan/gsheet" class="nav-link {{ request()->is('admin/keuangan/gsheet') ? 'active' : '' }}">
            <i class="fa fa-table"></i>Google Sheets
          </a>
        </div>
      </div>
      @endif

      {{-- Warga & Surat --}}
      @if(in_array($authRole, ['sekretaris','admin','super_admin']))
      <div class="nav-section-gap"></div>
      <div class="nav-section" id="sec-warga">
        <div class="nav-section-header" onclick="toggleSection('warga')">
          <div class="nav-section-icon">
            <svg viewBox="0 0 20 20" fill="currentColor" style="color:var(--gold)">
              <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 14.094A5.973 5.973 0 004 17v1H1v-1a3 3 0 013.75-2.906z"/>
            </svg>
          </div>
          <span class="nav-section-label">Warga &amp; Surat</span>
          <svg class="nav-section-chevron" viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 01.708 0l6 6a.5.5 0 010 .708l-6 6a.5.5 0 01-.708-.708L10.293 8 4.646 2.354a.5.5 0 010-.708z"/>
          </svg>
        </div>
        <div class="nav-section-body">
          <div class="nav-sub-label">Kependudukan</div>
          <a href="/admin/kependudukan" class="nav-link {{ request()->is('admin/kependudukan') && !request()->is('admin/kependudukan/*') ? 'active' : '' }}">
            <i class="fa fa-house-chimney"></i>Dashboard KK
          </a>
          <a href="/admin/kependudukan/warga" class="nav-link {{ request()->is('admin/kependudukan/warga*') ? 'active' : '' }}">
            <i class="fa fa-id-card"></i>Data Warga &amp; KK
          </a>
          <a href="/admin/kependudukan/kendaraan" class="nav-link {{ request()->is('admin/kependudukan/kendaraan*') ? 'active' : '' }}">
            <i class="fa fa-car"></i>Data Kendaraan
          </a>
          <a href="/admin/kependudukan/peta" class="nav-link {{ request()->is('admin/kependudukan/peta*') ? 'active' : '' }}">
            <i class="fa fa-map-location-dot"></i>Peta Warga
          </a>
          <div class="nav-sub-label">Komunikasi</div>
          <a href="/admin/pengumuman" class="nav-link {{ request()->is('admin/pengumuman') ? 'active' : '' }}">
            <i class="fa fa-bullhorn"></i>Pengumuman
          </a>
          <a href="/admin/surat" class="nav-link {{ request()->is('admin/surat*') ? 'active' : '' }}">
            <i class="fa fa-envelope-open-text"></i>Permohonan Surat
          </a>
        </div>
      </div>

      {{-- Kelembagaan --}}
      <div class="nav-section" id="sec-kelembagaan">
        <div class="nav-section-header" onclick="toggleSection('kelembagaan')">
          <div class="nav-section-icon">
            <svg viewBox="0 0 20 20" fill="currentColor" style="color:var(--gold)">
              <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
            </svg>
          </div>
          <span class="nav-section-label">Kelembagaan</span>
          <svg class="nav-section-chevron" viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 01.708 0l6 6a.5.5 0 010 .708l-6 6a.5.5 0 01-.708-.708L10.293 8 4.646 2.354a.5.5 0 010-.708z"/>
          </svg>
        </div>
        <div class="nav-section-body">
          <a href="/admin/kelembagaan/agenda" class="nav-link {{ request()->is('admin/kelembagaan/agenda*') ? 'active' : '' }}">
            <i class="fa fa-calendar-days"></i>Agenda Kegiatan
          </a>
          <a href="/admin/kelembagaan/galeri" class="nav-link {{ request()->is('admin/kelembagaan/galeri*') ? 'active' : '' }}">
            <i class="fa fa-images"></i>Galeri Foto
          </a>
          <a href="/admin/kelembagaan/berita" class="nav-link {{ request()->is('admin/kelembagaan/berita*') ? 'active' : '' }}">
            <i class="fa fa-newspaper"></i>Berita &amp; Info
          </a>
          <a href="/admin/kelembagaan/tata-tertib" class="nav-link {{ request()->is('admin/kelembagaan/tata-tertib*') ? 'active' : '' }}">
            <i class="fa fa-scale-balanced"></i>Tata Tertib
          </a>
          <a href="/admin/kelembagaan/program-kerja" class="nav-link {{ request()->is('admin/kelembagaan/program-kerja*') ? 'active' : '' }}">
            <i class="fa fa-list-check"></i>Program Kerja
          </a>
          <a href="/admin/kelembagaan/saran" class="nav-link {{ request()->is('admin/kelembagaan/saran*') ? 'active' : '' }}">
            <i class="fa fa-comments"></i>Saran &amp; Keluhan
          </a>
          <a href="/admin/kelembagaan/usulan" class="nav-link {{ request()->is('admin/kelembagaan/usulan*') ? 'active' : '' }}">
            <i class="fa fa-lightbulb"></i>Usulan Pembangunan
          </a>
          <a href="/admin/kelembagaan/bantuan-sosial" class="nav-link {{ request()->is('admin/kelembagaan/bantuan-sosial*') ? 'active' : '' }}">
            <i class="fa fa-hand-holding-heart"></i>Bantuan Sosial
          </a>
          <a href="/admin/kelembagaan/inventaris" class="nav-link {{ request()->is('admin/kelembagaan/inventaris*') ? 'active' : '' }}">
            <i class="fa fa-boxes-stacked"></i>Inventaris RT
          </a>
        </div>
      </div>
      @endif

      {{-- Sistem --}}
      @if(in_array($authRole, ['admin','super_admin']))
      <div class="nav-section-gap"></div>
      <div class="nav-section" id="sec-sistem">
        <div class="nav-section-header" onclick="toggleSection('sistem')">
          <div class="nav-section-icon">
            <svg viewBox="0 0 20 20" fill="currentColor" style="color:var(--gold)">
              <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
            </svg>
          </div>
          <span class="nav-section-label">Sistem</span>
          <svg class="nav-section-chevron" viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 01.708 0l6 6a.5.5 0 010 .708l-6 6a.5.5 0 01-.708-.708L10.293 8 4.646 2.354a.5.5 0 010-.708z"/>
          </svg>
        </div>
        <div class="nav-section-body">
          <a href="/admin/aktivitas" class="nav-link {{ request()->is('admin/aktivitas') ? 'active' : '' }}">
            <i class="fa fa-clock-rotate-left"></i>Log Aktivitas
          </a>
          <a href="/admin/pengaturan" class="nav-link {{ request()->is('admin/pengaturan') ? 'active' : '' }}">
            <i class="fa fa-gear"></i>Pengaturan
          </a>
          @if($authRole === 'super_admin')
          <a href="/admin/seeder" class="nav-link {{ request()->is('admin/seeder') ? 'active' : '' }}">
            <i class="fa fa-database"></i>Manajemen Seeder
          </a>
          @endif
        </div>
      </div>
      @endif

    </div>

    <div class="sidebar-footer">
      @php $initial = strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)); @endphp
      <div class="sidebar-user">
        <div class="sidebar-avatar-wrap">
          @if(auth()->user()->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}" class="sidebar-avatar" alt="{{ $initial }}"
              onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="sidebar-avatar-ph" style="display:none">{{ $initial }}</div>
          @else
            <div class="sidebar-avatar-ph">{{ $initial }}</div>
          @endif
        </div>
        <div class="sidebar-user-info">
          <div class="sidebar-user-name">{{ auth()->user()->nama ?? 'Admin' }}</div>
          <div class="sidebar-user-role">{{ auth()->user()->roleLabel() }}</div>
        </div>
      </div>
      <div class="sidebar-actions">
        <a href="/" class="sidebar-action action-public">
          <svg viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M14 8a.5.5 0 00-.5-.5H2.707l3.147-3.146a.5.5 0 10-.708-.708l-4 4a.5.5 0 000 .708l4 4a.5.5 0 00.708-.708L2.707 8.5H13.5A.5.5 0 0014 8z" clip-rule="evenodd"/></svg>
          Publik
        </a>
        <button onclick="doLogout()" class="sidebar-action action-logout">
          <svg viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 01-.5.5h-8a.5.5 0 01-.5-.5v-9a.5.5 0 01.5-.5h8a.5.5 0 01.5.5v2a.5.5 0 001 0v-2A1.5 1.5 0 0010 2h-8A1.5 1.5 0 000 3.5v9A1.5 1.5 0 001.5 14h8a1.5 1.5 0 001.5-1.5v-2a.5.5 0 00-1 0v2z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 000-.708l-3-3a.5.5 0 00-.708.708L14.293 7.5H5.5a.5.5 0 000 1h8.793l-2.147 2.146a.5.5 0 00.708.708l3-3z" clip-rule="evenodd"/></svg>
          Keluar
        </button>
      </div>
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

// ── Accordion ──
function toggleSection(name) {
  const el = document.getElementById('sec-' + name);
  if (!el) return;
  const isOpen = el.classList.contains('open');
  el.classList.toggle('open', !isOpen);
  try { sessionStorage.setItem('adminSection', !isOpen ? name : ''); } catch(e) {}
}

(function initSection() {
  const path = window.location.pathname;
  const map = [
    ['/admin/verifikasi',   'donasi'],
    ['/admin/kampanye',     'donasi'],
    ['/admin/keuangan',     'keuangan'],
    ['/admin/kependudukan', 'warga'],
    ['/admin/warga',        'warga'],
    ['/admin/pengumuman',   'warga'],
    ['/admin/surat',        'warga'],
    ['/admin/kelembagaan',  'kelembagaan'],
    ['/admin/aktivitas',    'sistem'],
    ['/admin/pengaturan',   'sistem'],
    ['/admin/seeder',       'sistem'],
  ];
  const match = map.find(([prefix]) => path.startsWith(prefix));
  const name = match ? match[1] : (() => { try { return sessionStorage.getItem('adminSection'); } catch(e) { return null; } })();
  if (name) {
    const el = document.getElementById('sec-' + name);
    if (el) el.classList.add('open');
  }
})();
</script>
@yield('scripts')
</body>
</html>
