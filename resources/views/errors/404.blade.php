<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — Halaman Tidak Ditemukan | Kas RT 10</title>
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
.badge{display:inline-flex;align-items:center;gap:8px;background:var(--forest-pale);border:1px solid rgba(26,61,43,0.15);border-radius:100px;padding:6px 16px;font-size:12px;font-weight:600;color:var(--forest);letter-spacing:0.05em;margin-bottom:1.5rem}
.num{font-family:'DM Serif Display',serif;font-size:6rem;color:var(--forest);line-height:1;margin-bottom:0.5rem;opacity:0.12;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);white-space:nowrap;pointer-events:none}
.icon-wrap{position:relative;width:80px;height:80px;margin:0 auto 1.5rem}
.icon-bg{width:80px;height:80px;border-radius:50%;background:linear-gradient(145deg,var(--forest),var(--forest-mid));display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(26,61,43,0.2)}
.icon-bg svg{width:36px;height:36px;fill:var(--gold)}
h1{font-family:'DM Serif Display',serif;font-size:1.9rem;color:var(--forest);margin-bottom:0.75rem}
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
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Error 404
  </div>
  <div class="icon-wrap">
    <div class="icon-bg">
      <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    </div>
  </div>
  <h1>Halaman Tidak Ditemukan</h1>
  <p>Halaman yang kamu cari tidak ada atau sudah dipindahkan.<br>Coba kembali ke beranda atau hubungi pengurus RT.</p>
  <div class="actions">
    <a href="/" class="btn-primary">← Beranda</a>
    <a href="javascript:history.back()" class="btn-secondary">Kembali</a>
  </div>
</div>
</body>
</html>
