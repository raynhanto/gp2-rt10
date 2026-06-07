@extends('layouts.app')
@section('title', 'Detail Kampanye')
@section('content')
<main style="padding:5rem 0 3rem">
<div class="container" style="max-width:860px">

  <div style="margin-bottom:2rem">
    <a href="/kampanye" style="font-size:13px;color:var(--ink-soft)">← Semua Kampanye</a>
  </div>

  <div id="kampanye-detail"><div style="text-align:center;padding:4rem;color:var(--ink-soft)">Memuat...</div></div>

</div>
</main>
@endsection
@section('scripts')
<script>
const kampanyeId = {{ (int)$kampanyeId }};

async function loadDetail() {
  const res = await fetch('/api/kampanye/' + kampanyeId);
  const data = await res.json();
  if (!data.success) {
    document.getElementById('kampanye-detail').innerHTML = '<div style="text-align:center;padding:4rem;color:var(--rust)">Kampanye tidak ditemukan.</div>';
    return;
  }
  const k = data.data;
  const pct = Math.min(100, Math.round(k.terkumpul / k.target * 100));
  const badgeClass = k.status==='urgent' ? 'badge-urgent' : pct>=90 ? 'badge-nearly' : k.status==='selesai' ? 'badge-done' : 'badge-open';
  const badgeText  = k.status==='urgent' ? 'Mendesak' : pct>=90 ? 'Hampir tercapai' : k.status==='selesai' ? 'Selesai' : 'Berjalan';
  const dl = k.deadline ? new Date(k.deadline).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'}) : null;

  document.getElementById('kampanye-detail').innerHTML = `
    <div style="border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);margin-bottom:2rem">
      ${k.foto_url
        ? `<img src="${k.foto_url}" alt="${k.judul}" style="width:100%;max-height:360px;object-fit:cover;display:block">`
        : `<div style="width:100%;height:240px;background:linear-gradient(135deg,#1A3D2B,#3D7A56);display:flex;align-items:center;justify-content:center;font-size:4rem">🏘️</div>`}
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;align-items:start">
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:1rem">
          <span class="badge ${badgeClass}">${badgeText}</span>
        </div>
        <h1 style="font-family:'DM Serif Display',serif;font-size:2rem;color:var(--forest);line-height:1.25;margin-bottom:1.25rem">${k.judul}</h1>
        <div style="font-size:15px;color:var(--ink-mid);line-height:1.8">${k.deskripsi}</div>
      </div>

      <div class="card" style="position:sticky;top:5rem">
        <div class="progress-track" style="margin-bottom:10px"><div class="progress-fill pf-green" style="width:${pct}%"></div></div>
        <div style="font-family:'DM Serif Display',serif;font-size:1.6rem;color:var(--forest);margin-bottom:4px">Rp ${parseInt(k.terkumpul).toLocaleString('id-ID')}</div>
        <div style="font-size:13px;color:var(--ink-soft);margin-bottom:1.25rem">${pct}% dari target Rp ${parseInt(k.target).toLocaleString('id-ID')}</div>
        ${dl ? `<div style="font-size:12px;color:var(--ink-soft);margin-bottom:1.25rem"><i class="fa fa-calendar" style="margin-right:5px;color:var(--gold)"></i>Deadline: ${dl}</div>` : ''}
        ${k.status !== 'selesai' && k.status !== 'arsip'
          ? `<a href="/donasi?kampanye=${k.id}" class="btn-primary" style="display:flex;width:100%;justify-content:center;padding:14px">Donasi Sekarang →</a>`
          : `<div style="padding:12px;background:var(--cream);border-radius:var(--radius-sm);text-align:center;font-size:13px;color:var(--ink-soft)">Kampanye ini sudah selesai</div>`}
      </div>
    </div>`;
}

loadDetail();
</script>
@endsection
