<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
body{font-family:'Segoe UI',Arial,sans-serif;background:#f4f4f2;margin:0;padding:0}
.wrap{max-width:560px;margin:40px auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08)}
.header{background:#1a3d2b;padding:32px 40px;text-align:center}
.header-title{color:#fff;font-size:22px;font-weight:700;margin:0}
.header-sub{color:rgba(255,255,255,.65);font-size:13px;margin-top:4px}
.body{padding:32px 40px}
.pengumuman-judul{font-size:20px;font-weight:700;color:#1a3d2b;margin-bottom:16px}
.pengumuman-isi{font-size:14px;color:#444;line-height:1.7;white-space:pre-line;background:#f8f8f6;border-radius:8px;padding:18px 20px;margin-bottom:24px}
.btn{display:block;text-align:center;background:#1a3d2b;color:#fff;padding:13px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600}
.footer{padding:20px 40px;text-align:center;font-size:11px;color:#aaa}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="header-title">RT 10 Golden Park 2</div>
    <div class="header-sub">Pengumuman Resmi</div>
  </div>
  <div class="body">
    <div class="pengumuman-judul">{{ $pengumuman->judul }}</div>
    <div class="pengumuman-isi">{{ $pengumuman->isi }}</div>
    <a href="{{ config('app.url') }}/informasi" class="btn">Lihat Info RT Lengkap</a>
  </div>
  <div class="footer">
    Email ini dikirim oleh pengurus RT 10 Golden Park 2, Serang, Banten.<br>
    Jika ada pertanyaan, hubungi pengurus RT.
  </div>
</div>
</body>
</html>
