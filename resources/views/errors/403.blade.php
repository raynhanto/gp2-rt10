<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>403 — Akses Ditolak | GP2 RT10</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<style>
:root{
  --cream:#F3EDD8;--warm:#FDFAF2;--parchment:#EDE5CC;
  --forest:#1A3D2B;--forest-mid:#2A5C40;--forest-light:#3D7A56;--forest-pale:#EBF3EE;
  --gold:#C8A030;--gold-pale:#FBF3DC;
  --rust:#B5401A;--rust-pale:#FDECEA;
  --ink:#1A1810;--ink-mid:#3D3828;--ink-soft:#6B6050;--ink-mute:#9D9080;
  --border:rgba(26,61,43,0.12);
  --radius:16px;--radius-sm:10px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{font-family:'DM Sans',sans-serif;background:var(--warm);color:var(--ink);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:2rem}
.wrap{text-align:center;max-width:420px}
.badge{display:inline-flex;align-items:center;gap:8px;background:var(--rust-pale);border:1px solid rgba(181,64,26,0.2);border-radius:100px;padding:6px 16px;font-size:12px;font-weight:600;color:var(--rust);letter-spacing:0.05em;margin-bottom:1.5rem}
.icon-wrap{width:80px;height:80px;margin:0 auto 1.5rem}
.icon-bg{width:80px;height:80px;border-radius:50%;background:linear-gradient(145deg,#8B2510,var(--rust));display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(181,64,26,0.25)}
.icon-bg svg{width:36px;height:36px;fill:#fff}
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
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
    Error 403
  </div>
  <div class="icon-wrap">
    <div class="icon-bg">
      <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
    </div>
  </div>
  <h1>Akses Ditolak</h1>
  <p>Kamu tidak punya izin untuk mengakses halaman ini.<br>Hubungi pengurus RT jika kamu rasa ini keliru.</p>
  <div class="actions">
    <a href="/" class="btn-primary">← Beranda</a>
    @auth
    <a href="/dashboard" class="btn-secondary">Dashboard</a>
    @endauth
    @guest
    <a href="/login" class="btn-secondary">Login</a>
    @endguest
  </div>
</div>
</body>
</html>
