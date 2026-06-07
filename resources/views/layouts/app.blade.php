<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Kas RT 10 Golden Park 2') — Kas RT 10</title>
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
.nav-brand{font-family:'DM Serif Display',serif;font-size:14px;color:var(--forest);letter-spacing:-0.01em;line-height:1}
.nav-brand span{display:none}
.nav-divider{width:1px;height:20px;background:rgba(26,61,43,0.12);flex-shrink:0;margin:0 4px}
.nav-links{display:flex;gap:1px;align-items:center}
.nav-links a{font-size:13px;font-weight:500;color:var(--ink-soft);padding:7px 15px;border-radius:100px;transition:color 0.18s,background 0.18s}
.nav-links a:hover{color:var(--forest);background:var(--forest-pale)}
.nav-links a.active{background:var(--forest);color:#fff;box-shadow:0 2px 8px rgba(26,61,43,0.22)}
.nav-right{display:flex;align-items:center;gap:6px;padding-left:4px}
.nav-cta{background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:#fff;font-size:13px;font-weight:500;padding:8px 20px;border-radius:100px;transition:all 0.2s;box-shadow:0 2px 8px rgba(26,61,43,0.22)}
.nav-cta:hover{box-shadow:0 4px 16px rgba(26,61,43,0.32)}
.hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:5px 8px;background:none;border:none}
.hamburger span{display:block;width:20px;height:2px;background:var(--ink-mid);border-radius:2px;transition:all 0.25s}
.mobile-menu{display:none;position:fixed;top:60px;left:0;right:0;background:var(--warm);border-bottom:1px solid var(--border);padding:1rem;z-index:99;flex-direction:column;gap:3px;box-shadow:var(--shadow-md)}
.mobile-menu a{display:block;padding:10px 14px;font-size:14px;font-weight:500;color:var(--ink-mid);border-radius:var(--radius-sm);transition:background 0.15s}
.mobile-menu a:hover{background:var(--forest-pale);color:var(--forest)}
.mobile-menu .nav-cta{text-align:center;margin-top:6px;display:block;border-radius:100px;padding:12px}
.nav-user{display:flex;align-items:center;gap:8px}
.nav-avatar{width:30px;height:30px;border-radius:50%;object-fit:cover;border:2px solid var(--gold-light)}
.nav-avatar-placeholder{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:var(--gold);font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center}
.member-nav{position:fixed;top:4.25rem;left:50%;transform:translateX(-50%);z-index:99;display:flex;align-items:center;gap:1px;padding:3px;background:rgba(253,250,242,0.97);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(200,160,48,0.32);border-radius:100px;box-shadow:0 2px 12px rgba(26,61,43,0.07);white-space:nowrap}
.member-nav a{font-size:12px;font-weight:500;color:var(--ink-soft);padding:6px 14px;border-radius:100px;transition:color 0.18s,background 0.18s;display:flex;align-items:center;gap:6px}
.member-nav a i{font-size:11px}
.member-nav a:hover{color:var(--forest);background:var(--forest-pale)}
.member-nav a.active{background:var(--forest);color:#fff;box-shadow:0 2px 8px rgba(26,61,43,0.22)}
.member-nav .mn-logout{color:var(--rust)}
.member-nav .mn-logout:hover{background:#FDECEA;color:var(--rust)}
.mn-divider{width:1px;height:16px;background:rgba(200,160,48,0.28);margin:0 2px;flex-shrink:0}
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
  nav{top:0;left:0;right:0;transform:none;width:100%;border-radius:0;padding:0 1rem;height:60px;border:none;border-bottom:1px solid var(--border);box-shadow:none;justify-content:space-between;gap:0}
  .nav-logo{padding:0}
  .nav-brand span{display:block}
  .nav-divider{display:none}
  .nav-links{display:none}
  .nav-right .nav-user,.nav-right .nav-cta{display:none!important}
  .nav-right{padding:0}
  .hamburger{display:flex}
  main{padding-top:60px}
  body.has-member main{padding-top:60px!important}
  .member-nav{display:none}
  .mobile-menu{top:60px}
  .container{padding:0 1rem}
  .footer-inner{grid-template-columns:1fr;gap:2rem;padding-bottom:2rem}
  .footer-bottom{flex-direction:column;gap:0.75rem;text-align:center}
}
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
      <div class="nav-brand">Kas RT 10<span>Golden Park 2 · Cisauk</span></div>
    </div>
  </a>

  <div class="nav-divider"></div>

  <div class="nav-links">
    <a href="/kampanye" class="{{ request()->is('kampanye*') ? 'active' : '' }}">Kampanye</a>
    <a href="/donasi"   class="{{ request()->is('donasi') ? 'active' : '' }}">Donasi</a>
    <a href="/donatur"  class="{{ request()->is('donatur') ? 'active' : '' }}">Donatur</a>
    @auth<a href="/laporan"  class="{{ request()->is('laporan') ? 'active' : '' }}">Laporan</a>@endauth
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
    <button class="hamburger" onclick="toggleMobileMenu()" aria-label="Menu" id="hamburger-btn">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

@auth
<nav class="member-nav">
  <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">
    <i class="fa fa-gauge"></i> Dashboard
  </a>
  <a href="/dashboard/profil" class="{{ request()->is('dashboard/profil') ? 'active' : '' }}">
    <i class="fa fa-user"></i> Profil
  </a>
  <a href="/dashboard/riwayat" class="{{ request()->is('dashboard/riwayat') ? 'active' : '' }}">
    <i class="fa fa-clock-rotate-left"></i> Riwayat
  </a>
  <a href="/dashboard/iuran" class="{{ request()->is('dashboard/iuran') ? 'active' : '' }}">
    <i class="fa fa-wallet"></i> Iuran
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

<div class="mobile-menu" id="mobile-menu">
  <a href="/kampanye">Kampanye</a>
  <a href="/donasi">Donasi</a>
  <a href="/donatur">Donatur</a>
  @auth<a href="/laporan">Laporan</a>@endauth
  <hr style="border:none;border-top:1px solid var(--border);margin:4px 0">
  @auth
    <a href="/dashboard">Dashboard</a>
    <a href="/dashboard/profil">Profil</a>
    <a href="/dashboard/riwayat">Riwayat</a>
    <a href="/dashboard/iuran">Iuran</a>
    @if(auth()->user()->isAdmin())<a href="/admin">{{ $roleLabel ?? 'Admin' }}</a>@endif
    <a href="#" onclick="doLogout()" style="color:var(--rust)">Keluar</a>
  @else
    <a href="/login" class="nav-cta">Masuk dengan Google →</a>
  @endauth
</div>

@yield('content')

<footer>
  <div class="container">
    <div class="footer-inner">
      <div>
        <div class="footer-brand-mark">
          <svg viewBox="0 0 24 24"><path d="M12 2L2 8v14h7v-7h6v7h7V8L12 2z"/></svg>
        </div>
        <div class="footer-brand">Kas RT 10<br>Golden Park 2</div>
        <div class="footer-brand-sub">Platform patungan & manajemen keuangan warga RT 10, Perumahan Golden Park 2, Cisauk, Banten.</div>
      </div>
      <div>
        <div class="footer-col-title">Navigasi</div>
        <div class="footer-links-col">
          <a href="/">Beranda</a>
          <a href="/kampanye">Kampanye</a>
          <a href="/donasi">Donasi</a>
          <a href="/donatur">Papan Donatur</a>
          @auth<a href="/laporan">Laporan Keuangan</a>@endauth
        </div>
      </div>
      <div>
        <div class="footer-col-title">Informasi</div>
        <div class="footer-links-col">
          <span style="font-size:13px;color:rgba(255,255,255,0.42);line-height:1.6">
            <i class="fa fa-location-dot" style="width:14px;color:var(--gold);margin-right:4px"></i>
            Jl. Golden Park 2, Cisauk, Tangerang Selatan, Banten
          </span>
          <a href="/login" style="display:inline-flex;align-items:center;gap:6px;margin-top:0.5rem">
            <i class="fa fa-right-to-bracket" style="width:14px;color:var(--gold)"></i> Login Warga
          </a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div>© {{ date('Y') }} RT 10 · Perumahan Golden Park 2</div>
      <div>Dibuat dengan ❤️ untuk warga</div>
    </div>
  </div>
</footer>

<div class="toast" id="toast"></div>

<script>
const _csrfToken = '{{ csrf_token() }}';
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
function toggleMobileMenu() {
  const menu = document.getElementById('mobile-menu');
  const btn  = document.getElementById('hamburger-btn');
  const open = menu.style.display === 'flex';
  menu.style.display = open ? 'none' : 'flex';
  btn.querySelectorAll('span')[0].style.transform = open ? '' : 'rotate(45deg) translate(5px,5px)';
  btn.querySelectorAll('span')[1].style.opacity   = open ? '1' : '0';
  btn.querySelectorAll('span')[2].style.transform = open ? '' : 'rotate(-45deg) translate(5px,-5px)';
}
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mobile-menu a').forEach(a => a.addEventListener('click', () => {
    document.getElementById('mobile-menu').style.display = 'none';
  }));
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
const observer = new IntersectionObserver(
  e => e.forEach(el => { if (el.isIntersecting) el.target.classList.add('visible') }),
  { threshold: 0.08, rootMargin: '0px 0px -32px 0px' }
);
document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
</script>
@yield('scripts')
</body>
</html>
