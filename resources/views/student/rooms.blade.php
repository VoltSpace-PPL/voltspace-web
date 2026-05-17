@extends('layouts.student')

@section('content')
<div class="flex items-center justify-between mb-8 flex-wrap gap-4">
    <div>
        <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">Room Available</h1>
        <p class="text-slate-400 text-[14px] mt-2">Daftar ruangan yang tersedia untuk dipinjam</p>
    </div>
    <a href="/student/bookings/create" class="flex items-center gap-2 px-5 py-2.5 bg-[#00d4aa] hover:bg-[#00bfa0] text-[#0b1120] rounded-xl font-bold text-[14px] transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
        New Booking
    </a>
</div>

{{-- Filter Bar --}}
<div class="flex items-center gap-3 mb-6 flex-wrap">
    <div class="relative flex-1 min-w-[200px]">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
        <input id="room-search" type="text" placeholder="Cari nama ruangan..." class="w-full pl-10 pr-4 py-2.5 bg-[#1e293b] border border-[#334155] rounded-xl text-white text-[13px] placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
    </div>
    <select id="room-status-filter" class="px-4 py-2.5 bg-[#1e293b] border border-[#334155] rounded-xl text-white text-[13px] focus:outline-none focus:border-[#00d4aa] transition-colors cursor-pointer">
        <option value="">Semua Status</option>
        <option value="tersedia">Tersedia</option>
        <option value="digunakan">Sedang Digunakan</option>
        <option value="dipesan">Dipesan</option>
    </select>
</div>

{{-- Rooms Grid --}}
<div id="student-rooms-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <div class="col-span-3 flex flex-col items-center justify-center py-16 gap-3">
        <div class="w-8 h-8 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
        <span class="text-slate-500 text-[13px]">Memuat ruangan...</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    let allRooms = [];

    function statusInfo(status) {
        const map = {
            'tersedia':  { label: 'Tersedia',          cls: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20',   dot: 'bg-emerald-400' },
            'digunakan': { label: 'Sedang Digunakan',  cls: 'bg-orange-500/15 text-orange-400 border-orange-500/20',      dot: 'bg-orange-400' },
            'dipesan':   { label: 'Dipesan',            cls: 'bg-blue-500/15 text-blue-400 border-blue-500/20',            dot: 'bg-blue-400' },
        };
        return map[status] || { label: status, cls: 'bg-slate-500/15 text-slate-400 border-slate-500/20', dot: 'bg-slate-400' };
    }

    function renderRooms(rooms) {
        const grid = document.getElementById('student-rooms-grid');
        if (!rooms.length) {
            grid.innerHTML = `<div class="col-span-3 text-center py-16">
                <svg class="w-14 h-14 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                <p class="text-slate-500 text-[15px]">Tidak ada ruangan yang ditemukan.</p>
            </div>`;
            return;
        }

        grid.innerHTML = rooms.map(r => {
            const s = statusInfo(r.status);
            const isAvailable = r.status === 'tersedia';
            return `<div class="bg-[#1e293b] border border-[#334155] rounded-2xl p-5 transition-all hover:border-slate-500 group">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(0,170,255,0.12); border:1px solid rgba(0,170,255,0.2);">
                            <svg class="w-5 h-5 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-[15px] font-bold text-white leading-tight truncate">${r.nama_ruangan || 'Ruangan'}</h3>
                            <p class="text-[12px] text-slate-500 mt-0.5">${r.kode || r.id}</p>
                        </div>
                    </div>
                    <span class="flex-shrink-0 flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ${s.cls}">
                        <span class="w-1.5 h-1.5 rounded-full ${s.dot}"></span>
                        ${s.label}
                    </span>
                </div>

                <div class="flex items-center gap-4 py-3 px-4 bg-[#0f172a] rounded-xl mb-4 border border-[#1e293b]">
                    <div class="flex items-center gap-2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2"/></svg>
                        <span class="text-[13px]">Kapasitas</span>
                    </div>
                    <span class="text-white font-bold text-[15px] ml-auto">${r.kapasitas ?? 0} <span class="text-slate-500 text-[12px] font-normal">orang</span></span>
                </div>

                ${isAvailable
                    ? `<a href="/student/bookings/create?room_id=${r.id}" class="w-full flex items-center justify-center gap-2 py-3 bg-[#00d4aa] hover:bg-[#00bfa0] text-[#0b1120] rounded-xl font-bold text-[13px] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Pesan Ruangan
                       </a>`
                    : `<button disabled class="w-full flex items-center justify-center gap-2 py-3 bg-[#334155] text-slate-500 rounded-xl font-bold text-[13px] cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Tidak Tersedia
                       </button>`
                }
            </div>`;
        }).join('');
    }

    function filterRooms() {
        const q = document.getElementById('room-search').value.toLowerCase();
        const s = document.getElementById('room-status-filter').value;
        const filtered = allRooms.filter(r => {
            const matchName = (r.nama_ruangan || '').toLowerCase().includes(q) || (r.kode || '').toLowerCase().includes(q);
            const matchStatus = !s || r.status === s;
            return matchName && matchStatus;
        });
        renderRooms(filtered);
    }

    async function loadRooms() {
        try {
            const res = await apiFetch('/ruangan');
            if (!res.ok) throw new Error();
            const data = await res.json();
            allRooms = Array.isArray(data) ? data : (data.data || []);
            filterRooms();
        } catch(e) {
            document.getElementById('student-rooms-grid').innerHTML = `<div class="col-span-3 text-center py-16 text-slate-500">Gagal memuat data ruangan.</div>`;
        }
    }

    document.getElementById('room-search').addEventListener('input', filterRooms);
    document.getElementById('room-status-filter').addEventListener('change', filterRooms);

    document.addEventListener('DOMContentLoaded', loadRooms);
})();
</script>
@endpush
