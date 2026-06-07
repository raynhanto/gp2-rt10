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
.ev-title{font-size:20px;font-weight:700;color:#1a3d2b;margin-bottom:20px}
.row{display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;font-size:14px;color:#444}
.icon{font-size:16px;flex-shrink:0;width:20px;text-align:center}
.badge{display:inline-block;font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px;text-transform:capitalize;margin-bottom:20px}
.badge-rapat{background:#EBF3EE;color:#1a3d2b}
.badge-kegiatan{background:#FBF3DC;color:#856404}
.badge-sosial{background:#FDECEA;color:#C0392B}
.badge-olahraga{background:#EAF4FF;color:#1A5CA8}
.badge-lainnya{background:#EFEFED;color:#666}
.desc{font-size:13px;color:#555;line-height:1.6;background:#f8f8f6;border-radius:6px;padding:14px 16px;margin-bottom:20px}
.btn{display:block;text-align:center;background:#1a3d2b;color:#fff;padding:13px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600}
.footer{padding:20px 40px;text-align:center;font-size:11px;color:#aaa}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="header-title">RT 10 Golden Park 2</div>
    <div class="header-sub">Agenda Kegiatan</div>
  </div>
  <div class="body">
    <div class="ev-title">{{ $agenda->judul }}</div>
    <span class="badge badge-{{ $agenda->kategori }}">{{ $agenda->kategori }}</span>

    @php
      $tgl = \Carbon\Carbon::parse($agenda->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
      $waktu = $agenda->waktu_mulai ? substr($agenda->waktu_mulai, 0, 5) : null;
      if ($waktu && $agenda->waktu_selesai) $waktu .= ' – ' . substr($agenda->waktu_selesai, 0, 5);
    @endphp

    <div class="row"><span class="icon">📆</span> <span>{{ $tgl }}</span></div>
    @if($waktu)
    <div class="row"><span class="icon">🕐</span> <span>{{ $waktu }} WIB</span></div>
    @endif
    @if($agenda->lokasi)
    <div class="row"><span class="icon">📍</span> <span>{{ $agenda->lokasi }}</span></div>
    @endif

    @if($agenda->deskripsi)
    <div class="desc">{{ $agenda->deskripsi }}</div>
    @endif

    <a href="{{ config('app.url') }}/agenda" class="btn">Lihat Agenda Lengkap</a>
  </div>
  <div class="footer">
    Email ini dikirim oleh pengurus RT 10 Golden Park 2, Serang, Banten.<br>
    Jika ada pertanyaan, hubungi pengurus RT.
  </div>
</div>
</body>
</html>
