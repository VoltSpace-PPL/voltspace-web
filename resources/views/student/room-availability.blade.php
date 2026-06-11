@extends('layouts.student')

@section('content')
{{-- Page Header --}}
<div class="flex items-start justify-between mb-8 flex-wrap gap-4">
    <div>
        <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">Room Availability</h1>
        <p class="text-slate-400 text-[14px] mt-2">Check room usage status and availability</p>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
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
        <p class="text-slate-400 text-[13px] leading-relaxed">Each room can only be booked by ONE person at a time. Once approved, the room will be exclusively yours during your booking period.</p>
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
            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" stroke-width="2"/>
        </svg>
        <select id="floor-filter"
            class="px-4 py-2.5 rounded-xl text-white text-[13px] focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/40 transition-all cursor-pointer"
            style="background:#161e2d; border:1px solid #1e2d45;">
            <option value="">All Floors</option>
            <option value="1">Floor 1</option>
            <option value="2">Floor 2</option>
            <option value="3">Floor 3</option>
        </select>
    </div>
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
    let activeBookings = [];



    const statusCfg = {
        tersedia:  { label: 'Available', cls: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30', dot: 'bg-emerald-400' },
        dipesan:   { label: 'Booked',    cls: 'bg-orange-500/15 text-orange-400 border-orange-500/30',   dot: 'bg-orange-400' },
        digunakan: { label: 'In Use',    cls: 'bg-red-500/15 text-red-400 border-red-500/30',            dot: 'bg-red-400' },
    };

    function statusInfo(s) {
        return statusCfg[s] || { label: s, cls: 'bg-slate-500/15 text-slate-400 border-slate-500/30', dot: 'bg-slate-400' };
    }

    function updateStats(rooms) {
        document.getElementById('stat-total').textContent     = rooms.length;
        document.getElementById('stat-available').textContent = rooms.filter(r => r.status === 'tersedia').length;
    }

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
            const s = statusInfo(r.status);
            const booking = activeBookings.find(b => b.ruangan_id === r.id && b.status === 'disetujui');

            return `
<div class="rounded-2xl overflow-hidden transition-all hover:scale-[1.01] hover:shadow-xl"
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
            ${s.label !== 'In Use' && s.label !== 'Booked' ? `
            <span class="flex-shrink-0 flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ${s.cls}">
                <span class="w-1.5 h-1.5 rounded-full ${s.dot}"></span>
                ${s.label}
            </span>
            ` : ''}
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
        </div>

    </div>

    <div class="px-5 pb-5 pt-1">
        <a href="/student/room-availability/${r.id}"
            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold text-[13px] hover:scale-[1.01] transition-all"
            style="background:linear-gradient(135deg,rgba(0,212,170,0.15),rgba(0,170,255,0.15)); color:#00d4aa; border:1px solid rgba(0,212,170,0.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            View Booking Calendar
        </a>
    </div>
</div>`;
        }).join('');
    }

    function filterAndRender() {
        const q    = document.getElementById('room-search').value.toLowerCase();
        const fl   = document.getElementById('floor-filter').value;
        const filtered = allRooms.filter(r => {
            if (r.status !== 'tersedia') return false;
            const name = (r.nama_ruangan || '').toLowerCase();
            const kode = (r.kode || r.id || '').toLowerCase();
            const floorLevel = String(r.lantai || 1);
            return (!q || name.includes(q) || kode.includes(q))
                && (!fl || floorLevel === fl);
        });
        renderRooms(filtered);
    }

    async function loadActiveBookings() {
        try {
            const res  = await apiFetch('/peminjaman?status=disetujui&per_page=100');
            if (!res.ok) return;
            const data = await res.json();
            activeBookings = data.data || [];
        } catch (e) { activeBookings = []; }
    }

    async function loadRooms() {
        try {
            const res  = await apiFetch('/ruangan');
            if (!res.ok) throw new Error();
            const data = await res.json();
            allRooms   = Array.isArray(data) ? data : (data.data || []);
            updateStats(allRooms);
            filterAndRender();
        } catch (e) {
            document.getElementById('rooms-grid').innerHTML =
                '<div class="col-span-3 text-center py-16 text-slate-500">Failed to load room data.</div>';
        }
    }

    document.getElementById('room-search').addEventListener('input', filterAndRender);
    document.getElementById('floor-filter').addEventListener('change', filterAndRender);

    document.addEventListener('DOMContentLoaded', async () => {
        await loadActiveBookings();
        await loadRooms();
    });


})();
</script>
@endpush
