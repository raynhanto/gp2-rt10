@extends('layouts.app')
@section('title', 'Beranda')
@section('styles')
<style>
.hero-wrap{position:relative;overflow:hidden;background:linear-gradient(145deg,#0F2318 0%,#1A3D2B 55%,#1F4A32 100%);margin-top:-5rem;padding:8rem 0 5rem}
.hero-wrap::before{content:'';position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,0.055) 1px,transparent 1px);background-size:28px 28px;pointer-events:none}
.hero-blob{position:absolute;pointer-events:none}
.hero-blob-1{width:420px;height:420px;top:-100px;right:-80px;border-radius:62% 38% 54% 46%/48% 60% 40% 52%;background:rgba(255,255,255,0.03)}
.hero-blob-2{width:280px;height:280px;bottom:-80px;left:-60px;border-radius:50% 50% 38% 62%/55% 45% 55% 45%;background:rgba(200,160,48,0.055)}
.hero-blob-3{width:180px;height:180px;top:50%;left:8%;border-radius:50%;background:rgba(255,255,255,0.02)}
.hero-center{position:relative;z-index:1;text-align:center;max-width:720px;margin:0 auto}
.hero-label{margin-bottom:1.75rem;display:flex;justify-content:center}
.hero-h1{font-family:'DM Serif Display',serif;font-size:clamp(3rem,6vw,4.5rem);line-height:1.08;color:#fff;margin-bottom:1.25rem;letter-spacing:-0.03em}
.hero-h1 em{font-style:italic;color:var(--gold-light)}
.hero-sub{font-size:16px;color:rgba(255,255,255,0.62);line-height:1.75;max-width:500px;margin:0 auto 2.25rem}
.hero-actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-bottom:2.75rem}
.hero-chips{display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap}
.hero-chip{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.13);border-radius:100px;padding:7px 16px;font-size:12px;font-weight:500;color:rgba(255,255,255,0.65);backdrop-filter:blur(8px)}
.hero-chip i{color:var(--gold);font-size:11px}
.section-wrap{padding:3rem 0}
.kgrid{display:grid;grid-template-columns:repeat(2,1fr);gap:1.25rem}
.kcard{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:0;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;box-shadow:var(--shadow-sm);overflow:hidden}
.kcard:hover{transform:translateY(-4px);box-shadow:var(--shadow-md)}
.kcard:hover .kcard-img img{transform:scale(1.05)}
.kcard-img{height:180px;position:relative;overflow:hidden;background:var(--forest-pale)}
.kcard-img img{width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.45s ease}
.kcard-img-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.5rem}
.kcard-badge{position:absolute;top:10px;right:10px;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px)}
.kcard-body{padding:1rem 1.125rem 1.25rem}
.feature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-top:2rem}
.feature-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:1.75rem 1.5rem;box-shadow:var(--shadow-sm);transition:transform 0.2s,box-shadow 0.2s}
.feature-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.feature-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:1.125rem}
.how-wrap{background:var(--forest-pale);border-top:1px solid rgba(26,61,43,0.1);border-bottom:1px solid rgba(26,61,43,0.1);padding:3rem 0}
.steps{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;position:relative}
.steps::before{content:'';position:absolute;top:22px;left:calc(16.66% + 16px);right:calc(16.66% + 16px);height:1.5px;background:repeating-linear-gradient(90deg,var(--forest-light) 0,var(--forest-light) 6px,transparent 6px,transparent 14px);opacity:0.4}
.step{text-align:center;position:relative}
.step-num{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:var(--gold);font-family:'DM Serif Display',serif;font-size:1.1rem;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 4px 14px rgba(26,61,43,0.24)}
.step-title{font-weight:600;font-size:14px;color:var(--forest);margin-bottom:0.375rem}
.step-sub{font-size:12.5px;color:var(--ink-soft);line-height:1.6}
.cta-banner{background:linear-gradient(135deg,var(--forest) 0%,var(--forest-mid) 100%);border-radius:var(--radius);padding:2.5rem;display:flex;justify-content:space-between;align-items:center;gap:2rem;position:relative;overflow:hidden;box-shadow:var(--shadow-md)}
.cta-banner::before{content:'';position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(200,160,48,0.08)}
.cta-banner::after{content:'';position:absolute;bottom:-60px;right:80px;width:140px;height:140px;border-radius:50%;background:rgba(200,160,48,0.05)}
@keyframes skpulse{0%,100%{opacity:1}50%{opacity:0.3}}
.sk{background:rgba(255,255,255,0.12);border-radius:4px;animation:skpulse 1.4s ease-in-out infinite}
@media(max-width:900px){.feature-grid{grid-template-columns:1fr 1fr}.steps::before{display:none}}
@media(max-width:768px){.kgrid{grid-template-columns:1fr}.feature-grid{grid-template-columns:1fr}.steps{grid-template-columns:1fr;gap:1.25rem}.cta-banner{flex-direction:column;text-align:center;padding:2rem}}
</style>
@endsection
@section('content')
<main>

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
      <h1 class="hero-h1">Patungan digital<br>warga <em>RT 10</em></h1>
      <p class="hero-sub">Donasi untuk kebutuhan lingkungan kita bersama.<br>Transparan, mudah, dan bisa dipantau oleh seluruh warga RT 10.</p>
      <div class="hero-actions">
        <a href="/donasi" class="btn-gold" style="font-size:15px;padding:13px 28px">Donasi Sekarang →</a>
        <a href="/kampanye" style="background:rgba(255,255,255,0.1);color:#fff;border:1.5px solid rgba(255,255,255,0.22);font-size:15px;font-weight:500;padding:13px 28px;border-radius:100px;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s;backdrop-filter:blur(8px)" onmouseenter="this.style.background='rgba(255,255,255,0.18)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">Lihat Kampanye</a>
      </div>
      <div class="hero-chips">
        <div class="hero-chip" id="chip-aktif"><i class="fa fa-layer-group"></i><span>Memuat kampanye…</span></div>
        <div class="hero-chip"><i class="fa fa-chart-bar"></i><span>Laporan terbuka</span></div>
        <div class="hero-chip"><i class="fa fa-shield-halved"></i><span>Diverifikasi pengurus RT</span></div>
      </div>
    </div>
  </div>
</div>

<section class="section-wrap">
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
        <div class="sk" style="height:180px;border-radius:0;margin:0"></div>
        <div style="padding:1rem">
          <div class="sk" style="height:13px;width:70%;margin-bottom:9px"></div>
          <div class="sk" style="height:10px;width:90%;margin-bottom:16px"></div>
          <div class="sk" style="height:5px;width:100%;border-radius:99px"></div>
        </div>
      </div>
      @endfor
    </div>
  </div>
</section>

<section style="padding:0 0 3.5rem">
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

<section style="padding:3rem 0">
  <div class="container">
    <div class="cta-banner fade-in">
      <div style="position:relative;z-index:1">
        <div style="font-family:'DM Serif Display',serif;font-size:clamp(1.4rem,2.5vw,1.9rem);color:#fff;line-height:1.25;margin-bottom:0.625rem">Pantau riwayat donasi & kampanye</div>
        <div style="font-size:14px;color:rgba(255,255,255,0.65);max-width:420px;line-height:1.6">Login dengan akun Google untuk menyimpan riwayat donasi dan mendapat update kampanye terbaru.</div>
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
  n = parseInt(n);
  if (n >= 1000000) return 'Rp ' + (n/1000000).toFixed(1).replace('.0','') + ' jt';
  if (n >= 1000)    return 'Rp ' + Math.round(n/1000) + ' rb';
  return 'Rp 0';
}
async function loadHome() {
  const [kRes, kasRes] = await Promise.all([fetch('/api/kampanye'), fetch('/api/kas/summary')]);
  try {
    const kas = await kasRes.clone().json();
    if (kas.success) {
      const chipAktif = document.getElementById('chip-aktif');
    }
  } catch(e) {}
  const k = await kRes.json();
  if (!k.success) return;
  const list = k.data.filter(x => x.status !== 'arsip');
  const aktifCount = list.filter(x => x.status === 'aktif' || x.status === 'urgent').length;
  const chipAktif = document.getElementById('chip-aktif');
  if (chipAktif) chipAktif.innerHTML = `<i class="fa fa-layer-group" style="color:var(--gold)"></i><span>${aktifCount} Kampanye aktif</span>`;
  const gridEl = document.getElementById('kgrid');
  if (!list.length) { gridEl.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--ink-soft)">Belum ada kampanye aktif.</div>'; return; }
  gridEl.innerHTML = list.slice(0,4).map((k,i) => {
    const pct = Math.min(100, Math.round(k.terkumpul / k.target * 100));
    const tgt = parseInt(k.target);
    const tgtStr = tgt >= 1000000 ? (tgt/1000000).toFixed(1).replace('.0','')+'jt' : Math.round(tgt/1000)+'rb';
    const dl = k.deadline ? new Date(k.deadline).toLocaleDateString('id-ID',{day:'numeric',month:'short'}) : null;
    const imgHtml = k.foto_url ? '<img src="' + k.foto_url + '" alt="" loading="lazy">' : '<div class="kcard-img-ph" style="background:' + PH_GRADS[i%4] + '"><svg viewBox="0 0 24 24" fill="rgba(255,255,255,0.35)" style="width:52px;height:52px"><path d="M12 2L2 8v14h7v-7h6v7h7V8L12 2z"/></svg></div>';
    return `<div class="kcard fade-in" onclick="location.href='/kampanye/${k.id}'">
      <div class="kcard-img">${imgHtml}<div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(15,35,24,0.38) 0%,transparent 55%);pointer-events:none"></div><span class="badge ${BADGE_MAP[k.status]||'badge-open'} kcard-badge">${LABEL_MAP[k.status]||k.status}</span></div>
      <div class="kcard-body">
        <div style="font-family:'DM Serif Display',serif;font-size:1.05rem;color:var(--forest);line-height:1.3;margin-bottom:5px">${k.judul}</div>
        <div style="font-size:12px;color:var(--ink-soft);line-height:1.6;margin-bottom:10px">${k.deskripsi.substring(0,80)}…</div>
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
loadHome();
</script>
@endsection
