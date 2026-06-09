@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')
<div class="container">
  <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;align-items:flex-end;margin-bottom:2.5rem">
    <div>
      <div class="section-label">Admin Panel</div>
      <div class="section-title" style="font-size:1.8rem">Dashboard Pengurus RT 10</div>
    </div>
    <div style="display:flex;gap:8px">
      <a href="/admin/kampanye" class="btn-secondary" style="font-size:13px;padding:8px 16px">+ Kampanye Baru</a>
      <a href="/admin/pengumuman" class="btn-primary" style="font-size:13px;padding:8px 16px"><i class="fa-solid fa-bullhorn" style="margin-right:6px"></i>Kirim Pengumuman</a>
    </div>
  </div>

  <div class="stat-grid-4" style="margin-bottom:2.5rem">
    @foreach(['admin-saldo'=>['Saldo Kas','#E8F4ED','var(--forest)'],'admin-masuk'=>['Pemasukan Bulan Ini','var(--gold-pale)','#7A5C00'],'admin-keluar'=>['Pengeluaran Bulan Ini','#FDECEA','var(--rust)'],'admin-pending'=>['Menunggu Verifikasi','#E8F0FD','#2D5AA8']] as $id=>[$lbl,$bg,$col])
    <div style="background:{{ $bg }};border-radius:var(--radius-sm);padding:1.25rem">
      <div style="font-size:12px;color:var(--ink-soft);margin-bottom:6px">{{ $lbl }}</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:{{ $col }}" id="{{ $id }}">—</div>
    </div>
    @endforeach
  </div>

  <div class="split-3-2">
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
        <div style="font-size:15px;font-weight:500">Donasi menunggu verifikasi</div>
        <a href="/admin/verifikasi" style="font-size:13px;color:var(--forest-light)">Lihat semua →</a>
      </div>
      <div id="pending-list"><div style="text-align:center;padding:1.5rem;color:var(--ink-soft)">Memuat...</div></div>
    </div>

    <div style="display:flex;flex-direction:column;gap:1rem">
      @foreach([['/admin/verifikasi','fa-solid fa-circle-check','Verifikasi Donasi','Approve bukti transfer masuk'],['/admin/kampanye','fa-solid fa-bullseye','Kelola Kampanye','Buat & edit kampanye'],['/admin/keuangan','fa-solid fa-wallet','Keuangan Lengkap','Kas, iuran, anggaran & laporan'],['/admin/pengeluaran','fa-solid fa-receipt','Catat Pengeluaran','Input nota & struk'],['/admin/warga','fa-solid fa-users','Data Warga','Daftar & info warga'],['/admin/pengumuman','fa-solid fa-bullhorn','Pengumuman','Kirim info ke warga'],['/admin/aktivitas','fa-solid fa-magnifying-glass','Log Aktivitas','Rekam jejak pengurus RT']] as [$url,$icon,$title,$sub])
      <a href="{{ $url }}" style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:#fff;border:1px solid var(--border);border-radius:var(--radius-sm);transition:all 0.15s;text-decoration:none" onmouseenter="this.style.borderColor='var(--forest-light)';this.style.background='#F7FBF8'" onmouseleave="this.style.borderColor='var(--border)';this.style.background='#fff'">
        <span style="font-size:16px;width:20px;text-align:center;color:var(--forest-mid)"><i class="{{ $icon }}"></i></span>
        <div><div style="font-size:13px;font-weight:500;color:var(--ink)">{{ $title }}</div><div style="font-size:11px;color:var(--ink-soft)">{{ $sub }}</div></div>
        <i class="fa fa-chevron-right" style="margin-left:auto;font-size:11px;color:var(--sand)"></i>
      </a>
      @endforeach
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
async function loadAdmin() {
  try {
    const [kasRes, donasiRes] = await Promise.all([
      fetch('/api/kas/summary'),
      fetch('/api/donasi?status=pending')
    ]);
    const kasData = await kasRes.json();
    const donasiData = await donasiRes.json();

    if (kasData.success) {
      const {saldo,masuk_bulan,keluar_bulan} = kasData.data;
      document.getElementById('admin-saldo').textContent = 'Rp '+(parseInt(saldo||0)).toLocaleString('id-ID');
      document.getElementById('admin-masuk').textContent = 'Rp '+(parseInt(masuk_bulan||0)).toLocaleString('id-ID');
      document.getElementById('admin-keluar').textContent = 'Rp '+(parseInt(keluar_bulan||0)).toLocaleString('id-ID');
    }

    if (donasiData.success) {
      const pending = donasiData.data;
      document.getElementById('admin-pending').textContent = pending.length;
      document.getElementById('pending-list').innerHTML = !pending.length
        ? '<div style="text-align:center;padding:1.5rem;color:var(--ink-soft)">Tidak ada donasi pending.</div>'
        : pending.slice(0,5).map(d => {
          const tgl = new Date(d.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'short'});
          return `<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
            <div>
              <div style="font-size:13px;font-weight:500;color:var(--ink)">${d.nama||'Warga'}</div>
              <div style="font-size:12px;color:var(--ink-soft)">${d.judul||''} · ${tgl}</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <span style="font-size:13px;font-weight:600;color:var(--forest)">Rp ${parseInt(d.nominal).toLocaleString('id-ID')}</span>
              <a href="/admin/verifikasi?id=${d.id}" style="font-size:12px;padding:4px 10px;background:var(--forest);color:#fff;border-radius:99px">Cek</a>
            </div>
          </div>`;
        }).join('');
    }
  } catch(e) { console.error(e); }
}
loadAdmin();
</script>
@endsection
