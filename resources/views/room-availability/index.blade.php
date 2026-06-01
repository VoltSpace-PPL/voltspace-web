@extends('layouts.main')

@section('content')
{{-- Page Header --}}
<div class="flex items-start justify-between mb-8 flex-wrap gap-4">
    <div>
        <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">Room Availability</h1>
        <p class="text-slate-400 text-[14px] mt-2">Monitor and manage room usage status and bookings</p>
    </div>
    {{-- Admin-only: Import Schedule Button --}}
    <div class="flex items-center gap-3">
        <button id="import-schedule-btn" onclick="triggerImport()"
            class="flex items-center gap-2.5 px-5 py-2.5 rounded-xl font-bold text-[14px] transition-all hover:scale-[1.02] active:scale-[0.98]"
            style="background:linear-gradient(135deg,#00d4aa,#0099cc); color:#0b1120; box-shadow:0 4px 20px rgba(0,212,170,0.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import Schedule
        </button>
        {{-- Hidden file input --}}
        <input type="file" id="import-file-input" accept=".xlsx,.xls,.csv,.db" class="hidden" onchange="handleImportFile(this)">
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="rounded-2xl p-5 flex items-center gap-4" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(99,179,237,0.15); border:1px solid rgba(99,179,237,0.2);">
            <svg class="w-6 h-6 text-[#63b3ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Total Rooms</p>
            <p class="text-[30px] font-extrabold text-white leading-none mt-0.5" id="stat-total">—</p>
        </div>
    </div>
    <div class="rounded-2xl p-5 flex items-center gap-4" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(0,212,170,0.15); border:1px solid rgba(0,212,170,0.2);">
            <svg class="w-6 h-6 text-[#00d4aa]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Available</p>
            <p class="text-[30px] font-extrabold text-white leading-none mt-0.5" id="stat-available">—</p>
        </div>
    </div>
    <div class="rounded-2xl p-5 flex items-center gap-4" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(246,173,85,0.15); border:1px solid rgba(246,173,85,0.2);">
            <svg class="w-6 h-6 text-[#f6ad55]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Booked</p>
            <p class="text-[30px] font-extrabold text-white leading-none mt-0.5" id="stat-booked">—</p>
        </div>
    </div>
</div>

{{-- Booking Policy Notice --}}
<div class="mb-6 px-5 py-4 rounded-2xl flex items-start gap-3"
     style="background:rgba(0,170,255,0.08); border:1px solid rgba(0,170,255,0.2);">
    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
         style="background:rgba(0,170,255,0.15);">
        <svg class="w-4 h-4 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
        </svg>
    </div>
    <div>
        <p class="text-[#00aaff] text-[13px] font-bold mb-0.5">Booking Policy</p>
        <p class="text-slate-400 text-[13px] leading-relaxed">Each room can only be booked by ONE person at a time. Once approved, the room will be exclusively theirs during the booking period. Admin can cancel bookings up to H-1 before the event.</p>
    </div>
</div>

{{-- Filter Bar --}}
<div class="flex items-center gap-3 mb-6 flex-wrap">
    <div class="relative flex-1 min-w-[220px]">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/>
        </svg>
        <input id="room-search" type="text" placeholder="Search by room name or building..."
            class="w-full pl-11 pr-4 py-2.5 rounded-xl text-white text-[13px] placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/40 transition-all"
            style="background:#161e2d; border:1px solid #1e2d45;">
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke-width="2"/>
        </svg>
        <select id="status-filter"
            class="px-4 py-2.5 rounded-xl text-white text-[13px] focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/40 transition-all cursor-pointer"
            style="background:#161e2d; border:1px solid #1e2d45;">
            <option value="">All Status</option>
            <option value="tersedia">Available</option>
            <option value="dipesan">Booked</option>
            <option value="digunakan">In Use</option>
        </select>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" stroke-width="2"/>
        </svg>
        <select id="building-filter"
            class="px-4 py-2.5 rounded-xl text-white text-[13px] focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/40 transition-all cursor-pointer"
            style="background:#161e2d; border:1px solid #1e2d45;">
            <option value="">All Buildings</option>
        </select>
    </div>
</div>

{{-- Active Bookings (Booked Rooms) for Admin Cancel --}}
<div id="active-bookings-section" class="mb-8 hidden">
    <h2 class="text-[16px] font-bold text-white mb-4 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>
        Active Bookings (Admin Can Cancel)
    </h2>
    <div id="active-bookings-list" class="space-y-3"></div>
</div>

{{-- Rooms Grid --}}
<div id="rooms-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <div class="col-span-3 flex flex-col items-center justify-center py-20 gap-3">
        <div class="w-8 h-8 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
        <span class="text-slate-500 text-[13px]">Loading rooms...</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    let allRooms = [];
    let allBuildings = [];
    let activeBookings = [];

    /* ── Status config ─────────────────────────────────── */
    const statusCfg = {
        tersedia:  { label: 'Available', cls: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',  dot: 'bg-emerald-400',  card: 'border-emerald-500/10' },
        dipesan:   { label: 'Booked',    cls: 'bg-orange-500/15 text-orange-400 border-orange-500/30',    dot: 'bg-orange-400',   card: 'border-orange-500/10' },
        digunakan: { label: 'In Use',    cls: 'bg-red-500/15 text-red-400 border-red-500/30',             dot: 'bg-red-400',      card: 'border-red-500/10' },
    };

    function statusInfo(s) {
        return statusCfg[s] || { label: s, cls: 'bg-slate-500/15 text-slate-400 border-slate-500/30', dot: 'bg-slate-400', card: '' };
    }

    /* ── Extract building name from room ───────────────── */
    function getBuilding(room) {
        // Try to derive building from kode or nama_ruangan
        const kode = (room.kode || room.id || '').toUpperCase();
        const nama = (room.nama_ruangan || '').trim();
        // Common Tel-U building codes
        const buildingMap = {
            'TUCH': 'TUCH', 'GSG': 'GSG', 'SC': 'Student Center',
            'GKB': 'GKB', 'GKU': 'GKU', 'FIK': 'FIK', 'FEB': 'FEB',
        };
        for (const [key, val] of Object.entries(buildingMap)) {
            if (kode.startsWith(key) || nama.toUpperCase().startsWith(key)) return val;
        }
        // Fallback: use first word of nama_ruangan
        return nama.split(/[\s\-_]/)[0] || 'Other';
    }

    /* ── Render stats ──────────────────────────────────── */
    function updateStats(rooms) {
        document.getElementById('stat-total').textContent     = rooms.length;
        document.getElementById('stat-available').textContent = rooms.filter(r => r.status === 'tersedia').length;
        document.getElementById('stat-booked').textContent    = rooms.filter(r => r.status === 'dipesan' || r.status === 'digunakan').length;
    }

    /* ── Populate building filter ──────────────────────── */
    function populateBuildingFilter(rooms) {
        const buildings = [...new Set(rooms.map(r => getBuilding(r)))].sort();
        const sel = document.getElementById('building-filter');
        const current = sel.value;
        // Keep "All Buildings" option
        sel.innerHTML = '<option value="">All Buildings</option>';
        buildings.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b; opt.textContent = b;
            if (b === current) opt.selected = true;
            sel.appendChild(opt);
        });
        allBuildings = buildings;
    }

    /* ── Render rooms grid ─────────────────────────────── */
    function renderRooms(rooms) {
        const grid = document.getElementById('rooms-grid');
        if (!rooms.length) {
            grid.innerHTML = `<div class="col-span-3 text-center py-20">
                <svg class="w-16 h-16 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/>
                </svg>
                <p class="text-slate-500 text-[15px] font-medium">No rooms found.</p>
            </div>`;
            return;
        }

        grid.innerHTML = rooms.map(r => {
            const s    = statusInfo(r.status);
            const bldg = getBuilding(r);
            // Find active booking for this room
            const booking = activeBookings.find(b => b.ruangan_id === r.id && b.status === 'disetujui');

            return `
<div class="rounded-2xl overflow-hidden transition-all hover:scale-[1.01] hover:shadow-xl group"
     style="background:#161e2d; border:1px solid #1e2d45;">
    <div class="px-5 pt-5 pb-4">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(0,170,255,0.12); border:1px solid rgba(0,170,255,0.2);">
                    <svg class="w-5 h-5 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="text-[15px] font-bold text-white leading-tight truncate">${r.nama_ruangan || 'Room'}</h3>
                    <p class="text-[12px] text-slate-500 mt-0.5">${r.kode || r.id}</p>
                </div>
            </div>
            <span class="flex-shrink-0 flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ${s.cls}">
                <span class="w-1.5 h-1.5 rounded-full ${s.dot}"></span>
                ${s.label}
            </span>
        </div>

        <div class="space-y-2">
            <div class="flex items-center gap-2 text-slate-400 text-[13px]">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/>
                </svg>
                <span>Tel-U Bandung</span>
            </div>
            <div class="flex items-center gap-2 text-slate-400 text-[13px]">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/>
                </svg>
                <span>${r.kapasitas ?? 0} people</span>
            </div>
            ${r.daya ? `<div class="flex items-center gap-2 text-slate-400 text-[13px]">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/>
                </svg>
                <span>${r.daya} VA</span>
            </div>` : ''}
        </div>

        ${booking ? `
        <div class="mt-4 rounded-xl p-3" style="background:rgba(246,173,85,0.08); border:1px solid rgba(246,173,85,0.2);">
            <p class="text-[11px] font-bold text-orange-400 uppercase tracking-wider mb-1.5">Current User</p>
            <p class="text-white text-[13px] font-semibold">${booking.user?.name || 'Unknown User'}</p>
            <p class="text-slate-500 text-[12px] mt-0.5">
                <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                ${(booking.waktu_mulai||'').substring(0,5)} – ${(booking.waktu_selesai||'').substring(0,5)}
            </p>
        </div>
        ` : ''}
    </div>

    ${booking ? `
    <div class="px-5 pb-5 pt-1">
        <button onclick="adminCancelBooking(${booking.id}, '${(r.nama_ruangan||'Room').replace(/'/g,"\\'")}', '${booking.user?.name?.replace(/'/g,"\\'")||''}' )"
            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold text-[13px] transition-all hover:scale-[1.01] active:scale-[0.99]"
            style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#f87171;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Cancel Booking
        </button>
    </div>` : ''}
</div>`;
        }).join('');
    }

    /* ── Filter rooms ──────────────────────────────────── */
    function filterAndRender() {
        const q    = document.getElementById('room-search').value.toLowerCase();
        const st   = document.getElementById('status-filter').value;
        const bldg = document.getElementById('building-filter').value;

        const filtered = allRooms.filter(r => {
            const name = (r.nama_ruangan || '').toLowerCase();
            const kode = (r.kode || r.id || '').toLowerCase();
            const b    = getBuilding(r);
            const matchQ     = !q    || name.includes(q) || kode.includes(q);
            const matchSt    = !st   || r.status === st;
            const matchBldg  = !bldg || b === bldg;
            return matchQ && matchSt && matchBldg;
        });
        renderRooms(filtered);
    }

    /* ── Load rooms ────────────────────────────────────── */
    async function loadRooms() {
        try {
            const res  = await apiFetch('/ruangan');
            if (!res.ok) throw new Error();
            const data = await res.json();
            allRooms   = Array.isArray(data) ? data : (data.data || []);
            updateStats(allRooms);
            populateBuildingFilter(allRooms);
            filterAndRender();
        } catch (e) {
            document.getElementById('rooms-grid').innerHTML =
                '<div class="col-span-3 text-center py-16 text-slate-500">Failed to load room data.</div>';
        }
    }

    /* ── Load active bookings (for cancel feature) ─────── */
    async function loadActiveBookings() {
        try {
            const res  = await apiFetch('/peminjaman?status=disetujui&per_page=100');
            if (!res.ok) return;
            const data = await res.json();
            activeBookings = data.data || [];
        } catch (e) { activeBookings = []; }
    }

    /* ── Admin cancel booking ──────────────────────────── */
    window.adminCancelBooking = async function(id, roomName, userName) {
        const ok = await vsAlert.confirm(
            'Cancel Booking?',
            `Are you sure you want to cancel the booking for <strong>${roomName}</strong> by <strong>${userName}</strong>?<br><span class="text-yellow-400 text-[12px]">⚠ This action cannot be undone. Admin can only cancel bookings up to H-1 before the event.</span>`,
            'Yes, Cancel Booking',
            'No, Keep It'
        );
        if (!ok) return;
        try {
            const res  = await apiFetch(`/peminjaman/${id}/cancel`, { method: 'POST' });
            const data = await res.json();
            if (res.ok) {
                vsAlert.success('Booking Cancelled', 'The booking has been successfully cancelled.');
                await loadActiveBookings();
                await loadRooms();
            } else {
                vsAlert.error('Failed to Cancel', data.message || 'Something went wrong.');
            }
        } catch (e) {
            vsAlert.error('Connection Error', 'Could not connect to server.');
        }
    };

    /* ── Import schedule (file picker) ─────────────────── */
    window.triggerImport = function() {
        document.getElementById('import-file-input').value = '';
        document.getElementById('import-file-input').click();
    };

    window.handleImportFile = async function(input) {
        const file = input.files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();
        if (!['xlsx', 'xls', 'csv', 'db'].includes(ext)) {
            vsAlert.error('Invalid File', 'Only .xlsx, .xls, .csv, and .db files are allowed.');
            return;
        }

        const btn = document.getElementById('import-schedule-btn');
        const origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Importing...`;

        try {
            const form = new FormData();
            form.append('file', file);

            // Use jadwal-listrik import endpoint
            const res  = await apiFetch('/jadwal-listrik/import', { method: 'POST', body: form });
            const data = await res.json();

            if (res.ok) {
                let msg = `Successfully imported <strong>${data.created ?? 0}</strong> schedule(s).`;
                if (data.errors && data.errors.length > 0) {
                    msg += `<br><span class="text-yellow-400 text-[12px]">⚠ ${data.errors.length} row(s) skipped.</span>`;
                }
                vsAlert.success('Import Successful', msg);
                loadRooms();
            } else {
                vsAlert.error('Import Failed', data.message || 'Could not process the file.');
            }
        } catch (e) {
            vsAlert.error('Connection Error', 'Could not connect to server.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
    };

    /* ── Event listeners ───────────────────────────────── */
    document.getElementById('room-search').addEventListener('input', filterAndRender);
    document.getElementById('status-filter').addEventListener('change', filterAndRender);
    document.getElementById('building-filter').addEventListener('change', filterAndRender);

    /* ── Init ──────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', async () => {
        await loadActiveBookings();
        await loadRooms();
    });
})();
</script>
@endpush
