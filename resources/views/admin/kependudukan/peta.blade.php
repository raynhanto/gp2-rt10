@extends('layouts.admin')
@section('title', 'Peta Warga')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" crossorigin="">
<style>
  #map { height: calc(100vh - 220px); min-height: 480px; border-radius: 16px; border: 1px solid rgba(26,61,43,0.12); overflow: hidden; }
  .peta-panel { width: 272px; flex-shrink: 0; }
  .peta-stat { display:flex;flex-direction:column;align-items:center;padding:0.75rem 1rem;background:#FDFAF2;border-radius:10px;border:1px solid rgba(26,61,43,0.12);flex:1;text-align:center }
  .peta-stat-num { font-family:'DM Serif Display',serif;font-size:1.6rem }
  .peta-stat-label { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#9D9080;margin-top:2px }
  .legend-dot { width:11px;height:11px;border-radius:50%;display:inline-block;flex-shrink:0;border:1.5px solid rgba(0,0,0,0.15) }
  .unplaced-item { display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid rgba(26,61,43,0.08) }
  .unplaced-item:last-child { border-bottom:none }
  .btn-pin { background:none;border:1.5px solid #1A3D2B;color:#1A3D2B;border-radius:6px;padding:3px 10px;font-size:11px;font-weight:600;cursor:pointer;white-space:nowrap;transition:background 0.15s }
  .btn-pin:hover { background:#EBF3EE }
  .btn-pin.active-place { background:#1A3D2B;color:#fff }
  #placing-hint { display:none;position:fixed;bottom:2rem;left:50%;transform:translateX(-50%);background:#1A3D2B;color:#fff;padding:0.6rem 1.25rem;border-radius:999px;font-size:13px;font-weight:600;z-index:9999;pointer-events:none;box-shadow:0 4px 16px rgba(0,0,0,0.2) }
  .toast-msg { display:none;position:fixed;bottom:2rem;right:2rem;background:#1A1810;color:#fff;padding:0.6rem 1.2rem;border-radius:8px;font-size:13px;z-index:9999;box-shadow:0 4px 16px rgba(0,0,0,0.2) }
  /* Custom leaflet marker: override default icon background */
  .peta-pin { width:18px;height:18px;border-radius:50%;border:2.5px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.28);transition:transform 0.1s }
  .peta-pin:hover { transform:scale(1.2) }
  .leaflet-container { cursor: default !important; }
  .leaflet-container.placing-cursor { cursor: crosshair !important; }
</style>
@endsection

@section('content')
<div class="container">

  {{-- Header --}}
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem">
    <div>
      <div class="section-title" style="font-size:1.8rem">Peta Warga</div>
      <div style="font-size:13px;color:#6B6050;margin-top:4px">Sebaran unit rumah RT 10 Golden Park 2</div>
    </div>
    <a href="/admin/kependudukan" style="font-size:13px;color:#6B6050;text-decoration:none">
      <i class="fa fa-arrow-left"></i> Kembali
    </a>
  </div>

  {{-- Stats strip --}}
  <div style="display:flex;gap:0.75rem;margin-bottom:1.25rem">
    <div class="peta-stat">
      <div class="peta-stat-num" id="stat-total" style="color:#1A1810">—</div>
      <div class="peta-stat-label">Total Unit</div>
    </div>
    <div class="peta-stat">
      <div class="peta-stat-num" id="stat-terdaftar" style="color:#27ae60">—</div>
      <div class="peta-stat-label">Ada Warga</div>
    </div>
    <div class="peta-stat">
      <div class="peta-stat-num" id="stat-kosong" style="color:#e67e22">—</div>
      <div class="peta-stat-label">Belum Ada KK</div>
    </div>
    <div class="peta-stat">
      <div class="peta-stat-num" id="stat-nopin" style="color:#9D9080">—</div>
      <div class="peta-stat-label">Belum Dipetakan</div>
    </div>
  </div>

  {{-- Legend --}}
  <div style="display:flex;gap:1.25rem;align-items:center;margin-bottom:1rem;flex-wrap:wrap">
    <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:#6B6050">
      <span class="legend-dot" style="background:#27ae60"></span> Warga terdaftar
    </div>
    <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:#6B6050">
      <span class="legend-dot" style="background:#e67e22"></span> Unit ada, belum ada KK
    </div>
    <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:#6B6050">
      <span class="legend-dot" style="background:#95a5a6"></span> Belum dipasang pin
    </div>
    <div style="margin-left:auto;font-size:12px;color:#9D9080">
      <i class="fa fa-info-circle"></i> Klik pin untuk detail warga
    </div>
  </div>

  {{-- Main layout --}}
  <div style="display:flex;gap:1rem;align-items:flex-start">

    {{-- Map --}}
    <div style="flex:1;min-width:0">
      {{-- Address search --}}
      <div style="display:flex;gap:6px;margin-bottom:8px">
        <input id="alamat-input" type="text" placeholder="Cari alamat atau nama jalan..." autocomplete="off"
          style="flex:1;padding:8px 12px;border-radius:8px;border:1.5px solid rgba(26,61,43,0.2);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;background:#fff"
          onkeydown="if(event.key==='Enter')cariAlamat()"
          onfocus="this.style.borderColor='#1A3D2B'" onblur="this.style.borderColor='rgba(26,61,43,0.2)'">
        <button onclick="cariAlamat()" id="btn-cari"
          style="padding:8px 16px;border-radius:8px;background:#1A3D2B;color:#fff;border:none;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:'DM Sans',sans-serif">
          <i class="fa fa-magnifying-glass"></i> Cari
        </button>
        <button onclick="fitArea()" title="Fit ke area semua pin"
          style="padding:8px 12px;border-radius:8px;background:#fff;color:#1A3D2B;border:1.5px solid rgba(26,61,43,0.2);font-size:13px;cursor:pointer"
          id="btn-fit">
          <i class="fa fa-expand"></i>
        </button>
      </div>
      <div id="search-results" style="position:relative;z-index:1000"></div>
      <div id="map"></div>
    </div>

    {{-- Side panel --}}
    <div class="peta-panel">

      {{-- Edit mode --}}
      <div class="card" style="margin-bottom:1rem;padding:1rem">
        <div style="font-size:12px;color:#6B6050;margin-bottom:0.6rem">
          Mode Edit: geser pin yang sudah ada untuk pindahkan lokasi.
        </div>
        <button id="btn-edit" onclick="toggleEditMode()"
          style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:0.55rem 1rem;border-radius:8px;border:1.5px solid #1A3D2B;background:none;color:#1A3D2B;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s">
          <i class="fa fa-arrows-up-down-left-right" id="btn-edit-icon"></i>
          <span id="btn-edit-label">Mode Edit Koordinat</span>
        </button>
      </div>

      {{-- Unplaced units --}}
      <div class="card" style="padding:1rem">
        <div style="font-size:13px;font-weight:600;color:#1A1810;margin-bottom:0.75rem">
          <i class="fa fa-map-pin" style="color:#9D9080;margin-right:4px"></i>
          Unit Belum Dipetakan
          <span id="unplaced-count" style="font-size:11px;font-weight:400;color:#9D9080;margin-left:4px"></span>
        </div>
        <div id="unplaced-list" style="max-height:calc(100vh - 520px);min-height:80px;overflow-y:auto">
          <div style="text-align:center;padding:1.5rem 0;color:#9D9080;font-size:13px">
            <i class="fa fa-circle-notch fa-spin"></i> Memuat...
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

<div id="placing-hint"></div>
<div class="toast-msg" id="toast"></div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" crossorigin=""></script>
<script>
(function () {
  'use strict';

  if (typeof L === 'undefined') {
    document.getElementById('map').innerHTML =
      '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#e67e22;font-size:13px;flex-direction:column;gap:8px">'
      + '<i class="fa fa-triangle-exclamation" style="font-size:2rem"></i>'
      + '<div>Gagal memuat library peta (Leaflet). Periksa koneksi internet.</div>'
      + '</div>';
    document.getElementById('unplaced-list').innerHTML =
      '<div style="text-align:center;padding:1rem;color:#e67e22;font-size:12px">Library tidak tersedia</div>';
    return;
  }

  const _csrf = '{{ csrf_token() }}';
  const PIN = { green: '#27ae60', orange: '#e67e22' };

  let editMode = false;
  let placingUnitId = null;
  let allUnits = [];
  const markers = {};

  // ── Icon factory ─────────────────────────────────────────────
  function makeIcon(color) {
    return L.divIcon({
      className: '',
      html: '<div class="peta-pin" style="background:' + color + '"></div>',
      iconSize: [18, 18],
      iconAnchor: [9, 9],
      popupAnchor: [0, -14],
    });
  }

  // ── Map init — default: Serang, Banten, zoom 17 ─────────────
  const map = L.map('map', { zoomControl: true }).setView([-6.1174, 106.1505], 17);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
  }).addTo(map);

  // Click on map: place pin
  map.on('click', function (e) {
    if (placingUnitId === null) return;
    const unit = allUnits.find(function (u) { return u.id === placingUnitId; });
    if (!unit) return;

    unit.lat    = e.latlng.lat;
    unit.lng    = e.latlng.lng;
    unit.has_pin = true;

    placeMarker(unit);
    saveCoords(unit.id, e.latlng.lat, e.latlng.lng, unit.label);

    const el = document.getElementById('unplaced-' + unit.id);
    if (el) el.remove();

    // update count badge
    const remaining = document.querySelectorAll('[id^="unplaced-"]').length;
    document.getElementById('unplaced-count').textContent = remaining ? '(' + remaining + ')' : '';
    if (!remaining) {
      document.getElementById('unplaced-list').innerHTML =
        '<div style="text-align:center;padding:1.25rem 0;color:#9D9080;font-size:12px"><i class="fa fa-check-circle" style="color:#27ae60"></i> Semua unit sudah dipetakan</div>';
    }

    stopPlace();
    updateStats();
  });

  // ── Place marker ─────────────────────────────────────────────
  function placeMarker(unit) {
    const color = unit.has_warga ? PIN.green : PIN.orange;

    if (markers[unit.id]) {
      markers[unit.id].setLatLng([unit.lat, unit.lng]);
      markers[unit.id].setIcon(makeIcon(color));
      markers[unit.id].unbindPopup();
      markers[unit.id].bindPopup(buildPopup(unit), { minWidth: 190 });
      return;
    }

    const m = L.marker([unit.lat, unit.lng], {
      icon: makeIcon(color),
      draggable: false,
    }).addTo(map);

    m.bindPopup(buildPopup(unit), { minWidth: 190 });
    m._unitId = unit.id;

    m.on('dragend', function (e) {
      const pos = e.target.getLatLng();
      unit.lat = pos.lat;
      unit.lng = pos.lng;
      saveCoords(unit.id, pos.lat, pos.lng, unit.label);
    });

    markers[unit.id] = m;
  }

  // ── Popup ─────────────────────────────────────────────────────
  function buildPopup(unit) {
    const stMap = { tetap: 'Tetap', kontrak: 'Kontrak', kos: 'Kos', lainnya: 'Lainnya' };
    const body = unit.has_warga
      ? '<div style="font-weight:600;color:#1A1810;margin-bottom:2px">' + escHtml(unit.kk_nama) + '</div>'
        + '<div style="font-size:11px;color:#6B6050">' + (stMap[unit.status_tinggal] || unit.status_tinggal || '') + '</div>'
      : '<div style="color:#e67e22;font-size:12px">Belum ada data warga</div>';

    const link = unit.kk_id
      ? '<a href="/admin/kependudukan/warga/' + unit.kk_id + '" style="display:inline-block;margin-top:8px;font-size:12px;color:#1A3D2B;font-weight:600;text-decoration:none">Lihat Detail <i class="fa fa-arrow-right"></i></a>'
      : '<a href="/admin/kependudukan/warga?tambah=1" style="display:inline-block;margin-top:8px;font-size:12px;color:#9D9080;text-decoration:none">+ Tambah KK</a>';

    return '<div style="font-family:\'DM Sans\',sans-serif;padding:2px">'
      + '<div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#1A3D2B;margin-bottom:6px">' + escHtml(unit.label) + '</div>'
      + body + link + '</div>';
  }

  function escHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ── Save coords ───────────────────────────────────────────────
  async function saveCoords(id, lat, lng, label) {
    try {
      const r = await fetch('/api/kependudukan/units/' + id + '/coordinates', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
        body: JSON.stringify({ lat: lat, lng: lng }),
      });
      const j = await r.json();
      showToast(j.success ? 'Pin ' + label + ' disimpan' : ('Gagal: ' + (j.message || 'Error')));
    } catch (err) {
      showToast('Gagal menyimpan koordinat');
    }
  }

  // ── Edit mode ─────────────────────────────────────────────────
  window.toggleEditMode = function () {
    editMode = !editMode;
    const btn = document.getElementById('btn-edit');
    const lbl = document.getElementById('btn-edit-label');
    if (editMode) {
      btn.style.background = '#1A3D2B';
      btn.style.color = '#fff';
      lbl.textContent = 'Selesai Edit';
      Object.keys(markers).forEach(function (id) { markers[id].dragging.enable(); });
      showToast('Mode edit aktif — geser pin untuk pindahkan posisi');
    } else {
      btn.style.background = '';
      btn.style.color = '#1A3D2B';
      lbl.textContent = 'Mode Edit Koordinat';
      Object.keys(markers).forEach(function (id) { markers[id].dragging.disable(); });
    }
  };

  // ── Place from panel ─────────────────────────────────────────
  window.startPlace = function (id, label) {
    if (placingUnitId === id) { stopPlace(); return; }

    // reset previous active btn
    if (placingUnitId !== null) {
      const prev = document.getElementById('pin-btn-' + placingUnitId);
      if (prev) prev.classList.remove('active-place');
    }

    placingUnitId = id;
    document.getElementById('map').classList.add('placing-cursor');
    showPlacingHint('Klik peta untuk menempatkan: ' + label);

    const btn = document.getElementById('pin-btn-' + id);
    if (btn) btn.classList.add('active-place');
  };

  function stopPlace() {
    if (placingUnitId !== null) {
      const btn = document.getElementById('pin-btn-' + placingUnitId);
      if (btn) btn.classList.remove('active-place');
    }
    placingUnitId = null;
    document.getElementById('map').classList.remove('placing-cursor');
    hidePlacingHint();
  }

  function showPlacingHint(msg) {
    const el = document.getElementById('placing-hint');
    el.textContent = msg;
    el.style.display = 'block';
  }
  function hidePlacingHint() {
    document.getElementById('placing-hint').style.display = 'none';
  }

  // ── Unplaced list ─────────────────────────────────────────────
  function renderUnplaced(units) {
    const el  = document.getElementById('unplaced-list');
    const cnt = document.getElementById('unplaced-count');
    cnt.textContent = units.length ? '(' + units.length + ')' : '';

    if (!units.length) {
      el.innerHTML = '<div style="text-align:center;padding:1.25rem 0;color:#9D9080;font-size:12px"><i class="fa fa-check-circle" style="color:#27ae60"></i> Semua unit sudah dipetakan</div>';
      return;
    }

    el.innerHTML = units.map(function (u) {
      const sub = u.has_warga
        ? '<div style="font-size:11px;color:#6B6050">' + escHtml(u.kk_nama) + '</div>'
        : '<div style="font-size:11px;color:#e67e22">Belum ada KK</div>';
      return '<div class="unplaced-item" id="unplaced-' + u.id + '">'
        + '<div><div style="font-size:12.5px;color:#1A1810;font-weight:500">' + escHtml(u.label) + '</div>' + sub + '</div>'
        + '<button class="btn-pin" id="pin-btn-' + u.id + '" onclick="startPlace(' + u.id + ', \'' + escHtml(u.label) + '\')">'
        + '<i class="fa fa-map-pin"></i> Pin</button>'
        + '</div>';
    }).join('');
  }

  // ── Stats ─────────────────────────────────────────────────────
  function updateStats() {
    document.getElementById('stat-total').textContent     = allUnits.length;
    document.getElementById('stat-terdaftar').textContent = allUnits.filter(function (u) { return u.has_warga; }).length;
    document.getElementById('stat-kosong').textContent    = allUnits.filter(function (u) { return !u.has_warga && u.has_pin; }).length;
    document.getElementById('stat-nopin').textContent     = allUnits.filter(function (u) { return !u.has_pin; }).length;
  }

  // ── Toast ─────────────────────────────────────────────────────
  let _toastTimer;
  function showToast(msg) {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.style.display = 'block';
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(function () { el.style.display = 'none'; }, 2800);
  }

  // ── Load data ─────────────────────────────────────────────────
  async function loadMap() {
    let j;
    try {
      const r = await fetch('/api/kependudukan/map');
      j = await r.json();
    } catch (err) {
      document.getElementById('unplaced-list').innerHTML =
        '<div style="text-align:center;padding:1rem;color:#e67e22;font-size:12px"><i class="fa fa-triangle-exclamation"></i> Gagal memuat data</div>';
      return;
    }

    if (!j.success) {
      document.getElementById('unplaced-list').innerHTML =
        '<div style="text-align:center;padding:1rem;color:#e67e22;font-size:12px"><i class="fa fa-triangle-exclamation"></i> ' + (j.message || 'Error') + '</div>';
      return;
    }

    allUnits = j.data;
    const unplaced = [];

    allUnits.forEach(function (unit) {
      if (unit.has_pin) placeMarker(unit);
      else unplaced.push(unit);
    });

    renderUnplaced(unplaced);
    updateStats();

    // Auto-fit if there are pins
    const placed = allUnits.filter(function (u) { return u.has_pin; });
    if (placed.length > 0) {
      try {
        map.fitBounds(
          L.latLngBounds(placed.map(function (u) { return [u.lat, u.lng]; })).pad(0.2)
        );
      } catch (e) { /* fitBounds may fail on single point edge case */ }
    }
  }

  loadMap();

}());
</script>
@endsection
