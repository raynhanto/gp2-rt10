@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
@php
  $whitelistRaw = config('app.iuran_whitelist', '');
  $iuranAllowed = !empty($whitelistRaw) && in_array(auth()->user()->email, array_filter(array_map('trim', explode(',', $whitelistRaw))), true);
@endphp
<main style="padding:5rem 0 3rem">
<div class="container" style="max-width:800px">
  <div style="margin-bottom:2.5rem">
    <div style="font-size:13px;color:var(--ink-soft);margin-bottom:6px">Selamat datang kembali,</div>
    <div style="font-family:'DM Serif Display',serif;font-size:2rem;color:var(--forest)">{{ auth()->user()->nama ?? 'Warga' }} 👋</div>
  </div>

  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem">
    @foreach(['total-donasi'=>['💰','Total Donasiku'],'donasi-count'=>['🎯','Jumlah Donasi'],'kampanye-count'=>['📋','Kampanye Didukung']] as $id=>[$icon,$lbl])
    <div style="background:var(--cream);border-radius:var(--radius-sm);padding:1.25rem;text-align:center">
      <div style="font-size:22px;margin-bottom:6px">{{ $icon }}</div>
      <div style="font-family:'DM Serif Display',serif;font-size:1.5rem;color:var(--forest)" id="{{ $id }}">—</div>
      <div style="font-size:12px;color:var(--ink-soft);margin-top:4px">{{ $lbl }}</div>
    </div>
    @endforeach
  </div>

  <div id="pending-banner" style="display:none;background:var(--gold-pale);border:1px solid var(--border-gold);border-radius:var(--radius-sm);padding:1rem 1.25rem;margin-bottom:1.25rem;display:none">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem">
      <div>
        <div style="font-size:14px;font-weight:500;color:var(--ink-mid)">💳 Ada donasi yang menunggu pembayaran</div>
        <div style="font-size:12px;color:var(--ink-soft);margin-top:2px" id="pending-banner-sub">Selesaikan pembayaran untuk melanjutkan.</div>
      </div>
      <a id="pending-banner-link" href="/dashboard/riwayat" style="flex-shrink:0;padding:8px 18px;background:var(--forest);color:#fff;border-radius:99px;font-size:13px;font-weight:500">Selesaikan →</a>
    </div>
  </div>

  @if($iuranAllowed)
  <div id="iuran-card" class="card" style="margin-bottom:1.25rem;display:none">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
      <div style="font-size:15px;font-weight:500">💵 Iuran Bulanan</div>
      <a href="/dashboard/iuran" style="font-size:13px;color:var(--forest-light)">Lihat semua →</a>
    </div>
    <div id="iuran-summary"><div style="font-size:13px;color:var(--ink-soft)">Memuat...</div></div>
  </div>
  @endif

  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
      <div style="font-size:15px;font-weight:500">Donasi terbaru</div>
      <a href="/dashboard/riwayat" style="font-size:13px;color:var(--forest-light)">Lihat semua →</a>
    </div>
    <div id="riwayat-list"><div style="text-align:center;padding:1.5rem;color:var(--ink-soft)">Memuat...</div></div>
  </div>
</div>
</main>
@endsection
@section('scripts')
<script>
async function loadDashboard() {
  const res = await fetch('/api/user/donasi');
  const data = await res.json();
  if (!data.success) return;
  const list = data.data;
  const total = list.filter(d => d.status === 'verified').reduce((s, d) => s + parseInt(d.nominal), 0);
  const kampanye = [...new Set(list.map(d => d.kampanye_id).filter(id => id !== null))].length;
  document.getElementById('total-donasi').textContent = 'Rp ' + total.toLocaleString('id-ID');
  document.getElementById('donasi-count').textContent = list.length;
  document.getElementById('kampanye-count').textContent = kampanye;

  const pending = list.filter(d => d.status === 'pending');
  if (pending.length) {
    const banner = document.getElementById('pending-banner');
    banner.style.display = 'block';
    document.getElementById('pending-banner-sub').textContent =
      pending.length === 1
        ? `1 donasi menunggu pembayaran.`
        : `${pending.length} donasi menunggu pembayaran.`;
    document.getElementById('pending-banner-link').href = `/dashboard/bayar?donasi=${pending[0].id}`;
  }

  const statusColor = { pending: '#7A5C00', verified: 'var(--forest)', rejected: 'var(--rust)' };
  const statusBg    = { pending: 'var(--gold-pale)', verified: '#E8F4ED', rejected: '#FDECEA' };
  const statusLabel = { pending: 'Menunggu', verified: 'Terverifikasi', rejected: 'Ditolak' };
  document.getElementById('riwayat-list').innerHTML = !list.length
    ? '<div style="text-align:center;padding:1.5rem;color:var(--ink-soft)">Belum ada donasi.</div>'
    : list.slice(0, 5).map(d => {
        const tgl = new Date(d.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        const sc = statusColor[d.status] || 'var(--ink-soft)';
        const sb = statusBg[d.status] || '#eee';
        const href = d.status === 'pending' ? `/dashboard/bayar?donasi=${d.id}` : '/dashboard/riwayat';
        return `<a href="${href}" style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);text-decoration:none;color:inherit;">
          <div><div style="font-size:13px;font-weight:500;color:var(--ink)">${d.judul || 'Kampanye'}</div><div style="font-size:12px;color:var(--ink-soft)">${tgl}</div></div>
          <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:3px">
            <div style="font-size:13px;font-weight:600;color:var(--forest)">Rp ${parseInt(d.nominal).toLocaleString('id-ID')}</div>
            <span style="font-size:11px;padding:2px 8px;border-radius:99px;background:${sb};color:${sc}">${statusLabel[d.status] || d.status}</span>
            ${d.status === 'pending' ? `<span style="font-size:11px;color:var(--forest);font-weight:500">Selesaikan →</span>` : ''}
          </div>
        </a>`;
      }).join('');
}
loadDashboard();

@if($iuranAllowed)
async function loadIuranSummary() {
  try {
    const res  = await fetch(`/api/iuran/warga/tagihan?tahun={{ date('Y') }}`);
    const data = await res.json();
    if (!data.success || !data.data.length) return;
    const rows   = data.data;
    const belum  = rows.filter(r => r.status === 'belum').length;
    const pending = rows.filter(r => r.status === 'pending').length;
    const lunas  = rows.filter(r => r.status === 'lunas').length;
    const card = document.getElementById('iuran-card');
    card.style.display = 'block';
    let parts = [];
    if (belum)   parts.push(`<span style="color:var(--rust);font-weight:600">${belum} belum bayar</span>`);
    if (pending) parts.push(`<span style="color:#856404;font-weight:600">${pending} menunggu</span>`);
    if (lunas)   parts.push(`<span style="color:var(--forest);font-weight:600">${lunas} lunas</span>`);
    document.getElementById('iuran-summary').innerHTML = parts.length
      ? `<div style="font-size:13px;color:var(--ink-mid)">${parts.join(' · ')} — <a href="/dashboard/iuran" style="color:var(--forest-light)">Lihat detail</a></div>`
      : `<div style="font-size:13px;color:var(--ink-soft)">Tidak ada tagihan tahun ini.</div>`;
  } catch (e) {}
}
loadIuranSummary();
@endif
</script>
@endsection
