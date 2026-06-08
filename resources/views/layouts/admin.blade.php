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
  --sidebar-w:240px;
  --sb-bg:#111C16;--sb-active:rgba(200,160,48,0.12);--sb-hover:rgba(255,255,255,0.05);
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
  background:var(--sb-bg);
  display:flex;flex-direction:column;
  z-index:200;
  transition:transform 0.26s cubic-bezier(0.4,0,0.2,1);
  border-right:1px solid rgba(255,255,255,0.04);
}

/* ── Brand ── */
.sb-brand{
  display:flex;align-items:center;gap:10px;
  padding:1.25rem 1.125rem 1.125rem;
  border-bottom:1px solid rgba(255,255,255,0.06);
  flex-shrink:0;
}
.sb-brand-icon{
  width:32px;height:32px;border-radius:9px;flex-shrink:0;
  background:linear-gradient(135deg,rgba(200,160,48,0.2),rgba(200,160,48,0.08));
  border:1px solid rgba(200,160,48,0.22);
  display:flex;align-items:center;justify-content:center;
}
.sb-brand-icon svg{width:15px;height:15px;fill:var(--gold)}
.sb-brand-text{}
.sb-brand-name{font-family:'DM Serif Display',serif;font-size:14px;color:#fff;display:block;line-height:1.2}
.sb-brand-sub{font-size:8.5px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(200,160,48,0.45);margin-top:2px;display:block}

/* ── Nav ── */
.sb-nav{flex:1;overflow-y:auto;overflow-x:hidden;padding:0.625rem 0 1rem}
.sb-nav::-webkit-scrollbar{width:2px}
.sb-nav::-webkit-scrollbar-thumb{background:rgba(200,160,48,0.12);border-radius:2px}

/* Group label */
.sb-group{
  font-size:9px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;
  color:rgba(255,255,255,0.22);
  padding:1.125rem 1rem 0.3rem;
}

/* Nav links */
.sb-link{
  display:flex;align-items:center;gap:9px;
  padding:8px 1rem;
  font-size:13px;font-weight:500;
  color:rgba(255,255,255,0.48);
  transition:color 0.14s,background 0.14s;
  position:relative;cursor:pointer;
}
.sb-link .sb-icon{
  width:16px;text-align:center;flex-shrink:0;
  font-size:12px;opacity:0.55;
  transition:opacity 0.14s,color 0.14s;
  display:flex;align-items:center;justify-content:center;
}
.sb-link .sb-icon svg{width:14px;height:14px}
.sb-link:hover{color:rgba(255,255,255,0.82);background:var(--sb-hover)}
.sb-link:hover .sb-icon{opacity:0.85}
.sb-link.active{color:#fff;background:var(--sb-active);font-weight:600}
.sb-link.active::before{
  content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);
  width:2.5px;height:20px;
  background:linear-gradient(180deg,var(--gold-light),var(--gold-dark));
  border-radius:0 2px 2px 0;
}
.sb-link.active .sb-icon{opacity:1;color:var(--gold-light)}

/* Divider between groups */
.sb-divider{height:1px;background:rgba(255,255,255,0.055);margin:0.5rem 1rem}

/* ── Footer ── */
.sb-footer{
  flex-shrink:0;padding:0.875rem 1rem 1rem;
  border-top:1px solid rgba(255,255,255,0.06);
}
.sb-user{display:flex;align-items:center;gap:9px;margin-bottom:10px}
.sb-avatar{
  width:30px;height:30px;border-radius:50%;flex-shrink:0;
  border:1.5px solid rgba(200,160,48,0.32);overflow:hidden;
}
.sb-avatar img{width:100%;height:100%;object-fit:cover;display:block}
.sb-avatar-ph{
  width:100%;height:100%;
  background:rgba(200,160,48,0.14);color:var(--gold);
  font-size:11px;font-weight:700;
  display:flex;align-items:center;justify-content:center;
}
.sb-user-info{min-width:0}
.sb-user-name{font-size:12.5px;font-weight:500;color:rgba(255,255,255,0.72);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sb-user-role{font-size:9.5px;font-weight:600;color:rgba(200,160,48,0.55);letter-spacing:0.04em;text-transform:uppercase;margin-top:1px}
.sb-actions{display:flex;gap:6px}
.sb-action{
  flex:1;display:flex;align-items:center;justify-content:center;gap:6px;
  padding:7px 6px;border-radius:8px;
  font-size:11.5px;font-weight:500;font-family:'DM Sans',sans-serif;
  cursor:pointer;border:none;transition:all 0.15s;
}
.sb-action svg{width:11px;height:11px;flex-shrink:0}
.sb-action-pub{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.5)}
.sb-action-pub:hover{background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.85)}
.sb-action-out{background:rgba(181,64,26,0.13);color:rgba(255,110,80,0.72)}
.sb-action-out:hover{background:rgba(181,64,26,0.24);color:#ff7a5a}

/* ── Mobile topbar ── */
.admin-topbar{
  display:none;position:fixed;top:0;left:0;right:0;height:52px;
  background:var(--sb-bg);border-bottom:1px solid rgba(255,255,255,0.07);
  align-items:center;justify-content:space-between;
  padding:0 1rem;z-index:150;
}
.topbar-toggle{background:none;border:none;cursor:pointer;padding:6px;display:flex;flex-direction:column;gap:4.5px}
.topbar-toggle span{display:block;width:19px;height:1.5px;background:rgba(255,255,255,0.6);border-radius:2px;transition:all 0.22s}
.topbar-brand{font-family:'DM Serif Display',serif;font-size:15px;color:#fff}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:190}

/* ── Main content ── */
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
  .admin-body{margin-left:0;padding-top:52px}
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

    <div class="sb-brand">
      <div class="sb-brand-icon">
        <svg viewBox="0 0 20 20"><path d="M2 11l8-8 8 8v7a1 1 0 01-1 1h-4v-5H7v5H3a1 1 0 01-1-1v-7z"/></svg>
      </div>
      <div class="sb-brand-text">
        <span class="sb-brand-name">GP2 RT10</span>
        <span class="sb-brand-sub">Admin Panel</span>
      </div>
    </div>

    @php $authRole = auth()->user()->role; @endphp
    <nav class="sb-nav">

      <a href="/admin" class="sb-link {{ request()->is('admin') && !request()->is('admin/*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-gauge"></i></span>
        Dashboard
      </a>

      @if(in_array($authRole, ['bendahara','admin','super_admin']))

      <div class="sb-divider"></div>
      <div class="sb-group">Donasi</div>
      <a href="/admin/verifikasi" class="sb-link {{ request()->is('admin/verifikasi') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-circle-check"></i></span>
        Verifikasi Donasi
      </a>
      <a href="/admin/kampanye" class="sb-link {{ request()->is('admin/kampanye') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-flag"></i></span>
        Kelola Kampanye
      </a>

      <div class="sb-divider"></div>
      <div class="sb-group">Keuangan</div>
      <a href="/admin/keuangan" class="sb-link {{ request()->is('admin/keuangan') && !request()->is('admin/keuangan/*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-chart-line"></i></span>
        Dashboard Keuangan
      </a>
      <a href="/admin/keuangan/kas" class="sb-link {{ request()->is('admin/keuangan/kas') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-coins"></i></span>
        Kas
      </a>
      <a href="/admin/keuangan/transaksi-instan" class="sb-link {{ request()->is('admin/keuangan/transaksi-instan') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-right-left"></i></span>
        Transaksi Instan
      </a>
      <a href="/admin/keuangan/iuran" class="sb-link {{ request()->is('admin/keuangan/iuran*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-calendar-check"></i></span>
        Iuran Bulanan
      </a>
      <a href="/admin/keuangan/pengeluaran" class="sb-link {{ request()->is('admin/keuangan/pengeluaran') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-receipt"></i></span>
        Pengeluaran
      </a>
      <a href="/admin/keuangan/anggaran" class="sb-link {{ request()->is('admin/keuangan/anggaran') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-chart-pie"></i></span>
        Anggaran
      </a>
      <a href="/admin/keuangan/kategori" class="sb-link {{ request()->is('admin/keuangan/kategori') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-tags"></i></span>
        Kategori
      </a>
      <a href="/admin/keuangan/laporan" class="sb-link {{ request()->is('admin/keuangan/laporan') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-file-lines"></i></span>
        Laporan &amp; Export
      </a>
      <a href="/admin/keuangan/gsheet" class="sb-link {{ request()->is('admin/keuangan/gsheet') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-table"></i></span>
        Google Sheets
      </a>

      @endif

      @if(in_array($authRole, ['sekretaris','admin','super_admin']))

      <div class="sb-divider"></div>
      <div class="sb-group">Kependudukan</div>
      <a href="/admin/warga" class="sb-link {{ request()->is('admin/warga') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-users"></i></span>
        Akun Pengguna
      </a>
      <a href="/admin/kependudukan" class="sb-link {{ request()->is('admin/kependudukan') && !request()->is('admin/kependudukan/*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-house-chimney"></i></span>
        Dashboard KK
      </a>
      <a href="/admin/kependudukan/warga" class="sb-link {{ request()->is('admin/kependudukan/warga*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-id-card"></i></span>
        Kepala Keluarga
      </a>
      <a href="/admin/kependudukan/kendaraan" class="sb-link {{ request()->is('admin/kependudukan/kendaraan*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-car"></i></span>
        Data Kendaraan
      </a>
      <a href="/admin/kependudukan/peta" class="sb-link {{ request()->is('admin/kependudukan/peta*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-map-location-dot"></i></span>
        Peta Warga
      </a>
      <a href="/admin/kependudukan/laporan" class="sb-link {{ request()->is('admin/kependudukan/laporan*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-chart-pie"></i></span>
        Laporan Kependudukan
      </a>

      <div class="sb-divider"></div>
      <div class="sb-group">Komunikasi</div>
      <a href="/admin/pengumuman" class="sb-link {{ request()->is('admin/pengumuman') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-bullhorn"></i></span>
        Pengumuman
      </a>
      <a href="/admin/surat" class="sb-link {{ request()->is('admin/surat*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-envelope-open-text"></i></span>
        Permohonan Surat
      </a>

      <div class="sb-divider"></div>
      <div class="sb-group">Kelembagaan</div>
      <a href="/admin/kelembagaan/agenda" class="sb-link {{ request()->is('admin/kelembagaan/agenda*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-calendar-days"></i></span>
        Agenda Kegiatan
      </a>
      <a href="/admin/kelembagaan/galeri" class="sb-link {{ request()->is('admin/kelembagaan/galeri*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-images"></i></span>
        Galeri Foto
      </a>
      <a href="/admin/kelembagaan/berita" class="sb-link {{ request()->is('admin/kelembagaan/berita*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-newspaper"></i></span>
        Berita &amp; Info
      </a>
      <a href="/admin/kelembagaan/tata-tertib" class="sb-link {{ request()->is('admin/kelembagaan/tata-tertib*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-scale-balanced"></i></span>
        Tata Tertib
      </a>
      <a href="/admin/kelembagaan/program-kerja" class="sb-link {{ request()->is('admin/kelembagaan/program-kerja*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-list-check"></i></span>
        Program Kerja
      </a>
      <a href="/admin/kelembagaan/saran" class="sb-link {{ request()->is('admin/kelembagaan/saran*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-comments"></i></span>
        Saran &amp; Keluhan
      </a>
      <a href="/admin/kelembagaan/usulan" class="sb-link {{ request()->is('admin/kelembagaan/usulan*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-lightbulb"></i></span>
        Usulan Pembangunan
      </a>
      <a href="/admin/kelembagaan/bantuan-sosial" class="sb-link {{ request()->is('admin/kelembagaan/bantuan-sosial*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-hand-holding-heart"></i></span>
        Bantuan Sosial
      </a>
      <a href="/admin/kelembagaan/inventaris" class="sb-link {{ request()->is('admin/kelembagaan/inventaris*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-boxes-stacked"></i></span>
        Inventaris RT
      </a>

      @endif

      @if(in_array($authRole, ['admin','super_admin']))

      <div class="sb-divider"></div>
      <div class="sb-group">Sistem</div>
      <a href="/admin/aktivitas" class="sb-link {{ request()->is('admin/aktivitas') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-clock-rotate-left"></i></span>
        Log Aktivitas
      </a>
      <a href="/admin/pengaturan" class="sb-link {{ request()->is('admin/pengaturan') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-gear"></i></span>
        Pengaturan
      </a>
      @if($authRole === 'super_admin')
      <a href="/admin/seeder" class="sb-link {{ request()->is('admin/seeder') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fa fa-database"></i></span>
        Manajemen Seeder
      </a>
      @endif

      @endif

    </nav>

    <div class="sb-footer">
      @php $initial = strtoupper(substr(auth()->user()->nama ?? 'A', 0, 1)); @endphp
      <div class="sb-user">
        <div class="sb-avatar">
          @if(auth()->user()->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}" alt="{{ $initial }}"
              onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="sb-avatar-ph" style="display:none">{{ $initial }}</div>
          @else
            <div class="sb-avatar-ph">{{ $initial }}</div>
          @endif
        </div>
        <div class="sb-user-info">
          <div class="sb-user-name">{{ auth()->user()->nama ?? 'Admin' }}</div>
          <div class="sb-user-role">{{ auth()->user()->roleLabel() }}</div>
        </div>
      </div>
      <div class="sb-actions">
        <a href="/" class="sb-action sb-action-pub">
          <svg viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M14 8a.5.5 0 00-.5-.5H2.707l3.147-3.146a.5.5 0 10-.708-.708l-4 4a.5.5 0 000 .708l4 4a.5.5 0 00.708-.708L2.707 8.5H13.5A.5.5 0 0014 8z"/></svg>
          Halaman Publik
        </a>
        <button onclick="doLogout()" class="sb-action sb-action-out">
          <svg viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 01-.5.5h-8a.5.5 0 01-.5-.5v-9a.5.5 0 01.5-.5h8a.5.5 0 01.5.5v2a.5.5 0 001 0v-2A1.5 1.5 0 0010 2h-8A1.5 1.5 0 000 3.5v9A1.5 1.5 0 001.5 14h8a1.5 1.5 0 001.5-1.5v-2a.5.5 0 00-1 0v2z"/><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 000-.708l-3-3a.5.5 0 00-.708.708L14.293 7.5H5.5a.5.5 0 000 1h8.793l-2.147 2.146a.5.5 0 00.708.708l3-3z"/></svg>
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
</script>
@yield('scripts')
</body>
</html>
