<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>(function(){var m=document.cookie.match(/(?:^|;\s*)gp2-theme=([^;]*)/);document.documentElement.setAttribute('data-theme',(m?m[1]:null)||'light')})()</script>
<title>@yield('title', 'RT 10 Golden Park 2') — GP2 RT10</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
:root {
  --cream:#F3EDD8;--warm:#FDFAF2;--parchment:#EDE5CC;
  --forest:#1A3D2B;--forest-mid:#2A5C40;--forest-light:#3D7A56;--forest-pale:#EBF3EE;
  --gold:#C8A030;--gold-dark:#9A7818;--gold-light:#DFBA56;--gold-pale:#FBF3DC;--gold-wash:#F5ECC5;
  --rust:#B5401A;--sand:#D4BF98;--ink:#1A1810;--ink-mid:#3D3828;--ink-soft:#6B6050;--ink-mute:#9D9080;
  --border:rgba(26,61,43,0.12);--border-gold:rgba(200,160,48,0.28);
  --radius:16px;--radius-sm:10px;--radius-xs:6px;
  --shadow-sm:0 2px 8px rgba(26,61,43,0.07);
  --shadow-md:0 8px 24px rgba(26,61,43,0.10);
  --shadow-lg:0 20px 48px rgba(26,61,43,0.13);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--warm);color:var(--ink);overflow-x:hidden}
a{text-decoration:none;color:inherit}
nav{
  position:fixed;top:1rem;
  left:50%;transform:translateX(-50%);
  z-index:100;
  display:flex;align-items:center;gap:0;
  padding:5px;
  background:rgba(253,250,242,0.97);
  backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);
  border:1px solid rgba(26,61,43,0.15);
  border-radius:100px;
  box-shadow:0 4px 24px rgba(26,61,43,0.09),0 1px 4px rgba(26,61,43,0.04);
  white-space:nowrap;
  transition:box-shadow 0.25s,border-color 0.25s;
}
nav.scrolled{box-shadow:0 8px 32px rgba(26,61,43,0.13),0 2px 6px rgba(26,61,43,0.06);border-color:rgba(26,61,43,0.22)}
.nav-logo{display:flex;align-items:center;gap:9px;padding:0 14px 0 10px}
.nav-logo-mark{width:32px;height:32px;flex-shrink:0;border-radius:8px;background:linear-gradient(145deg,var(--forest),var(--forest-mid));display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(26,61,43,0.28)}
.nav-logo-mark svg{width:17px;height:17px;fill:var(--gold)}
.nav-brand{font-family:'DM Serif Display',serif;font-size:14px;color:var(--forest);letter-spacing:-0.01em;line-height:1.2}
.nav-brand span{display:block;font-family:'DM Sans',sans-serif;font-size:10px;font-weight:500;color:var(--ink-mute);letter-spacing:0.03em;margin-top:1px}
.nav-divider{width:1px;height:20px;background:rgba(26,61,43,0.12);flex-shrink:0;margin:0 4px}
.nav-links{display:flex;gap:1px;align-items:center}
.nav-links a{font-size:14px;font-weight:500;color:var(--ink-soft);padding:8px 16px;border-radius:100px;transition:color 0.18s,background 0.18s}
.nav-links a:hover{color:var(--forest);background:var(--forest-pale)}
.nav-links a.active{background:var(--forest);color:#fff;box-shadow:0 2px 8px rgba(26,61,43,0.22)}
.nav-right{display:flex;align-items:center;gap:6px;padding-left:4px}
.nav-cta{background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:#fff;font-size:13px;font-weight:500;padding:8px 20px;border-radius:100px;transition:all 0.2s;box-shadow:0 2px 8px rgba(26,61,43,0.22)}
.nav-cta:hover{box-shadow:0 4px 16px rgba(26,61,43,0.32)}
nav.bottom-nav{display:none;position:fixed;bottom:0;top:auto;left:0;right:0;transform:none;width:100%;border-radius:0;border:none;border-top:1px solid var(--border);box-shadow:0 -2px 16px rgba(26,61,43,0.06);padding:0 0 env(safe-area-inset-bottom) 0;justify-content:space-around;height:auto;z-index:200;white-space:nowrap}
.bn-item{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;padding:8px 4px;font-size:10px;font-weight:600;color:var(--ink-mute);cursor:pointer;transition:color 0.15s;border-radius:0;letter-spacing:0.01em}
.bn-item i{font-size:19px;line-height:1;transition:color 0.15s,transform 0.15s}
.bn-item:hover{color:var(--forest-mid)}
.bn-item.active{color:var(--forest)}
.bn-item.active i{transform:scale(1.08)}
.bn-logout{color:var(--rust)!important}
.bn-logout:hover{opacity:0.75}
button.bn-item{background:none;border:none;font-family:inherit;-webkit-appearance:none;appearance:none}
.nav-menu-btn{display:none;align-items:center;justify-content:center;width:36px;height:36px;border:none;background:none;cursor:pointer;color:var(--ink-mid);border-radius:8px;transition:background 0.15s;font-size:18px;flex-shrink:0}
.nav-menu-btn:hover{background:var(--forest-pale);color:var(--forest)}
.nav-drawer{position:fixed;top:0;right:0;bottom:0;width:280px;background:var(--warm);z-index:300;transform:translateX(100%);transition:transform 0.28s cubic-bezier(0.4,0,0.2,1);overflow-y:auto;display:flex;flex-direction:column;box-shadow:-8px 0 32px rgba(26,61,43,0.13)}
.nav-drawer.open{transform:translateX(0)}
.nav-drawer-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:299;backdrop-filter:blur(2px)}
.nav-drawer-overlay.open{display:block}
.nav-drawer-header{display:flex;align-items:center;justify-content:space-between;padding:1rem 1rem 0.875rem;border-bottom:1px solid var(--border)}
.nav-drawer-close{display:flex;align-items:center;justify-content:center;width:32px;height:32px;border:none;background:none;cursor:pointer;color:var(--ink-soft);font-size:17px;border-radius:8px;transition:background 0.15s;flex-shrink:0}
.nav-drawer-close:hover{background:var(--forest-pale);color:var(--forest)}
.nav-drawer-links{display:flex;flex-direction:column;padding:0.75rem;flex:1;gap:2px}
.nav-drawer-links a{display:flex;align-items:center;gap:10px;font-size:15px;font-weight:500;color:var(--ink-mid);padding:11px 12px;border-radius:10px;transition:background 0.15s,color 0.15s}
.nav-drawer-links a:hover{background:var(--forest-pale);color:var(--forest)}
.nav-drawer-links a.active{background:var(--forest);color:#fff}
.nav-drawer-links a i{width:16px;text-align:center;font-size:14px}
.nav-drawer-footer{padding:0.75rem 1rem 1.5rem}
.nav-drawer-user{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:var(--forest-pale);transition:background 0.15s}
.nav-drawer-user:hover{background:var(--parchment)}
.nav-drawer-user-name{font-size:13px;font-weight:600;color:var(--forest)}
.nav-drawer-user-sub{font-size:11px;color:var(--ink-mute);margin-top:1px}
.more-sheet{position:fixed;bottom:0;left:0;right:0;z-index:300;background:var(--warm);border-radius:20px 20px 0 0;padding:0 0 calc(env(safe-area-inset-bottom) + 0.5rem);transform:translateY(100%);transition:transform 0.28s cubic-bezier(0.4,0,0.2,1);box-shadow:0 -8px 32px rgba(26,61,43,0.13)}
.more-sheet.open{transform:translateY(0)}
.more-sheet-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.35);z-index:299;backdrop-filter:blur(2px)}
.more-sheet-overlay.open{display:block}
.more-sheet-handle{width:40px;height:4px;background:var(--sand);border-radius:2px;margin:12px auto 8px}
.more-sheet-title{font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--ink-mute);padding:4px 1.25rem 0.625rem}
.more-sheet-links{display:flex;flex-direction:column;padding:0 0.75rem;gap:2px}
.more-sheet-links a,.more-sheet-links button{display:flex;align-items:center;gap:12px;font-size:15px;font-weight:500;color:var(--ink-mid);padding:13px 12px;border-radius:10px;transition:background 0.15s,color 0.15s;background:none;border:none;cursor:pointer;font-family:inherit;width:100%;text-align:left}
.more-sheet-links a:hover,.more-sheet-links button:hover{background:var(--forest-pale);color:var(--forest)}
.more-sheet-links a.active{color:var(--forest);font-weight:600;background:var(--forest-pale)}
.more-sheet-links a i,.more-sheet-links button i{width:20px;text-align:center;font-size:15px;color:var(--ink-mute);flex-shrink:0}
.more-sheet-links a.active i{color:var(--forest)}
.more-sheet-links .ms-logout{color:var(--rust)}
.more-sheet-links .ms-logout i{color:var(--rust)}
.more-sheet-links .ms-logout:hover{background:#FDECEA;color:var(--rust)}
.more-sheet-divider{height:1px;background:var(--border);margin:0.25rem 0.75rem}
.nav-user{display:flex;align-items:center;gap:8px}
.nav-avatar{width:30px;height:30px;border-radius:50%;object-fit:cover;border:2px solid var(--gold-light)}
.nav-avatar-placeholder{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:var(--gold);font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center}
.member-nav{position:fixed;top:4.25rem;left:50%;transform:translateX(-50%);z-index:99;display:flex;align-items:center;gap:1px;padding:3px;background:rgba(245,236,197,0.97);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(200,160,48,0.45);border-radius:100px;box-shadow:0 4px 16px rgba(154,120,24,0.14);white-space:nowrap}
.member-nav a{font-size:13px;font-weight:500;color:var(--ink-mid);padding:7px 15px;border-radius:100px;transition:color 0.18s,background 0.18s;display:flex;align-items:center;gap:6px}
.member-nav a i{font-size:12px}
.member-nav a:hover{color:var(--gold-dark);background:rgba(200,160,48,0.14)}
.member-nav a.active{background:var(--gold-dark);color:#fff;box-shadow:0 2px 8px rgba(154,120,24,0.28)}
.member-nav .mn-logout{color:var(--rust)}
.member-nav .mn-logout:hover{background:rgba(181,64,26,0.1);color:var(--rust)}
.mn-divider{width:1px;height:16px;background:rgba(200,160,48,0.35);margin:0 2px;flex-shrink:0}
main{padding-top:5rem;min-height:100vh}
body.has-member main{padding-top:7.5rem!important}
.container{max-width:1100px;margin:0 auto;padding:0 2rem}
.section-label{display:inline-flex;align-items:center;gap:8px;font-size:10.5px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:var(--forest-light);margin-bottom:0.875rem}
.section-label::before{content:'';width:20px;height:2.5px;background:var(--gold);border-radius:2px}
.section-title{font-family:'DM Serif Display',serif;font-size:clamp(1.8rem,3vw,2.4rem);color:var(--forest);line-height:1.2;letter-spacing:-0.02em}
.btn-primary{background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:#fff;padding:12px 26px;border-radius:100px;font-size:14px;font-weight:500;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s;font-family:'DM Sans',sans-serif;box-shadow:0 4px 14px rgba(26,61,43,0.24)}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 22px rgba(26,61,43,0.32)}
.btn-secondary{background:transparent;color:var(--forest);padding:12px 26px;border-radius:100px;font-size:14px;font-weight:500;border:1.5px solid rgba(26,61,43,0.28);cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s;font-family:'DM Sans',sans-serif}
.btn-secondary:hover{background:var(--forest-pale);border-color:var(--forest)}
.btn-gold{background:linear-gradient(135deg,var(--gold-dark),var(--gold));color:#fff;padding:12px 26px;border-radius:100px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s;font-family:'DM Sans',sans-serif;box-shadow:0 4px 14px rgba(200,160,48,0.32)}
.btn-gold:hover{transform:translateY(-2px);box-shadow:0 7px 22px rgba(200,160,48,0.42)}
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
.gp2-dot-pattern{background-image:radial-gradient(var(--border) 1px,transparent 1px);background-size:24px 24px}
footer{background:linear-gradient(160deg,#122A1E 0%,#1A3D2B 100%);color:rgba(255,255,255,0.5);padding:3rem 0 2rem;margin-top:5rem;position:relative;overflow:hidden}
footer::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--gold),var(--gold-light),var(--gold),transparent)}
footer::after{content:'';position:absolute;bottom:0;right:-60px;width:280px;height:280px;border-radius:50%;border:1px solid rgba(200,160,48,0.08);pointer-events:none}
.footer-inner{display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:3rem;padding-bottom:2.5rem;border-bottom:1px solid rgba(255,255,255,0.08)}
.footer-brand{font-family:'DM Serif Display',serif;font-size:1.2rem;color:#fff;margin-bottom:0.625rem;line-height:1.25}
.footer-brand-mark{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:rgba(200,160,48,0.18);margin-bottom:0.875rem}
.footer-brand-mark svg{width:17px;height:17px;fill:var(--gold)}
.footer-brand-sub{font-size:12px;color:rgba(255,255,255,0.35);line-height:1.7;max-width:220px}
.footer-col-title{font-size:10px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:var(--gold-light);margin-bottom:1rem}
.footer-links-col{display:flex;flex-direction:column;gap:0.6rem}
.footer-links-col a{font-size:13px;color:rgba(255,255,255,0.42);transition:color 0.18s}
.footer-links-col a:hover{color:#fff}
.footer-bottom{display:flex;justify-content:space-between;align-items:center;padding-top:1.75rem;font-size:11.5px;color:rgba(255,255,255,0.3)}
.footer-bottom a{color:rgba(255,255,255,0.4);transition:color 0.18s}
.footer-bottom a:hover{color:#fff}
.toast{position:fixed;bottom:32px;left:50%;transform:translateX(-50%) translateY(20px);background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:#fff;padding:14px 28px;border-radius:100px;font-size:14px;font-weight:500;box-shadow:var(--shadow-lg);opacity:0;transition:all 0.3s;pointer-events:none;white-space:nowrap;z-index:999}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
.fade-in{opacity:0;transform:translateY(22px);transition:opacity 0.55s ease,transform 0.55s ease}
.fade-in.visible{opacity:1;transform:translateY(0)}
@media(max-width:768px){
  #main-nav{top:0;left:0;right:0;transform:none;width:100%;border-radius:0;padding:0 1rem;height:60px;border:none;border-bottom:1px solid var(--border);box-shadow:none;justify-content:space-between;gap:0}
  .nav-logo{padding:0}
  .nav-divider{display:none}
  .nav-links{display:none}
  .nav-right .nav-user,.nav-right .nav-cta{display:none!important}
  .nav-right{padding:0}
  .nav-menu-btn{display:flex}
  main{padding-top:72px;padding-bottom:0}
  body.has-member main{padding-top:72px!important;padding-bottom:calc(56px + env(safe-area-inset-bottom))!important}
  .member-nav{display:none}
  nav.bottom-nav{display:flex}
  .toast{bottom:calc(56px + env(safe-area-inset-bottom) + 12px)}
  .container{padding:0 1rem}
  .footer-inner{grid-template-columns:1fr;gap:2rem;padding-bottom:2rem}
  .footer-bottom{flex-direction:column;gap:0.75rem;text-align:center}
}
/* ── Theme toggle button ── */
.nav-theme-btn{width:34px;height:34px;border:none;background:none;cursor:pointer;color:var(--ink-soft);border-radius:8px;transition:background 0.15s,color 0.15s;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.nav-theme-btn:hover{background:var(--forest-pale);color:var(--forest)}
/* ── Dark mode variables ── */
html[data-theme="dark"]{
  --warm:#111714;--cream:#192019;--parchment:#1F2B22;
  --forest:#4A8C65;--forest-mid:#5A9E72;--forest-light:#72B888;
  --forest-pale:#1E3228;
  --gold-pale:#201C0E;--gold-wash:#1A160A;
  --sand:#3A3028;
  --ink:#E5DDD0;--ink-mid:#BDB49E;--ink-soft:#7E7668;--ink-mute:#5A5248;
  --border:rgba(255,255,255,0.09);--border-gold:rgba(200,160,48,0.20);
  --shadow-sm:0 2px 8px rgba(0,0,0,0.32);
  --shadow-md:0 8px 24px rgba(0,0,0,0.42);
  --shadow-lg:0 20px 48px rgba(0,0,0,0.52);
}
html[data-theme="dark"] #main-nav{background:rgba(13,19,15,0.97);border-color:rgba(255,255,255,0.08)}
html[data-theme="dark"] #main-nav.scrolled{box-shadow:0 8px 32px rgba(0,0,0,0.32),0 2px 6px rgba(0,0,0,0.18);border-color:rgba(255,255,255,0.12)}
html[data-theme="dark"] nav.bottom-nav{background:rgba(13,19,15,0.98);border-top-color:rgba(255,255,255,0.08)}
html[data-theme="dark"] .member-nav{background:rgba(20,18,6,0.97);border-color:rgba(200,160,48,0.25)}
html[data-theme="dark"] .card{background:#1F2B22;border-color:rgba(255,255,255,0.09)}
html[data-theme="dark"] .gp2-chip{background:#1F2B22}
html[data-theme="dark"] .btn-secondary{border-color:rgba(255,255,255,0.16);color:var(--ink)}
html[data-theme="dark"] .badge-done{background:#252A24;color:var(--ink-mute)}
html[data-theme="dark"] .badge-urgent{background:#2A1512}
html[data-theme="dark"] .badge-open{background:#1E3228}
html[data-theme="dark"] .badge-nearly{background:#201C0E}
html[data-theme="dark"] .nav-theme-btn:hover{background:rgba(74,140,101,0.2);color:#7FC8A0}
</style>
@yield('styles')
</head>
<body class="@auth has-member @endauth">

<nav id="main-nav">
  <a href="/" class="nav-logo">
    <div class="nav-logo-mark">
      <svg viewBox="0 0 24 24"><path d="M12 2L2 8v14h7v-7h6v7h7V8L12 2z"/></svg>
    </div>
    <div>
      <div class="nav-brand">GP2 RT10<span>Golden Park 2 · Cisauk</span></div>
    </div>
  </a>

  <div class="nav-divider"></div>

  <div class="nav-links">
    <a href="/kampanye"  class="{{ request()->is('kampanye*') ? 'active' : '' }}">Kampanye</a>
    <a href="/donasi"    class="{{ request()->is('donasi') ? 'active' : '' }}">Donasi</a>
    <a href="/agenda"    class="{{ request()->is('agenda') ? 'active' : '' }}">Agenda</a>
    <a href="/galeri"    class="{{ request()->is('galeri') ? 'active' : '' }}">Galeri</a>
    <a href="/informasi" class="{{ request()->is('informasi*') ? 'active' : '' }}">Informasi</a>
  </div>

  <div class="nav-divider"></div>

  <div class="nav-right">
    @auth
    <div class="nav-user" style="display:none" id="nav-user-desktop">
      @php $initial = strtoupper(substr(auth()->user()->nama ?? 'W', 0, 1)); @endphp
      @if(auth()->user()->avatar_url)
        <img src="{{ auth()->user()->avatar_url }}" class="nav-avatar" alt="{{ $initial }}"
          onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="nav-avatar-placeholder" style="display:none">{{ $initial }}</div>
      @else
        <div class="nav-avatar-placeholder">{{ $initial }}</div>
      @endif
      <span style="font-size:13px;font-weight:500;color:var(--ink-mid)">{{ auth()->user()->nama ?? 'Warga' }}</span>
    </div>
    @else
    <a href="/login" class="nav-cta" id="nav-cta-desktop">Masuk →</a>
    @endauth
    <button class="nav-theme-btn" id="theme-toggle" onclick="toggleTheme()" aria-label="Toggle tema">
      <i id="theme-icon" class="fa fa-moon"></i>
    </button>
    <button class="nav-menu-btn" id="nav-menu-btn" onclick="toggleNavDrawer()" aria-label="Menu">
      <i class="fa fa-bars"></i>
    </button>
  </div>
</nav>

<div class="nav-drawer" id="nav-drawer">
  <div class="nav-drawer-header">
    <div class="nav-logo">
      <div class="nav-logo-mark">
        <svg viewBox="0 0 24 24"><path d="M12 2L2 8v14h7v-7h6v7h7V8L12 2z"/></svg>
      </div>
      <div>
        <div class="nav-brand">GP2 RT10<span>Golden Park 2 · Cisauk</span></div>
      </div>
    </div>
    <button class="nav-drawer-close" onclick="toggleNavDrawer()" aria-label="Tutup">
      <i class="fa fa-xmark"></i>
    </button>
  </div>
  <div class="nav-drawer-links">
    <a href="/" class="{{ !request()->path() ? 'active' : '' }}">
      <i class="fa fa-house"></i> Beranda
    </a>
    <a href="/kampanye" class="{{ request()->is('kampanye*') ? 'active' : '' }}">
      <i class="fa fa-flag"></i> Kampanye
    </a>
    <a href="/donasi" class="{{ request()->is('donasi') ? 'active' : '' }}">
      <i class="fa fa-heart"></i> Donasi
    </a>
    <a href="/agenda" class="{{ request()->is('agenda') ? 'active' : '' }}">
      <i class="fa fa-calendar-days"></i> Agenda
    </a>
    <a href="/galeri" class="{{ request()->is('galeri') ? 'active' : '' }}">
      <i class="fa fa-images"></i> Galeri
    </a>
    <a href="/informasi" class="{{ request()->is('informasi*') ? 'active' : '' }}">
      <i class="fa fa-circle-info"></i> Informasi
    </a>
  </div>
  <div class="nav-drawer-footer">
    @guest
    <a href="/login" class="nav-cta" style="display:block;text-align:center">Masuk →</a>
    @endguest
    @auth
    @php $drawerInitial = strtoupper(substr(auth()->user()->nama ?? 'W', 0, 1)); @endphp
    <a href="/dashboard" class="nav-drawer-user">
      @if(auth()->user()->avatar_url)
        <img src="{{ auth()->user()->avatar_url }}" class="nav-avatar" alt="{{ $drawerInitial }}"
          onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="nav-avatar-placeholder" style="display:none">{{ $drawerInitial }}</div>
      @else
        <div class="nav-avatar-placeholder">{{ $drawerInitial }}</div>
      @endif
      <div>
        <div class="nav-drawer-user-name">{{ auth()->user()->nama ?? 'Warga' }}</div>
        <div class="nav-drawer-user-sub">Buka Dashboard</div>
      </div>
    </a>
    @endauth
  </div>
</div>
<div class="nav-drawer-overlay" id="nav-drawer-overlay" onclick="toggleNavDrawer()"></div>

@auth
<nav class="member-nav">
  <a href="/dashboard" class="{{ request()->is('dashboard') && !request()->is('dashboard/*') ? 'active' : '' }}">
    <i class="fa fa-gauge"></i> Dashboard
  </a>
  @if(auth()->user()->isAdmin())
  <a href="/dashboard/iuran" class="{{ request()->is('dashboard/iuran') ? 'active' : '' }}">
    <i class="fa fa-wallet"></i> Iuran
  </a>
  @endif
  <a href="/dashboard/riwayat" class="{{ request()->is('dashboard/riwayat') ? 'active' : '' }}">
    <i class="fa fa-clock-rotate-left"></i> Riwayat
  </a>
  <a href="/laporan" class="{{ request()->is('laporan') ? 'active' : '' }}">
    <i class="fa fa-chart-bar"></i> Laporan
  </a>
  <a href="/dashboard/profil" class="{{ request()->is('dashboard/profil') ? 'active' : '' }}">
    <i class="fa fa-user"></i> Profil
  </a>
  @if(auth()->user()->isAdmin())
  <a href="/admin" class="{{ request()->is('admin*') ? 'active' : '' }}">
    <i class="fa fa-lock"></i>
    @php
    $roleLabel = match(auth()->user()->role) {
        'super_admin' => 'Super Admin',
        'bendahara'   => 'Bendahara',
        'sekretaris'  => 'Sekretaris',
        default       => 'Admin',
    };
    @endphp
    {{ $roleLabel }}
  </a>
  @endif
  <div class="mn-divider"></div>
  <a href="#" class="mn-logout" onclick="doLogout()">
    <i class="fa fa-right-from-bracket"></i> Keluar
  </a>
</nav>
@endauth

@auth
<nav class="bottom-nav" id="bottom-nav">
  @if(auth()->user()->isAdmin())
  @php $moreActive = request()->is('dashboard/iuran') || request()->is('dashboard/riwayat') || request()->is('laporan'); @endphp
  <a href="/dashboard" class="bn-item {{ request()->is('dashboard') && !request()->is('dashboard/*') ? 'active' : '' }}">
    <i class="fa fa-gauge"></i><span>Dashboard</span>
  </a>
  <a href="/admin" class="bn-item {{ request()->is('admin*') ? 'active' : '' }}">
    <i class="fa fa-lock"></i><span>Admin</span>
  </a>
  <a href="/dashboard/profil" class="bn-item {{ request()->is('dashboard/profil') ? 'active' : '' }}">
    <i class="fa fa-user"></i><span>Profil</span>
  </a>
  <button class="bn-item {{ $moreActive ? 'active' : '' }}" onclick="toggleMoreSheet()">
    <i class="fa fa-ellipsis"></i><span>Lainnya</span>
  </button>
  @else
  @php $moreActive = request()->is('dashboard/riwayat') || request()->is('laporan'); @endphp
  <a href="/dashboard" class="bn-item {{ request()->is('dashboard') && !request()->is('dashboard/*') ? 'active' : '' }}">
    <i class="fa fa-gauge"></i><span>Dashboard</span>
  </a>
  <a href="/donasi" class="bn-item {{ request()->is('donasi') ? 'active' : '' }}">
    <i class="fa fa-heart"></i><span>Donasi</span>
  </a>
  <a href="/dashboard/profil" class="bn-item {{ request()->is('dashboard/profil') ? 'active' : '' }}">
    <i class="fa fa-user"></i><span>Profil</span>
  </a>
  <button class="bn-item {{ $moreActive ? 'active' : '' }}" onclick="toggleMoreSheet()">
    <i class="fa fa-ellipsis"></i><span>Lainnya</span>
  </button>
  @endif
</nav>
@endauth

@auth
<div class="more-sheet" id="more-sheet">
  <div class="more-sheet-handle"></div>
  <div class="more-sheet-title">Menu Lainnya</div>
  <div class="more-sheet-links">
    @if(auth()->user()->isAdmin())
    <a href="/dashboard/iuran" class="{{ request()->is('dashboard/iuran') ? 'active' : '' }}">
      <i class="fa fa-wallet"></i> Iuran Bulanan
    </a>
    @endif
    <a href="/dashboard/riwayat" class="{{ request()->is('dashboard/riwayat') ? 'active' : '' }}">
      <i class="fa fa-clock-rotate-left"></i> Riwayat Donasi
    </a>
    <a href="/laporan" class="{{ request()->is('laporan') ? 'active' : '' }}">
      <i class="fa fa-chart-bar"></i> Laporan Keuangan
    </a>
    <div class="more-sheet-divider"></div>
    <button class="ms-logout" onclick="doLogout();closeMoreSheet()">
      <i class="fa fa-right-from-bracket"></i> Keluar
    </button>
  </div>
</div>
<div class="more-sheet-overlay" id="more-sheet-overlay" onclick="closeMoreSheet()"></div>
@endauth

@yield('content')

<footer>
  <div class="container">
    <div class="footer-inner">
      <div>
        <div class="footer-brand-mark">
          <svg viewBox="0 0 24 24"><path d="M12 2L2 8v14h7v-7h6v7h7V8L12 2z"/></svg>
        </div>
        <div class="footer-brand">RT 10<br>Golden Park 2</div>
        <div class="footer-brand-sub">Portal digital warga RT 10, Perumahan Golden Park 2, Cisauk, Tangerang Selatan, Banten.</div>
      </div>
      <div>
        <div class="footer-col-title">Navigasi</div>
        <div class="footer-links-col">
          <a href="/">Beranda</a>
          <a href="/kampanye">Kampanye</a>
          <a href="/donasi">Donasi</a>
          <a href="/agenda">Agenda</a>
          <a href="/galeri">Galeri</a>
          <a href="/informasi">Informasi RT</a>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Informasi</div>
        <div class="footer-links-col">
          <span style="font-size:13px;color:rgba(255,255,255,0.42);line-height:1.6">
            <i class="fa fa-location-dot" style="width:14px;color:var(--gold);margin-right:4px"></i>
            Jl. Golden Park 2, Cisauk, Tangerang Selatan, Banten
          </span>
          <a href="/donatur" style="display:inline-flex;align-items:center;gap:6px;margin-top:0.25rem">
            <i class="fa fa-users" style="width:14px;color:var(--gold)"></i> Papan Donatur
          </a>
          <a href="/login" style="display:inline-flex;align-items:center;gap:6px;margin-top:0.25rem">
            <i class="fa fa-right-to-bracket" style="width:14px;color:var(--gold)"></i> Login Warga
          </a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div>© {{ date('Y') }} RT 10 · Perumahan Golden Park 2</div>
      <div>Dibuat dengan <svg viewBox="0 0 20 18" fill="#d97070" style="width:13px;height:13px;display:inline;vertical-align:middle"><path d="M10 16.5l-1.4-1.3C3.4 10.2 0 7.2 0 3.5 0 1.6 1.6 0 3.5 0 4.6 0 5.7.5 6.5 1.3 7.3.5 8.4 0 9.5 0 10.5 0 11.6.5 12.4 1.3 13.2.5 14.4 0 15.5 0 17.4 0 19 1.6 19 3.5c0 3.7-3.4 6.7-8.6 11.7L10 16.5z"/></svg> untuk warga</div>
    </div>
  </div>
</footer>

<div class="toast" id="toast"></div>

<script>
const _csrfToken = '{{ csrf_token() }}';
function toggleTheme() {
  const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  const exp = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toUTCString();
  document.cookie = 'gp2-theme=' + next + ';path=/;expires=' + exp + ';SameSite=Lax';
  const icon = document.getElementById('theme-icon');
  if (icon) icon.className = next === 'dark' ? 'fa fa-sun' : 'fa fa-moon';
}
// Ensure all fetch() calls include Accept: application/json so auth errors return JSON, not HTML redirects
const _nativeFetch = window.fetch;
window.fetch = (url, opts = {}) => {
  opts.headers = Object.assign({ 'Accept': 'application/json' }, opts.headers || {});
  return _nativeFetch(url, opts);
};
function doLogout() {
  fetch('/auth/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': _csrfToken } })
    .then(() => location.href = '/');
}
document.addEventListener('DOMContentLoaded', () => {
  const _themeIcon = document.getElementById('theme-icon');
  if (_themeIcon) _themeIcon.className = document.documentElement.getAttribute('data-theme') === 'dark' ? 'fa fa-sun' : 'fa fa-moon';
  function handleResize() {
    const isMobile = window.innerWidth <= 768;
    const desktopUser = document.getElementById('nav-user-desktop');
    const desktopCta  = document.getElementById('nav-cta-desktop');
    if (desktopUser) desktopUser.style.display = isMobile ? 'none' : 'flex';
    if (desktopCta)  desktopCta.style.display  = isMobile ? 'none' : 'inline-flex';
  }
  handleResize();
  window.addEventListener('resize', handleResize);
  const nav = document.getElementById('main-nav');
  if (nav) {
    const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 12);
    window.addEventListener('scroll', onScroll, { passive: true });
  }
});
function showToast(msg, dur = 3000) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), dur);
}
function toggleNavDrawer() {
  const drawer = document.getElementById('nav-drawer');
  const overlay = document.getElementById('nav-drawer-overlay');
  const isOpen = drawer.classList.toggle('open');
  overlay.classList.toggle('open', isOpen);
  document.body.style.overflow = isOpen ? 'hidden' : '';
}
function toggleMoreSheet() {
  const sheet = document.getElementById('more-sheet');
  const overlay = document.getElementById('more-sheet-overlay');
  const isOpen = sheet.classList.toggle('open');
  overlay.classList.toggle('open', isOpen);
  document.body.style.overflow = isOpen ? 'hidden' : '';
}
function closeMoreSheet() {
  document.getElementById('more-sheet').classList.remove('open');
  document.getElementById('more-sheet-overlay').classList.remove('open');
  document.body.style.overflow = '';
}
const observer = new IntersectionObserver(
  e => e.forEach(el => { if (el.isIntersecting) el.target.classList.add('visible') }),
  { threshold: 0.08, rootMargin: '0px 0px -32px 0px' }
);
document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
</script>
@yield('scripts')
</body>
</html>
