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
.status-badge{display:inline-flex;align-items:center;gap:8px;font-size:15px;font-weight:700;padding:10px 20px;border-radius:99px;margin-bottom:24px}
.status-verified{background:#EBF3EE;color:#1a3d2b}
.status-rejected{background:#FDECEA;color:#C0392B}
.amount{font-size:28px;font-weight:800;color:#1a3d2b;margin-bottom:4px}
.label{font-size:12px;color:#888;text-transform:uppercase;letter-spacing:.05em;margin-bottom:20px}
.info-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f0f0ee;font-size:13px}
.info-key{color:#888}
.info-val{font-weight:600;color:#333;text-align:right}
.catatan-box{background:#FFF8DC;border-left:3px solid #C8A030;padding:12px 16px;border-radius:0 6px 6px 0;margin:20px 0;font-size:13px;color:#555;line-height:1.6}
.btn{display:block;text-align:center;background:#1a3d2b;color:#fff;padding:13px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;margin-top:24px}
.footer{padding:20px 40px;text-align:center;font-size:11px;color:#aaa}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="header-title">RT 10 Golden Park 2</div>
    <div class="header-sub">Konfirmasi Donasi</div>
  </div>
  <div class="body">
    @if($donasi->status === 'verified')
      <div class="status-badge status-verified">&#10003; Donasi Diverifikasi</div>
      <p style="font-size:14px;color:#444;margin-bottom:24px">
        Halo <strong>{{ $donasi->user?->nama ?? 'Donatur' }}</strong>,<br>
        donasi Anda telah <strong>diverifikasi</strong> oleh pengurus RT. Terima kasih atas kontribusi Anda!
      </p>
    @else
      <div class="status-badge status-rejected">&#215; Donasi Ditolak</div>
      <p style="font-size:14px;color:#444;margin-bottom:24px">
        Halo <strong>{{ $donasi->user?->nama ?? 'Donatur' }}</strong>,<br>
        mohon maaf, donasi Anda <strong>tidak dapat diverifikasi</strong>. Silakan hubungi pengurus RT untuk informasi lebih lanjut.
      </p>
    @endif

    <div class="amount">Rp {{ number_format($donasi->nominal, 0, ',', '.') }}</div>
    <div class="label">Nominal Donasi</div>

    <div class="info-row">
      <span class="info-key">Kampanye</span>
      <span class="info-val">{{ $donasi->kampanye?->judul ?? 'Umum' }}</span>
    </div>
    <div class="info-row">
      <span class="info-key">Metode</span>
      <span class="info-val">{{ strtoupper($donasi->metode) }}</span>
    </div>
    <div class="info-row">
      <span class="info-key">Tanggal Verifikasi</span>
      <span class="info-val">{{ \Carbon\Carbon::parse($donasi->verified_at)->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB</span>
    </div>

    @if($donasi->catatan_admin)
    <div class="catatan-box">
      <strong>Catatan pengurus:</strong><br>{{ $donasi->catatan_admin }}
    </div>
    @endif

    <a href="{{ config('app.url') }}/dashboard/riwayat" class="btn">Lihat Riwayat Donasi</a>
  </div>
  <div class="footer">
    Email ini dikirim otomatis oleh sistem RT 10 Golden Park 2, Serang, Banten.<br>
    Jika ada pertanyaan, hubungi pengurus RT.
  </div>
</div>
</body>
</html>
