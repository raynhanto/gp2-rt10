@extends('layouts.app')
@section('title', 'Beranda')
@section('styles')
<style>
/* ── Hero ─────────────────────────────────────────────────────── */
.hero-wrap{position:relative;overflow:hidden;background:linear-gradient(145deg,#0F2318 0%,#1A3D2B 55%,#1F4A32 100%);margin-top:-5rem;padding:5rem 0 3.25rem}
.hero-wrap::before{content:'';position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,0.055) 1px,transparent 1px);background-size:28px 28px;pointer-events:none}
.hero-blob{position:absolute;pointer-events:none}
.hero-blob-1{width:420px;height:420px;top:-100px;right:-80px;border-radius:62% 38% 54% 46%/48% 60% 40% 52%;background:rgba(255,255,255,0.03)}
.hero-blob-2{width:280px;height:280px;bottom:-80px;left:-60px;border-radius:50% 50% 38% 62%/55% 45% 55% 45%;background:rgba(200,160,48,0.055)}
.hero-blob-3{width:180px;height:180px;top:50%;left:8%;border-radius:50%;background:rgba(255,255,255,0.02)}
.hero-center{position:relative;z-index:1;text-align:center;max-width:720px;margin:0 auto}
.hero-label{margin-bottom:1.25rem;display:flex;justify-content:center}
.hero-h1{font-family:'DM Serif Display',serif;font-size:clamp(2.5rem,5vw,3.75rem);line-height:1.08;color:#fff;margin-bottom:1rem;letter-spacing:-0.03em}
.hero-h1 em{font-style:italic;color:var(--gold-light)}
.hero-sub{font-size:14.5px;color:rgba(255,255,255,0.62);line-height:1.75;max-width:500px;margin:0 auto 1.75rem}
.hero-actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-bottom:2rem}
.hero-chips{display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap}
.hero-chip{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.13);border-radius:100px;padding:7px 16px;font-size:12px;font-weight:500;color:rgba(255,255,255,0.65);backdrop-filter:blur(8px);transition:background 0.2s,border-color 0.2s}
.hero-chip:hover{background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.22)}
.hero-chip i{color:var(--gold);font-size:11px}

/* ── Shared section parts ─────────────────────────────────────── */
.section-wrap{padding:3rem 0}
.kgrid{display:grid;grid-template-columns:repeat(2,1fr);gap:1.25rem}
.kcard{background:#fff;border:1px solid var(--border);border-radius:var(--radius);cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;box-shadow:var(--shadow-sm);overflow:hidden}
.kcard:hover{transform:translateY(-4px);box-shadow:var(--shadow-md)}
.kcard:hover .kcard-img img{transform:scale(1.05)}
.kcard-img{height:180px;position:relative;overflow:hidden;background:var(--forest-pale)}
.kcard-img img{width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.45s ease}
.kcard-img-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.kcard-badge{position:absolute;top:10px;right:10px;backdrop-filter:blur(6px)}
.kcard-body{padding:1rem 1.125rem 1.25rem}
.feature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-top:2rem}
.feature-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem 1.5rem;box-shadow:var(--shadow-sm);transition:transform 0.2s,box-shadow 0.2s}
.feature-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.feature-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:1.125rem}
.how-wrap{background:var(--forest-pale);border-top:1px solid rgba(26,61,43,0.1);border-bottom:1px solid rgba(26,61,43,0.1);padding:3rem 0}
.steps{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;position:relative}
.steps::before{content:'';position:absolute;top:22px;left:calc(16.66% + 16px);right:calc(16.66% + 16px);height:1.5px;background:repeating-linear-gradient(90deg,var(--forest-light) 0,var(--forest-light) 6px,transparent 6px,transparent 14px);opacity:0.4}
.step{text-align:center}
.step-num{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:var(--gold);font-family:'DM Serif Display',serif;font-size:1.1rem;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 4px 14px rgba(26,61,43,0.24)}
.step-title{font-weight:600;font-size:14px;color:var(--forest);margin-bottom:0.375rem}
.step-sub{font-size:12.5px;color:var(--ink-soft);line-height:1.6}
.cta-banner{background:linear-gradient(135deg,var(--forest) 0%,var(--forest-mid) 100%);border-radius:var(--radius);padding:2.5rem;display:flex;justify-content:space-between;align-items:center;gap:2rem;position:relative;overflow:hidden;box-shadow:var(--shadow-md)}
.cta-banner::before{content:'';position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(200,160,48,0.08)}
.cta-banner::after{content:'';position:absolute;bottom:-60px;right:80px;width:140px;height:140px;border-radius:50%;background:rgba(200,160,48,0.05)}

/* ── Layanan Platform ─────────────────────────────────────────── */
.layanan-wrap{padding:4.5rem 0;background:#fff}
.layanan-intro{text-align:center;margin-bottom:2.5rem}
.layanan-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem}
.layanan-card{display:block;text-decoration:none;background:#fff;border:1.5px solid var(--border);border-radius:18px;padding:1.75rem 1.5rem;box-shadow:var(--shadow-sm);transition:transform 0.35s cubic-bezier(.22,.61,.36,1),box-shadow 0.35s,border-color 0.3s;position:relative;overflow:hidden}
.layanan-card::after{content:'';position:absolute;bottom:-40px;right:-40px;width:110px;height:110px;border-radius:50%;opacity:0;transition:opacity 0.4s,transform 0.4s;transform:scale(0.5)}
.layanan-card:hover{transform:translateY(-7px);box-shadow:0 20px 52px rgba(15,35,24,0.12);border-color:rgba(26,61,43,0.18)}
.layanan-card:hover::after{opacity:1;transform:scale(1.3)}
.layanan-icon{width:56px;height:56px;border-radius:15px;display:flex;align-items:center;justify-content:center;margin-bottom:1.125rem;transition:transform 0.4s cubic-bezier(.22,.61,.36,1),box-shadow 0.3s;position:relative;z-index:1}
.layanan-card:hover .layanan-icon{transform:scale(1.1) rotate(-6deg);box-shadow:0 8px 22px rgba(0,0,0,0.1)}
.lsvg{width:27px;height:27px;display:block;transition:filter 0.3s,transform 0.3s}
.layanan-card:hover .lsvg{filter:drop-shadow(0 2px 6px rgba(0,0,0,0.18));transform:scale(1.08)}
/* lst/lft: always visible — no hiding */
.lst{transition:stroke 0.3s}
.lft{transition:opacity 0.3s}
.layanan-cat{display:inline-flex;align-items:center;gap:5px;font-size:10.5px;font-weight:700;letter-spacing:0.07em;text-transform:uppercase;margin-bottom:0.55rem;position:relative;z-index:1}
.lcat-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0;animation:catPulse 2.5s ease-in-out infinite}
@keyframes catPulse{0%,100%{opacity:1}50%{opacity:0.4}}
.layanan-title{font-weight:700;font-size:15.5px;color:var(--forest);margin-bottom:0.4rem;line-height:1.3;position:relative;z-index:1}
.layanan-desc{font-size:12.5px;color:var(--ink-soft);line-height:1.7;margin-bottom:1.1rem;position:relative;z-index:1}
.layanan-link{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;transition:gap 0.25s;position:relative;z-index:1}
.layanan-card:hover .layanan-link{gap:10px}

/* ── Transparansi Live (dark stats) ──────────────────────────── */
.stats-wrap{background:linear-gradient(155deg,#0A1E12 0%,#0F2318 55%,#152B1E 100%);padding:4.5rem 0;position:relative;overflow:hidden}
.stats-dots{position:absolute;inset:0;background-image:radial-gradient(rgba(200,160,48,0.07) 1px,transparent 1px);background-size:28px 28px;pointer-events:none}
.stats-glow{position:absolute;border-radius:50%;pointer-events:none}
.stats-glow-l{width:320px;height:320px;background:radial-gradient(circle,rgba(26,61,43,0.7),transparent);left:-90px;top:50%;transform:translateY(-50%);filter:blur(45px)}
.stats-glow-r{width:260px;height:260px;background:radial-gradient(circle,rgba(200,160,48,0.12),transparent);right:-70px;top:50%;transform:translateY(-50%);filter:blur(55px)}
.stats-hdr{text-align:center;margin-bottom:3rem;position:relative;z-index:1}
.stats-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(200,160,48,0.12);border:1px solid rgba(200,160,48,0.25);border-radius:100px;padding:5px 16px;font-size:10.5px;font-weight:700;color:var(--gold-light);letter-spacing:0.07em;text-transform:uppercase;margin-bottom:1rem}
.live-dot{width:6px;height:6px;background:var(--gold);border-radius:50%;animation:livePulse 1.8s ease-in-out infinite}
@keyframes livePulse{0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(200,160,48,0.5)}60%{opacity:0.8;box-shadow:0 0 0 6px rgba(200,160,48,0)}}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);position:relative;z-index:1;border:1px solid rgba(255,255,255,0.07);border-radius:16px;overflow:hidden;background:rgba(255,255,255,0.02)}
.stat-item{text-align:center;padding:2rem 1.25rem;position:relative;transition:background 0.3s}
.stat-item::after{content:'';position:absolute;right:0;top:15%;height:70%;width:1px;background:rgba(255,255,255,0.07)}
.stat-item:last-child::after{display:none}
.stat-item:hover{background:rgba(255,255,255,0.03)}
.stat-ico{width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:17px;transition:transform 0.3s,background 0.3s}
.stat-item:hover .stat-ico{transform:scale(1.1);background:rgba(255,255,255,0.08)}
.stat-num{font-family:'DM Serif Display',serif;font-size:clamp(1.4rem,2.2vw,2rem);color:rgba(255,255,255,0.2);line-height:1;margin-bottom:0.35rem;transition:color 0.6s;min-height:2.1rem;display:flex;align-items:center;justify-content:center}
.stat-num.lit{color:var(--gold-light)}
.stat-lbl{font-size:11.5px;color:rgba(255,255,255,0.38);line-height:1.4}

/* ── Pengumuman Terbaru ───────────────────────────────────────── */
.peng-wrap{padding:4rem 0;background:var(--forest-pale);border-top:1px solid rgba(26,61,43,0.08)}
.peng-grid{display:grid;gap:1rem;margin-top:2rem}
.peng-card{background:#fff;border:1px solid var(--border);border-radius:14px;padding:1.125rem 1.375rem 1.125rem 1.625rem;display:flex;gap:1rem;align-items:flex-start;box-shadow:var(--shadow-sm);transition:transform 0.25s,box-shadow 0.25s,border-color 0.25s;position:relative;overflow:hidden}
.peng-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3.5px;border-radius:0 2px 2px 0}
.peng-card:hover{transform:translateX(5px);box-shadow:var(--shadow-md);border-color:rgba(26,61,43,0.16)}
.peng-ico{width:42px;height:42px;flex-shrink:0;border-radius:11px;display:flex;align-items:center;justify-content:center;transition:transform 0.3s}
.peng-card:hover .peng-ico{transform:scale(1.08)}
.peng-title{font-weight:600;font-size:14px;color:var(--forest);margin-bottom:3px;line-height:1.35}
.peng-body{font-size:12.5px;color:var(--ink-soft);line-height:1.6}
.peng-meta{display:flex;align-items:center;gap:8px;margin-top:6px;flex-wrap:wrap}
.peng-date{font-size:11px;color:var(--ink-soft);display:flex;align-items:center;gap:4px}
.peng-tag{display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;padding:2px 8px;border-radius:100px;text-transform:uppercase;letter-spacing:0.04em}

/* ── Skeleton ─────────────────────────────────────────────────── */
@keyframes skpulse{0%,100%{opacity:1}50%{opacity:0.35}}
.sk{border-radius:5px;animation:skpulse 1.4s ease-in-out infinite}
.sk-dark{background:rgba(26,61,43,0.08)}
.sk-white{background:rgba(255,255,255,0.12)}

/* ── Responsive ───────────────────────────────────────────────── */
@media(max-width:1024px){.layanan-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){
  .feature-grid{grid-template-columns:1fr 1fr}
  .steps::before{display:none}
  .stats-grid{grid-template-columns:repeat(2,1fr);border-radius:12px}
  .stat-item:nth-child(2)::after{display:none}
}
@media(max-width:768px){
  .kgrid,.layanan-grid,.feature-grid{grid-template-columns:1fr}
  .steps{grid-template-columns:1fr;gap:1.25rem}
  .cta-banner{flex-direction:column;text-align:center;padding:2rem}
  .stat-item{padding:1.5rem 1rem}
}
</style>
@endsection

@section('content')
<main>

{{-- ── HERO ──────────────────────────────────────────────────── --}}
<div class="hero-wrap">
  <div class="hero-blob hero-blob-1"></div>
  <div class="hero-blob hero-blob-2"></div>
  <div class="hero-blob hero-blob-3"></div>
  <div class="container">
    <div class="hero-center">
      <div class="hero-label">
        <div style="display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.18);padding:5px 16px;border-radius:100px;font-size:10.5px;font-weight:600;color:rgba(255,255,255,0.85);letter-spacing:0.07em;text-transform:uppercase;backdrop-filter:blur(8px)">
          <span style="width:7px;height:7px;background:var(--gold);border-radius:50%;flex-shrink:0;display:inline-block"></span>
          Perumahan Golden Park 2 · Cisauk
        </div>
      </div>
      <h1 class="hero-h1">Platform digital<br>warga <em>RT 10</em></h1>
      <p class="hero-sub">Donasi, iuran, laporan keuangan, dan informasi RT — transparan dan terbuka untuk seluruh warga.</p>
      <div class="hero-actions">
        @guest
        <a href="/login" class="btn-gold" style="font-size:15px;padding:13px 28px;display:inline-flex;align-items:center;gap:9px">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M20.283 10.356h-8.327v3.451h4.792c-.446 2.193-2.313 3.453-4.792 3.453a5.27 5.27 0 01-5.279-5.28 5.27 5.27 0 015.279-5.279c1.259 0 2.397.447 3.29 1.178l2.6-2.599c-1.584-1.381-3.615-2.233-5.89-2.233a8.908 8.908 0 00-8.934 8.934 8.908 8.908 0 008.934 8.934c4.467 0 8.529-3.249 8.529-8.934 0-.528-.081-1.097-.202-1.625z" fill="currentColor"/></svg>
          Masuk sebagai Warga
        </a>
        @endguest
        @auth
        <a href="/dashboard" class="btn-gold" style="font-size:15px;padding:13px 28px;display:inline-flex;align-items:center;gap:9px">
          @if(auth()->user()->avatar_url)
          <img src="{{ auth()->user()->avatar_url }}" style="width:20px;height:20px;border-radius:50%;object-fit:cover" alt="">
          @else
          <i class="fa fa-circle-user"></i>
          @endif
          Halo, {{ explode(' ', auth()->user()->name)[0] }}! → Dashboard
        </a>
        @endauth
        <a href="/kampanye" style="background:rgba(255,255,255,0.1);color:#fff;border:1.5px solid rgba(255,255,255,0.22);font-size:15px;font-weight:500;padding:13px 28px;border-radius:100px;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s;backdrop-filter:blur(8px)" onmouseenter="this.style.background='rgba(255,255,255,0.18)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">Lihat Kampanye</a>
      </div>
      <div class="hero-chips">
        <div class="hero-chip" id="chip-aktif"><i class="fa fa-layer-group"></i><span>Memuat kampanye…</span></div>
        <div class="hero-chip"><i class="fa fa-shield-halved"></i><span>Diverifikasi pengurus RT</span></div>
      </div>
    </div>
  </div>
</div>

{{-- ── KAMPANYE AKTIF ────────────────────────────────────────── --}}
<section class="section-wrap" style="background:var(--forest-pale);border-top:1px solid rgba(26,61,43,0.08)">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.75rem;flex-wrap:wrap;gap:10px">
      <div class="fade-in">
        <div class="section-label">Kampanye Aktif</div>
        <div class="section-title">Yang perlu dukungan kita</div>
      </div>
      <a href="/kampanye" style="font-size:13px;color:var(--forest-light);font-weight:500;display:flex;align-items:center;gap:5px">Semua kampanye <i class="fa fa-arrow-right" style="font-size:11px"></i></a>
    </div>
    <div class="kgrid" id="kgrid">
      @for($i=0;$i<4;$i++)
      <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm)">
        <div class="sk sk-dark" style="height:180px;border-radius:0;margin:0"></div>
        <div style="padding:1rem">
          <div class="sk sk-dark" style="height:13px;width:70%;margin-bottom:9px"></div>
          <div class="sk sk-dark" style="height:10px;width:90%;margin-bottom:16px"></div>
          <div class="sk sk-dark" style="height:5px;width:100%;border-radius:99px"></div>
        </div>
      </div>
      @endfor
    </div>
  </div>
</section>

{{-- ── LAYANAN PLATFORM ──────────────────────────────────────── --}}
<section class="layanan-wrap">
  <div class="container">
    <div class="layanan-intro fade-in">
      <div class="section-label" style="justify-content:center">Layanan Platform</div>
      <div class="section-title" style="font-size:clamp(1.5rem,2.5vw,2rem)">Semua kebutuhan RT dalam satu aplikasi</div>
      <p style="font-size:14px;color:var(--ink-soft);margin-top:0.5rem;max-width:480px;margin-left:auto;margin-right:auto;line-height:1.75">Dari donasi hingga data kependudukan — dikelola secara digital, transparan, dan bisa diakses seluruh warga kapan saja.</p>
    </div>
    <div class="layanan-grid">

      {{-- Donasi & Kampanye --}}
      <a href="/kampanye" class="layanan-card fade-in">
        <div class="layanan-icon" style="background:var(--forest-pale);color:var(--forest)">
          <svg class="lsvg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path class="lst" d="M12 21C12 21 3 13.5 3 8a4.5 4.5 0 019-1.5A4.5 4.5 0 0121 8c0 5.5-9 13-9 13z"/>
            <path class="lst" d="M12 9.5v5M10 13l2-3.5 2 3.5"/>
          </svg>
        </div>
        <div class="layanan-cat" style="color:var(--forest)"><span class="lcat-dot" style="background:var(--forest)"></span>Keuangan</div>
        <div class="layanan-title">Donasi &amp; Kampanye</div>
        <div class="layanan-desc">Dukung kampanye warga dengan donasi yang transparan dan diverifikasi langsung oleh pengurus RT sebelum masuk kas.</div>
        <div class="layanan-link" style="color:var(--forest)">Lihat kampanye <i class="fa fa-arrow-right" style="font-size:10px"></i></div>
        <div style="position:absolute;bottom:-35px;right:-35px;width:100px;height:100px;border-radius:50%;background:rgba(26,61,43,0.05);pointer-events:none"></div>
      </a>

      {{-- Iuran Bulanan --}}
      <a href="/dashboard/iuran" class="layanan-card fade-in">
        <div class="layanan-icon" style="background:rgba(200,160,48,0.1);color:var(--gold-dark)">
          <svg class="lsvg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect class="lst" x="3" y="4" width="18" height="17" rx="2"/>
            <path class="lst" d="M3 10h18M8 2v4M16 2v4"/>
            <path class="lst" d="M8.5 15.5l2 2 5-5"/>
          </svg>
        </div>
        <div class="layanan-cat" style="color:var(--gold-dark)"><span class="lcat-dot" style="background:var(--gold)"></span>Keuangan</div>
        <div class="layanan-title">Iuran Bulanan</div>
        <div class="layanan-desc">Pantau tagihan iuran, upload bukti bayar, dan lihat riwayat pembayaran unit rumah Anda secara online.</div>
        <div class="layanan-link" style="color:var(--gold-dark)">Bayar iuran <i class="fa fa-arrow-right" style="font-size:10px"></i></div>
        <div style="position:absolute;bottom:-35px;right:-35px;width:100px;height:100px;border-radius:50%;background:rgba(200,160,48,0.04);pointer-events:none"></div>
      </a>

      {{-- Laporan Keuangan --}}
      <a href="/laporan" class="layanan-card fade-in">
        <div class="layanan-icon" style="background:rgba(15,35,24,0.07);color:#0F2318">
          <svg class="lsvg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect class="lst" x="3" y="14" width="4" height="7" rx="1"/>
            <rect class="lst" x="10" y="9" width="4" height="12" rx="1"/>
            <rect class="lst" x="17" y="3" width="4" height="18" rx="1"/>
            <path class="lst" d="M5 12.5l7-5 7 3.5" stroke-dasharray="2.5 2"/>
          </svg>
        </div>
        <div class="layanan-cat" style="color:#0F2318"><span class="lcat-dot" style="background:#1A3D2B"></span>Transparansi</div>
        <div class="layanan-title">Laporan Keuangan</div>
        <div class="layanan-desc">Rekap kas masuk-keluar, pengeluaran lengkap dengan nota, dan laporan iuran warga yang bisa diunduh kapan saja.</div>
        <div class="layanan-link" style="color:#0F2318">Lihat laporan <i class="fa fa-arrow-right" style="font-size:10px"></i></div>
        <div style="position:absolute;bottom:-35px;right:-35px;width:100px;height:100px;border-radius:50%;background:rgba(15,35,24,0.04);pointer-events:none"></div>
      </a>

      {{-- Informasi RT --}}
      <a href="/informasi" class="layanan-card fade-in">
        <div class="layanan-icon" style="background:rgba(181,64,26,0.08);color:var(--rust)">
          <svg class="lsvg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect class="lst" x="3" y="3" width="18" height="18" rx="2"/>
            <path class="lst" d="M7 8h10M7 12h7M7 16h4"/>
            <circle class="lft" cx="17.5" cy="16" r="1.75" fill="currentColor" stroke="none"/>
          </svg>
        </div>
        <div class="layanan-cat" style="color:var(--rust)"><span class="lcat-dot" style="background:var(--rust)"></span>Informasi</div>
        <div class="layanan-title">Informasi RT</div>
        <div class="layanan-desc">Baca berita terkini, tata tertib, dan program kerja RT 10 Golden Park 2 yang dikelola langsung oleh pengurus.</div>
        <div class="layanan-link" style="color:var(--rust)">Baca informasi <i class="fa fa-arrow-right" style="font-size:10px"></i></div>
        <div style="position:absolute;bottom:-35px;right:-35px;width:100px;height:100px;border-radius:50%;background:rgba(181,64,26,0.04);pointer-events:none"></div>
      </a>

      {{-- Galeri Foto --}}
      <a href="/galeri" class="layanan-card fade-in">
        <div class="layanan-icon" style="background:rgba(45,90,168,0.08);color:#2D5AA8">
          <svg class="lsvg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path class="lst" d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
            <circle class="lst" cx="12" cy="13" r="4"/>
            <circle class="lft" cx="12" cy="13" r="2" fill="currentColor" stroke="none"/>
          </svg>
        </div>
        <div class="layanan-cat" style="color:#2D5AA8"><span class="lcat-dot" style="background:#2D5AA8"></span>Komunitas</div>
        <div class="layanan-title">Galeri Foto</div>
        <div class="layanan-desc">Dokumentasi kegiatan, acara warga, dan momen penting di lingkungan RT 10 Golden Park 2 dalam satu album.</div>
        <div class="layanan-link" style="color:#2D5AA8">Lihat galeri <i class="fa fa-arrow-right" style="font-size:10px"></i></div>
        <div style="position:absolute;bottom:-35px;right:-35px;width:100px;height:100px;border-radius:50%;background:rgba(45,90,168,0.04);pointer-events:none"></div>
      </a>

      {{-- Agenda Kegiatan --}}
      <a href="/agenda" class="layanan-card fade-in">
        <div class="layanan-icon" style="background:rgba(80,50,180,0.07);color:#5032B4">
          <svg class="lsvg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect class="lst" x="3" y="4" width="18" height="17" rx="2"/>
            <path class="lst" d="M3 10h18M8 2v4M16 2v4"/>
            <circle class="lft" cx="8.5" cy="15.5" r="1.5" fill="currentColor" stroke="none"/>
            <circle class="lft" cx="15.5" cy="15.5" r="1.5" fill="currentColor" stroke="none"/>
          </svg>
        </div>
        <div class="layanan-cat" style="color:#5032B4"><span class="lcat-dot" style="background:#5032B4"></span>Komunitas</div>
        <div class="layanan-title">Agenda Kegiatan</div>
        <div class="layanan-desc">Jadwal rapat RT, kerja bakti, arisan, dan kegiatan warga yang bisa diikuti seluruh penghuni Golden Park 2.</div>
        <div class="layanan-link" style="color:#5032B4">Lihat agenda <i class="fa fa-arrow-right" style="font-size:10px"></i></div>
        <div style="position:absolute;bottom:-35px;right:-35px;width:100px;height:100px;border-radius:50%;background:rgba(80,50,180,0.04);pointer-events:none"></div>
      </a>

    </div>
  </div>
</section>

{{-- ── TRANSPARANSI LIVE ─────────────────────────────────────── --}}
<div class="stats-wrap">
  <div class="stats-dots"></div>
  <div class="stats-glow stats-glow-l"></div>
  <div class="stats-glow stats-glow-r"></div>
  <div class="container">
    <div class="stats-hdr fade-in">
      <div><span class="stats-pill"><span class="live-dot"></span> Data real-time</span></div>
      <div style="font-family:'DM Serif Display',serif;font-size:clamp(1.6rem,3vw,2.2rem);color:#fff;line-height:1.25;margin-bottom:0.5rem">Transparansi keuangan RT 10</div>
      <div style="font-size:14px;color:rgba(255,255,255,0.42)">Diperbarui otomatis dari sistem kas &amp; donasi</div>
    </div>
    <div class="stats-grid" id="statsGrid">
      <div class="stat-item">
        <div class="stat-ico" style="color:var(--gold)"><i class="fa fa-wallet"></i></div>
        <div class="stat-num" id="statSaldo">—</div>
        <div class="stat-lbl">Saldo Kas Berjalan</div>
      </div>
      <div class="stat-item">
        <div class="stat-ico" style="color:#5DADE2"><i class="fa fa-hand-holding-heart"></i></div>
        <div class="stat-num" id="statMasuk">—</div>
        <div class="stat-lbl">Total Donasi Masuk</div>
      </div>
      <div class="stat-item">
        <div class="stat-ico" style="color:#58D68D"><i class="fa fa-circle-check"></i></div>
        <div class="stat-num" id="statVerified">—</div>
        <div class="stat-lbl">Donasi Terverifikasi</div>
      </div>
      <div class="stat-item">
        <div class="stat-ico" style="color:#F1948A"><i class="fa fa-layer-group"></i></div>
        <div class="stat-num" id="statKampanye">—</div>
        <div class="stat-lbl">Total Kampanye</div>
      </div>
    </div>
  </div>
</div>

{{-- ── PENGUMUMAN TERBARU ────────────────────────────────────── --}}
<section class="peng-wrap">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:10px">
      <div class="fade-in">
        <div class="section-label">Dari Pengurus RT</div>
        <div class="section-title">Pengumuman Terbaru</div>
      </div>
    </div>
    <div class="peng-grid" id="pengGrid">
      @for($i=0;$i<3;$i++)
      <div style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:1.125rem 1.375rem;display:flex;gap:1rem;align-items:flex-start;box-shadow:var(--shadow-sm)">
        <div class="sk sk-dark" style="width:42px;height:42px;border-radius:11px;flex-shrink:0"></div>
        <div style="flex:1">
          <div class="sk sk-dark" style="height:13px;width:60%;margin-bottom:9px"></div>
          <div class="sk sk-dark" style="height:10px;width:90%;margin-bottom:6px"></div>
          <div class="sk sk-dark" style="height:10px;width:72%"></div>
        </div>
      </div>
      @endfor
    </div>
  </div>
</section>

{{-- ── KENAPA PERCAYAI KAMI ──────────────────────────────────── --}}
<section style="padding:3.5rem 0;background:#fff">
  <div class="container">
    <div class="fade-in" style="text-align:center;margin-bottom:0.5rem">
      <div class="section-label" style="justify-content:center">Kenapa percayai kami</div>
      <div class="section-title" style="font-size:clamp(1.5rem,2.5vw,2rem)">Transparan sejak rupiah pertama</div>
    </div>
    <div class="feature-grid">
      @foreach([
        ['fa-solid fa-shield-halved','var(--forest-pale)','var(--forest)','Dana Terverifikasi','Setiap donasi dicatat dan diverifikasi langsung oleh pengurus RT 10 sebelum masuk ke kas.'],
        ['fa-solid fa-chart-bar','var(--gold-pale)','var(--gold-dark)','Laporan Terbuka','Rekap penggunaan dana bisa diakses kapan saja oleh seluruh warga tanpa perlu login.'],
        ['fa-solid fa-receipt','#FDECEA','var(--rust)','Bukti Tiap Transaksi','Setiap pengeluaran dilengkapi foto nota sehingga dapat diaudit oleh siapapun.'],
      ] as [$icon,$bg,$col,$title,$desc])
      <div class="feature-card fade-in">
        <div class="feature-icon" style="background:{{$bg}};color:{{$col}}"><i class="{{$icon}}"></i></div>
        <div style="font-weight:600;font-size:15px;color:var(--forest);margin-bottom:0.5rem">{{$title}}</div>
        <div style="font-size:13px;color:var(--ink-soft);line-height:1.7">{{$desc}}</div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── CARA BERDONASI ────────────────────────────────────────── --}}
<div class="how-wrap">
  <div class="container">
    <div class="fade-in" style="text-align:center;margin-bottom:2.5rem">
      <div class="section-label" style="justify-content:center">Cara Berdonasi</div>
      <div class="section-title" style="font-size:clamp(1.4rem,2.5vw,1.9rem)">Hanya 3 langkah mudah</div>
    </div>
    <div class="steps">
      @foreach([
        ['1','Pilih Kampanye','Lihat kampanye yang sedang berjalan dan pilih mana yang ingin kamu dukung.'],
        ['2','Scan QRIS','Scan QR code menggunakan aplikasi dompet digital atau mobile banking-mu.'],
        ['3','Upload Bukti','Kirim foto bukti bayar agar donasi tercatat dan dapat diverifikasi pengurus.'],
      ] as [$n,$t,$d])
      <div class="step fade-in">
        <div class="step-num">{{$n}}</div>
        <div class="step-title">{{$t}}</div>
        <div class="step-sub">{{$d}}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>

{{-- ── CTA BANNER ────────────────────────────────────────────── --}}
<section style="padding:3rem 0;background:#fff">
  <div class="container">
    <div class="cta-banner fade-in">
      <div style="position:relative;z-index:1">
        <div style="font-family:'DM Serif Display',serif;font-size:clamp(1.4rem,2.5vw,1.9rem);color:#fff;line-height:1.25;margin-bottom:0.625rem">Pantau riwayat donasi &amp; kampanye</div>
        <div style="font-size:14px;color:rgba(255,255,255,0.65);max-width:420px;line-height:1.6">Login dengan akun Google untuk menyimpan riwayat donasi, bayar iuran, dan mendapat update kampanye terbaru.</div>
      </div>
      <div style="display:flex;gap:10px;flex-shrink:0;position:relative;z-index:1;flex-wrap:wrap">
        <a href="/login" class="btn-gold">Masuk dengan Google</a>
        <a href="/laporan" style="background:rgba(255,255,255,0.1);color:#fff;border:1.5px solid rgba(255,255,255,0.2);font-size:14px;font-weight:500;padding:12px 24px;border-radius:100px;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s" onmouseenter="this.style.background='rgba(255,255,255,0.18)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">Lihat Laporan</a>
      </div>
    </div>
  </div>
</section>

</main>
@endsection

@section('scripts')
<script>
const FILL_CLASSES = ['pf-green','pf-gold','pf-rust','pf-green'];
const BADGE_MAP    = {urgent:'badge-urgent',aktif:'badge-open',selesai:'badge-done',arsip:'badge-done'};
const LABEL_MAP    = {urgent:'Mendesak',aktif:'Berjalan',selesai:'Selesai',arsip:'Arsip'};
const PH_GRADS     = [
  'linear-gradient(135deg,#1A3D2B 0%,#3D7A56 100%)',
  'linear-gradient(135deg,#9A7818 0%,#C8A030 100%)',
  'linear-gradient(135deg,#B5401A 0%,#D96A4A 100%)',
  'linear-gradient(135deg,#2D5AA8 0%,#5B89D8 100%)',
];

function fmtKompak(n) {
  n = Math.abs(parseInt(n));
  if (n >= 1000000000) return 'Rp ' + (n/1000000000).toFixed(1).replace('.0','') + ' M';
  if (n >= 1000000)    return 'Rp ' + (n/1000000).toFixed(1).replace('.0','') + ' jt';
  if (n >= 1000)       return 'Rp ' + Math.round(n/1000) + ' rb';
  return 'Rp 0';
}

function escHtml(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ── Count-up animation ──────────────────────────────────────── */
function animateCount(el, target, isCurrency, dur = 1800) {
  if (!el) return;
  el.classList.add('lit');
  const start = performance.now();
  (function tick(now) {
    const p = Math.min((now - start) / dur, 1);
    const ease = 1 - Math.pow(1 - p, 3);
    const cur = Math.round(target * ease);
    el.textContent = isCurrency ? fmtKompak(cur) : cur.toLocaleString('id-ID');
    if (p < 1) requestAnimationFrame(tick);
  })(start);
}

/* ── State ───────────────────────────────────────────────────── */
let kasData      = null;
let kampanyeData = null;
let statsAnimated = false;

function tryAnimateStats() {
  if (statsAnimated || !kasData || !kampanyeData) return;
  const el = document.getElementById('statsGrid');
  if (!el) return;
  const rect = el.getBoundingClientRect();
  if (rect.top < window.innerHeight * 0.9) {
    statsAnimated = true;
    animateCount(document.getElementById('statSaldo'),    parseInt(kasData.saldo),        true);
    animateCount(document.getElementById('statMasuk'),    parseInt(kasData.masuk),         true);
    animateCount(document.getElementById('statVerified'), parseInt(kasData.donasi_count),  false);
    animateCount(document.getElementById('statKampanye'), kampanyeData.length,             false);
  }
}

/* IntersectionObserver for stats section */
const statsObs = new IntersectionObserver(entries => {
  if (entries[0].isIntersecting) tryAnimateStats();
}, { threshold: 0.25 });
const statsEl = document.getElementById('statsGrid');
if (statsEl) statsObs.observe(statsEl);

/* ── Render helpers ──────────────────────────────────────────── */
function renderKampanye(list) {
  const filtered = list.filter(x => x.status !== 'arsip');
  const gridEl = document.getElementById('kgrid');
  const aktifCount = list.filter(x => x.status === 'aktif' || x.status === 'urgent').length;
  const chip = document.getElementById('chip-aktif');
  if (chip) chip.innerHTML = `<i class="fa fa-layer-group" style="color:var(--gold)"></i><span>${aktifCount} Kampanye aktif</span>`;

  if (!filtered.length) {
    gridEl.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--ink-soft)">Belum ada kampanye aktif.</div>';
    return;
  }
  gridEl.innerHTML = filtered.slice(0, 4).map((k, i) => {
    const pct = Math.min(100, Math.round(k.terkumpul / k.target * 100));
    const tgt = parseInt(k.target);
    const tgtStr = tgt >= 1000000 ? (tgt/1000000).toFixed(1).replace('.0','')+'jt' : Math.round(tgt/1000)+'rb';
    const dl = k.deadline ? new Date(k.deadline).toLocaleDateString('id-ID',{day:'numeric',month:'short'}) : null;
    const imgHtml = k.foto_url
      ? `<img src="${k.foto_url}" alt="" loading="lazy">`
      : `<div class="kcard-img-ph" style="background:${PH_GRADS[i%4]}"><svg viewBox="0 0 24 24" fill="rgba(255,255,255,0.35)" style="width:52px;height:52px"><path d="M12 2L2 8v14h7v-7h6v7h7V8L12 2z"/></svg></div>`;
    return `<div class="kcard fade-in" onclick="location.href='/kampanye/${k.id}'">
      <div class="kcard-img">${imgHtml}<div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(15,35,24,0.38) 0%,transparent 55%);pointer-events:none"></div><span class="badge ${BADGE_MAP[k.status]||'badge-open'} kcard-badge">${LABEL_MAP[k.status]||k.status}</span></div>
      <div class="kcard-body">
        <div style="font-family:'DM Serif Display',serif;font-size:1.05rem;color:var(--forest);line-height:1.3;margin-bottom:5px">${escHtml(k.judul)}</div>
        <div style="font-size:12px;color:var(--ink-soft);line-height:1.6;margin-bottom:10px">${escHtml(k.deskripsi.substring(0,80))}…</div>
        <div class="progress-track" style="height:5px;margin:0 0 8px"><div class="progress-fill ${FILL_CLASSES[i%4]}" style="width:${pct}%"></div></div>
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:13px;font-weight:700;color:var(--forest)">Rp ${parseInt(k.terkumpul).toLocaleString('id-ID')}</span>
          <span style="font-size:11px;color:var(--ink-soft)">${pct}% · Rp ${tgtStr}${dl?' · '+dl:''}</span>
        </div>
      </div>
    </div>`;
  }).join('');
  document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
}

function renderPengumuman(list) {
  const grid = document.getElementById('pengGrid');
  if (!list || !list.length) {
    grid.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-soft)">Belum ada pengumuman.</div>';
    return;
  }

  const ICONS = [
    `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>`,
    `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
    `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>`,
  ];
  const COLORS = ['var(--forest)', '#2D5AA8', 'var(--gold-dark)'];
  const BGS    = ['var(--forest-pale)', 'rgba(45,90,168,0.08)', 'rgba(200,160,48,0.1)'];

  grid.innerHTML = list.slice(0, 3).map((p, i) => {
    const d = new Date(p.created_at || p.tanggal || Date.now());
    const dateStr = d.toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'});
    const isDonatur = p.target === 'donatur';
    const tagBg   = isDonatur ? 'rgba(200,160,48,0.12)' : 'rgba(26,61,43,0.07)';
    const tagCol  = isDonatur ? 'var(--gold-dark)' : 'var(--forest)';
    const tagText = isDonatur ? 'Khusus Donatur' : 'Semua Warga';
    const judul   = escHtml(p.judul || p.isi.substring(0, 55) + (p.isi.length > 55 ? '…' : ''));
    const pesan   = escHtml(p.isi.substring(0, 120)) + (p.isi.length > 120 ? '…' : '');
    return `<div class="peng-card fade-in">
      <div style="position:absolute;left:0;top:0;bottom:0;width:3.5px;background:${COLORS[i]};border-radius:0 2px 2px 0"></div>
      <div class="peng-ico" style="background:${BGS[i]};color:${COLORS[i]}">${ICONS[i]}</div>
      <div style="flex:1;min-width:0">
        <div class="peng-title">${judul}</div>
        <div class="peng-body">${pesan}</div>
        <div class="peng-meta">
          <span class="peng-date"><i class="fa fa-clock" style="font-size:9px;opacity:0.55"></i> ${dateStr}</span>
          <span class="peng-tag" style="background:${tagBg};color:${tagCol}"><i class="fa fa-users" style="font-size:9px"></i> ${tagText}</span>
        </div>
      </div>
    </div>`;
  }).join('');
  document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
}

/* ── Main load ───────────────────────────────────────────────── */
async function loadHome() {
  const [kRes, kasRes, pengRes] = await Promise.all([
    fetch('/api/kampanye'),
    fetch('/api/kas/summary'),
    fetch('/api/pengumuman'),
  ]);

  try {
    const kas = await kasRes.json();
    if (kas.success) { kasData = kas.data; tryAnimateStats(); }
  } catch(e) {}

  try {
    const k = await kRes.json();
    if (k.success) {
      kampanyeData = k.data;
      renderKampanye(k.data);
      tryAnimateStats();
    }
  } catch(e) {
    const g = document.getElementById('kgrid');
    if (g) g.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--ink-soft)">Gagal memuat kampanye.</div>';
  }

  try {
    const p = await pengRes.json();
    if (p.success) renderPengumuman(p.data);
  } catch(e) {}
}

loadHome();
</script>
@endsection
