<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>500 — Terjadi Kesalahan | GP2 RT10</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<style>
:root{
  --cream:#F3EDD8;--warm:#FDFAF2;--parchment:#EDE5CC;
  --forest:#1A3D2B;--forest-mid:#2A5C40;--forest-light:#3D7A56;--forest-pale:#EBF3EE;
  --gold:#C8A030;--gold-pale:#FBF3DC;
  --ink:#1A1810;--ink-mid:#3D3828;--ink-soft:#6B6050;--ink-mute:#9D9080;
  --border:rgba(26,61,43,0.12);
  --radius:16px;--radius-sm:10px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{font-family:'DM Sans',sans-serif;background:var(--warm);color:var(--ink);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:2rem}
.wrap{text-align:center;max-width:420px}
.badge{display:inline-flex;align-items:center;gap:8px;background:var(--gold-pale);border:1px solid rgba(200,160,48,0.25);border-radius:100px;padding:6px 16px;font-size:12px;font-weight:600;color:#7A5C00;letter-spacing:0.05em;margin-bottom:1.5rem}
.icon-wrap{width:80px;height:80px;margin:0 auto 1.5rem}
.icon-bg{width:80px;height:80px;border-radius:50%;background:linear-gradient(145deg,#5A4200,#8A6200);display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(200,160,48,0.25)}
.icon-bg svg{width:36px;height:36px;fill:var(--gold)}
h1{font-family:'DM Serif Display',serif;font-size:1.9rem;color:var(--ink);margin-bottom:0.75rem}
p{font-size:14px;color:var(--ink-soft);line-height:1.7;margin-bottom:2rem}
.actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.btn-primary{background:linear-gradient(135deg,var(--forest),var(--forest-mid));color:#fff;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:500;padding:10px 22px;border-radius:100px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 8px rgba(26,61,43,0.22);transition:box-shadow 0.2s}
.btn-primary:hover{box-shadow:0 4px 16px rgba(26,61,43,0.32)}
.btn-secondary{background:#fff;color:var(--forest);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:500;padding:10px 22px;border-radius:100px;border:1.5px solid var(--border);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:border-color 0.2s,background 0.2s}
.btn-secondary:hover{border-color:rgba(26,61,43,0.25);background:var(--forest-pale)}
</style>
</head>
<body>
<div class="wrap">
  <div class="badge">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    Error 500
  </div>
  <div class="icon-wrap">
    <div class="icon-bg">
      <svg viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM13 13v4h-2v-4H8l4-4 4 4h-3z"/></svg>
    </div>
  </div>
  <h1>Terjadi Kesalahan</h1>
  <p>Server mengalami masalah. Tim pengurus sudah diberitahu.<br>Coba muat ulang halaman atau kembali beberapa saat lagi.</p>
  <div class="actions">
    <a href="/" class="btn-primary">← Beranda</a>
    <a href="javascript:location.reload()" class="btn-secondary">Muat Ulang</a>
  </div>
</div>
</body>
</html>
